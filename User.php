<?php
session_start();

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'view_surveys':
        header('Location: viewUserSurveys.php');
        exit;
    case 'view_account':
        header('Location: viewAccount.php');
        exit;
    case 'exit':
        session_destroy();
        header('Location: Home.php');
        exit;
    default:
        break;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
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
        <h1>Welcome to the User Dashboard</h1>
        <p>Select from options on left!</p>
    </div>    

</body>
</html>