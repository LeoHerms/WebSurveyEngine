<?php 
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: Home.php');
    exit;
}

include "engineDB.php";

$store_db = new EngineDB();
$store_db->connect();

$userID = $_POST['user_id'] ?? 0;

// Get the database
$store_db->getDb();
// Delete by ID using the function
$store_db->deleteUserById($userID);

$store_db->disconnect();

header('Location: viewUsers.php');
exit;
?>

