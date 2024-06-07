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

// Retrieve user information from the database
$user_email = $_SESSION['user_email'];
echo '<script>alert("' . $_SESSION['user_email'] . '");</script>';
$users = $store_db->getUser($user_email);
echo '<script>alert("' . $users['EMAIL'] . $users['PASSWORD'] . '");</script>';

$store_db->disconnect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Account</title>
    <link rel="stylesheet" href="css/user-styles.css">
    
</head>
<body>
    <div class="sidenav">
        <form action="User.php" method="post">
            <button type="submit" name="action" value="view_surveys">View Surveys</button>
            <button type="submit" name="action" value="view_account">View Account</button>
            <button type="submit" name="action" value="exit">Exit</button>
        </form>
    </div>

    <div class="content">
        <h1>Your Account Information</h1>
        <table>
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Password</th> 
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo htmlspecialchars($users['EMAIL']); ?></td>
                    <td><?php echo htmlspecialchars($users['PASSWORD']); ?></td>
                </tr>
            <br><br>
            </tbody>
        </table>
    </div>    

</body>
</html>