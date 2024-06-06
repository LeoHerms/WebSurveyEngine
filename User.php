<?php
session_start();

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
        <form id="userForm" method="post" target="iframe_content">
            <button type="button" onclick="submitForm('viewSurveys')">View Surveys</button>
            <button type="button" onclick="submitForm('viewAccount')">View Account</button>
            <button type="button" onclick="submitForm('Exit')">Exit</button>
        </form>
    </div>

    <div class="content">
        <h1>Welcome to the User Dashboard</h1>
        <p>Select from options on left!</p>
    </div>    

    <iframe name="iframe_content" frameborder="0"></iframe>

    <script>
        function submitForm(action) {
            if (action === 'Exit') {
                window.location.href = 'Home.php';
            } else {
                document.getElementById('userForm').action = action + '.php';
                document.getElementById('userForm').submit();
            }
        }
    </script>
</body>
</html>