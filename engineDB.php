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
};
