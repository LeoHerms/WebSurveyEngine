<?php

class EngineDB {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "enginedb";
    private $db;

    function connect() {
        $this->db = new mysqli(
            $this->servername,
            $this->username,
            $this->password,
            $this->dbname
        );

        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    function disconnect() {
        if (!$this->db) {
            echo "No connection to close.";
            return;
        }
        if ($this->db->close()) {
            echo "Connection closed.";
        } else {
            echo "Error closing connection.";
        }
    }

    function getDB() {
        return $this->db;
    }

    function checkUserExists($email) {
        $sql = "SELECT * FROM entity_user WHERE email = '$email'";
        $result = $this->db->query($sql);
        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    function getUser($email) {
        $query = "SELECT `id_user` AS `ID_USER`, `email` AS `EMAIL`, `password` AS `PASSWORD`, `is_admin` AS `IS_ADMIN` FROM `entity_user` WHERE `email` = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    function addUser($email, $password, $isAdmin) {
        $sql = "INSERT INTO entity_user (email, password, is_admin) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        if ($stmt === false) {
            // Handle error
            return false;
        }

        // Bind the parameters
        $stmt->bind_param("ssi", $email, $password, $isAdmin);

        // Execute the statement
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    function updateUser($email, $password) {
        $sql = "UPDATE entity_user SET password = '$password' WHERE email = '$email'";
        if ($this->db->query($sql) === TRUE) {
            return true;
        } else {
            return false;
        }
    }

    function deleteUserById($user_id) {
        // Delete from the entity_user table
        $sql = "DELETE FROM entity_user WHERE id_user = $user_id";
        if ($this->db->query($sql) === TRUE) {
            // Now delete from the xref_survey_question_answer_user table
            $sql = "DELETE FROM xref_survey_question_answer_user WHERE id_user = $user_id";
            if ($this->db->query($sql) === TRUE) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function deleteUserByEmail($email) {
        $sql = "DELETE FROM entity_user WHERE email = '$email'";
        if ($this->db->query($sql) === TRUE) {
            return true;
        } else {
            return false;
        }
    }

    function getAllUsers() {
        $sql = "SELECT * FROM entity_users";
        $result = $this->db->query($sql);
        if ($result->num_rows > 0) {
            $users = array();
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            return $users;
        } else {
            return null;
        }
    }


    function deleteSurveyById($survey_id) {
        // Start a transaction to ensure atomicity
        $this->db->begin_transaction();
    
        try {
            // Delete entries from xref_survey_question_answer_user
            $sql = "DELETE FROM xref_survey_question_answer_user WHERE id_survey = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $survey_id);
            if (!$stmt->execute()) {
                throw new Exception("Failed to delete from xref_survey_question_answer_user");
            }
            $stmt->close();
    
            // Get all questions related to the survey
            $sql = "SELECT id_question FROM xref_survey_question WHERE id_survey = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $survey_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $questions = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
    
            // Prepare the statement for getting answers
            $sql = "SELECT id_answer FROM xref_question_answer WHERE id_question = ?";
            $stmt_get_answers = $this->db->prepare($sql);
    
            // Prepare the statement for deleting answers
            $sql_delete_answer = "DELETE FROM entity_answer WHERE id_answer = ?";
            $stmt_delete_answer = $this->db->prepare($sql_delete_answer);
    
            // Loop through each question
            foreach ($questions as $question) {
                $question_id = $question['id_question'];
    
                // Get all answers related to the question
                $stmt_get_answers->bind_param("i", $question_id);
                $stmt_get_answers->execute();
                $result = $stmt_get_answers->get_result();
                $answers = $result->fetch_all(MYSQLI_ASSOC);
    
                // Delete each answer
                foreach ($answers as $answer) {
                    $answer_id = $answer['id_answer'];
                    $stmt_delete_answer->bind_param("i", $answer_id);
                    if (!$stmt_delete_answer->execute()) {
                        throw new Exception("Failed to delete from entity_answer");
                    }
                }
            }
    
            // Close prepared statements for answers
            $stmt_get_answers->close();
            $stmt_delete_answer->close();
    
            // Delete cross references in the xref_question_answer table for these questions
            $sql = "DELETE FROM xref_question_answer WHERE id_question = ?";
            $stmt = $this->db->prepare($sql);
            foreach ($questions as $question) {
                $question_id = $question['id_question'];
                $stmt->bind_param("i", $question_id);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to delete from xref_question_answer");
                }
            }
            $stmt->close();
    
            // Delete the questions in the entity_question table
            $sql = "DELETE FROM entity_question WHERE id_question = ?";
            $stmt = $this->db->prepare($sql);
            foreach ($questions as $question) {
                $question_id = $question['id_question'];
                $stmt->bind_param("i", $question_id);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to delete from entity_question");
                }
            }
            $stmt->close();
    
            // Delete cross references in the xref_survey_question table
            $sql = "DELETE FROM xref_survey_question WHERE id_survey = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $survey_id);
            if (!$stmt->execute()) {
                throw new Exception("Failed to delete from xref_survey_question");
            }
            $stmt->close();
    
            // Finally, delete the survey
            $sql = "DELETE FROM entity_survey WHERE id_survey = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $survey_id);
            if (!$stmt->execute()) {
                throw new Exception("Failed to delete from entity_survey");
            }
            $stmt->close();
    
            // Commit the transaction
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            // Rollback the transaction if an error occurs
            $this->db->rollback();
            return false;
        }
    }
    

    function none($survey_id) {
        // Need to first delete all the answers for the survey
        // Also delete the cross references in the xref_question_answer table

        // Then delete the questions for those answers
        // Also delete the cross references in the xref_survey_question table

        // Then delete the survey
        // Then delete the entries in teh xref table for survey_question_answer_user

        // Delete entries from xref_survey_question_answer_user
        $sql = "DELETE FROM xref_survey_question_answer_user WHERE id_survey = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $survey_id);
        if (!$stmt->execute()) {
            $stmt->close();
            return false;
        }
        $stmt->close();

        // Get all questions related to the survey
        $sql = "SELECT id_question FROM xref_survey_question WHERE id_survey = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $survey_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $questions = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // Get all the answers related to each of the questions in the survey
        $sql = "SELECT id_answer FROM xref_question_answer WHERE id_question = ?";  
        foreach ($questions as $question) {
            $question_id = $question['id_question'];
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $question_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $answers = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            // Delete the answers
            $sql = "DELETE FROM entity_answer WHERE id_answer = ?";
            foreach ($answers as $answer) {
                $answer_id = $answer['id_answer'];
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param("i", $answer_id);
                if (!$stmt->execute()) {
                    $stmt->close();
                    return false;
                }
                $stmt->close();
            }
        }

        // Delete cross references in the xref_question_answer table for these questions
        foreach ($questions as $question) {
            $question_id = $question['id_question'];
            $sql = "DELETE FROM xref_question_answer WHERE id_question = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $question_id);
            if (!$stmt->execute()) {
                $stmt->close();
                return false;
            }
            $stmt->close();
        }

        // Delete the questions in the entity_question table
        $sql = "DELETE FROM entity_question WHERE id_question = ?";
        foreach ($questions as $question) {
            $question_id = $question['id_question'];
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $question_id);
            if (!$stmt->execute()) {
                $stmt->close();
                return false;
            }
            $stmt->close();
        }

        // Delete cross references in the xref_survey_question table
        $sql = "DELETE FROM xref_survey_question WHERE id_survey = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $survey_id);
        if (!$stmt->execute()) {
            $stmt->close();
            return false;
        }
        $stmt->close();


        // Finally, delete the survey
        $sql = "DELETE FROM entity_survey WHERE id_survey = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $survey_id);
        if (!$stmt->execute()) {
            $stmt->close();
            return false;
        }
        $stmt->close();

        return true;
    }
};
