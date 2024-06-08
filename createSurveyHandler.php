<?php
session_start();

include "engineDB.php";

if (!isset($_SESSION['user_email'])) {
    header('Location: Home.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the survey details
    $surveyTitle = $_POST['survey_title'];
    $surveyDescription = $_POST['survey_description'];
    $questions = $_POST['questions'];

    // Connect to the database
    $store_db = new EngineDB();
    $store_db->connect();

    // Insert the survey into the database
    $stmt = $store_db->getDb()->prepare("INSERT INTO entity_survey (name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $surveyTitle, $surveyDescription);
    $stmt->execute();
    $surveyId = $stmt->insert_id; // Get the last inserted id for the survey
    $stmt->close();

    // Insert the questions and choices into the database
    foreach ($questions as $question) {
        $questionText = $question['text'];
        $stmt = $store_db->getDb()->prepare("INSERT INTO entity_question (question) VALUES (?)");
        $stmt->bind_param("s", $questionText);
        $stmt->execute();
        $questionId = $stmt->insert_id; // Get the last inserted id for the question
        $stmt->close();

        // Insert the cross reference for the survey to the question
        $stmt = $store_db->getDb()->prepare("INSERT INTO xref_survey_question (id_survey, id_question) VALUES (?, ?)");
        $stmt->bind_param("ii", $surveyId, $questionId);
        $stmt->execute();
        $stmt->close();

        foreach ($question['choices'] as $choice) {
            $stmt = $store_db->getDb()->prepare("INSERT INTO entity_answer (answer) VALUES (?)");
            $stmt->bind_param("s", $choice);
            $stmt->execute();
            $choiceId = $stmt->insert_id; // Get the last inserted id for the choice
            $stmt->close();

            // Insert the cross reference for the question to the answer
            $stmt = $store_db->getDb()->prepare("INSERT INTO xref_question_answer (id_question, id_answer) VALUES (?, ?)");
            $stmt->bind_param("ii", $questionId, $choiceId);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Close the database connection
    $store_db->disconnect();

    // Redirect to a success page
    echo "Survey created successfully!";
    header('Location: createSurvey.php');
    exit;
} else {
    // Redirect back to the form if not a POST request
    header('Location: createSurvey.php');
    exit;
}
?>
