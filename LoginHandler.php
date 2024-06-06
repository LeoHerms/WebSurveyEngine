<?php
session_start();

include "engineDB.php";

$store_db = new EngineDB();

// Sanitize for security
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);


// Check if user exists
$store_db->connect();
$userdata = $store_db->getUser($email);
$store_db->disconnect();


if (!($userdata["EMAIL"] == $email && password_verify($password, $userdata["PASSWORD"]))) {
    $_SESSION["login_error"] = "Invalid email or password. DEBUG: email: $email, password: $password, email in db: " . $userdata["EMAIL"] . ", password in db: " . $userdata["PASSWORD"];
    header("Location: Home.php");
    exit;
}

// If user is an admin, redirect to admin.php
if ($userdata["IS_ADMIN"] == 1) {
    $_SESSION["user_email"] = $email; // Store user email in session for further use
    header("Location: Admin.php");
    exit;
}

//redirect to user.php - admin display is managed there
$_SESSION["user_email"] = $email; // Store user email in session for further use
header("Location: User.php");

exit;
?>