<!-- This will be the main page for the admin to view all the users and their information. -->
<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin-styles.css">
</head>
<body>
    
    <div class="sidenav">
        <form id="adminForm" method="post" target="iframe_content">
            <button type="button" onclick="submitForm('viewUsers')">View Users</button>
            <button type="button" onclick="submitForm('viewSurveys')">View Surveys</button>
            <button type="button" onclick="submitForm('viewResults')">View User-Survey Results</button>
            <button type="button" onclick="submitForm('viewQuestions')">View Questions</button>
            <button type="button" onclick="submitForm('Exit')">Exit</button>
        </form>
    </div>

    <div class="content">
        <h1>Welcome to the Admin Dashboard</h1>
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