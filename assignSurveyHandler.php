<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: Home.php');
    exit;
}

include "engineDB.php";

$store_db = new EngineDB();
$store_db->connect();

$assignedUserID = 0;
if (isset($_SESSION['assignedUserID'])) {
    $assignedUserID = $_SESSION['assignedUserID'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if assign_surveys[] is set in the POST data
    if (isset($_POST['assign_surveys']) && is_array($_POST['assign_surveys'])) {
        // Retrieve the survey IDs from the POST data
        $assignedSurveys = $_POST['assign_surveys'];
        
        // Now $assignedSurveys contains an array of survey IDs to be assigned
        // You can perform further processing here, such as inserting these IDs into a database
        
        // Example: Loop through the array of assigned survey IDs
        foreach ($assignedSurveys as $surveyId) {
            // Insert the assignment into the database
            $query = "INSERT INTO xref_survey_question_answer_user (id_survey, id_question, id_answer, id_user) VALUES ($surveyId, NULL, NULL, $assignedUserID)";
            $store_db->getDb()->query($query);
        }
        
        // Redirect or display a success message
        // header("Location: success.php");
        // exit();
    } else {
        // If assign_surveys[] is not set or is not an array
        echo "Error: No surveys selected for assignment";
    }
} else {
    // If the form was not submitted via POST
    echo "Error: Form not submitted via POST method";
}


$store_db->disconnect();

header("Location: assignSurvey.php");
exit;

?>