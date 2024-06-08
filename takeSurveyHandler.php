<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: Home.php');
    exit;
}

include "engineDB.php";

$store_db = new EngineDB();
$store_db->connect();

// We want to store whatever the user selected from the survey
// We will either be inserting or modifying the already exising entries
// The only table we will be modifying is the xref_survey_question_answer_user table

// Get the user id
$user_email = $_SESSION['user_email'];
$users = $store_db->getUser($user_email);
$user_id = $users['ID_USER'];

// Get the survey id
$survey_id = $_POST['survey_id'] ?? '';

// If the survey id is not set, then that means that we need to insert
// If the survey id is set, then that means that we need to update

// Access the table to see
$crossR = "SELECT id_survey, id_question, id_answer, id_user FROM xref_survey_question_answer_user WHERE id_survey = '$survey_id' AND id_user = '$user_id'";
$crossRefResult = $store_db->getDb()->query($crossR);
$crossRefs = [];
$flag = false;
if ($crossRefResult->num_rows > 0) {
    while($row = $crossRefResult->fetch_assoc()) {
        if (empty($row['id_answer'])) { // Essentially if there is only one entry in the table and the answer is null, then the survey hasnt been taking but the user has access to it
            // Set a flag to indicate that we need to insert
            $flag = true;
        }
        $crossRefs[] = $row;
    }
}

// Verify that the user has not already taken the survey
if ($flag) {
    // Insert
    $query = "SELECT id_question FROM xref_survey_question WHERE id_survey = '$survey_id'";
    $result = $store_db->getDb()->query($query);
    $questions = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $questions[] = $row;
        }
    }

    $counter = 0;
    // Insert the questions and answers into the xref_survey_question_answer_user table
    foreach ($questions as $question) {
        // Do one update for the original entry that had an empty answer
        if ($counter == 0) {
            $answer = $_POST[$question['id_question']] ?? '';
            $query = "UPDATE xref_survey_question_answer_user SET id_answer = '$answer' WHERE id_survey = '$survey_id' AND id_question = '" . $question['id_question'] . "' AND id_user = '$user_id'";
            $store_db->getDb()->query($query);
            $counter++;
        }
        else {
            $answer = $_POST[$question['id_question']] ?? '';
            $query = "INSERT INTO xref_survey_question_answer_user (id_survey, id_question, id_answer, id_user) VALUES ('$survey_id', '" . $question['id_question'] . "', '$answer', '$user_id')";
            $store_db->getDb()->query($query);
        }

    }
} else {
    // Update
    $query = "SELECT id_question FROM xref_survey_question WHERE id_survey = '$survey_id'";
    $result = $store_db->getDb()->query($query);
    $questions = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $questions[] = $row;
        }
    }

    // Update the questions and answers into the xref_survey_question_answer_user table
    foreach ($questions as $question) {
        $answer = $_POST[$question['id_question']] ?? '';
        $query = "UPDATE xref_survey_question_answer_user SET id_answer = '$answer' WHERE id_survey = '$survey_id' AND id_question = '" . $question['id_question'] . "' AND id_user = '$user_id'";
        $store_db->getDb()->query($query);
    }
}




$store_db->disconnect();

header("Location: viewUserSurveys.php");
exit;
?>