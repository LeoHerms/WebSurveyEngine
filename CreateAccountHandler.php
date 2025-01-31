<?php
session_start();

include "engineDB.php";

$store_db = new EngineDB();

// Sanitize for security
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION["register_error"] = "Invalid email format";
    header("Location: CreateAccount.php");
    exit;
}

$passwordpattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{7,}$/';

if (!preg_match($passwordpattern, $password)) {
    $_SESSION["register_error"] = "Password must contain at least one lowercase letter, one uppercase letter, and one number.";
    header("Location: CreateAccount.php");
    exit;
}

// Check if user is already in the database
$store_db->connect();
$exists = $store_db->checkUserExists($email);
$store_db->disconnect();
if ($exists) {
    $_SESSION["register_error"] = "A user with that e-mail already exists";
    header("Location: CreateAccount.php");
    exit;
}
// Since email and password are valid and user doesn't exist, set default values
$isAdmin = false; // By default, users are not admins

// Store user data
$password_hashed = password_hash($password, PASSWORD_DEFAULT);  // Hash password for security
$store_db->connect();
$result = $store_db->addUser($email, $password_hashed, $isAdmin);
$store_db->disconnect();

if (!$result) {
    $_SESSION["register_error"] = "Failed to register user. Please try again.";
    header("Location: CreateAccount.php");
    exit;
}

// Clear error messages upon successful registration
unset($_SESSION['register_error']);

$_SESSION["register_success"] = "Registration successful! Please log in.";
header("Location: CreateAccount.php");
exit;
?>