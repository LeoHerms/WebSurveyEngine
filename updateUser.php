<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: Home.php');
    exit;
}

// Include the necessary database operations
include "engineDB.php";

$store_db = new EngineDB();
$store_db->connect();

// Get user from ID
$id_user = $_POST['user_id'];

// Hash the password
$password_hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

// Using this we can directly access the db and update the user's email and password
$query = "UPDATE entity_user SET email = ?, password = ? WHERE id_user = ?";
$stmt = $store_db->getDb()->prepare($query);
$stmt->bind_param("ssi", $_POST['new_email'], $password_hash, $id_user);
$stmt->execute();
$stmt->close();


$store_db->disconnect();


header('Location: editUser.php');

?>