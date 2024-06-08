<?php 
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: Home.php');
    exit;
}

include "engineDB.php";

$store_db = new EngineDB();
$store_db->connect();

// Get the database
$store_db->getDb();

// Delete by ID using the function
$store_db->deleteSurveyById($_POST['survey_id']);

$store_db->disconnect();

header('Location: viewSurveys.php');
exit;
?>

