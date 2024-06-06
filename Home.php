<!-- Home Page for website --> 

<?php
    session_start();

    if (isset($_SESSION["login_error"])) {
        echo '<script>alert("' . $_SESSION["login_error"] . '");</script>';
        unset($_SESSION["login_error"]); // Clear the error after displaying it
    }

    if (isset($_SESSION["register_success"])) {
        echo '<script>alert("' . $_SESSION["register_success"] . '");</script>';
        unset($_SESSION["register_success"]); // Clear the success message after displaying it
    }
?>

<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Survey Engine</title>
        <link rel="stylesheet" href="css/home-styles.css">
    
    </head>
    <body>
        <h1>Survey Engine</h1>
        <hr>
        <div class="container">   
            <!-- Login Form -->
            <!-- Need to determine when to send to admin or user page -->
            <!-- This will be determined via the database -->
            <form action="LoginHandler.php" method="post">
                <label for="email">E-mail:</label>
                <input type="email" name="email" id="email" required>
                <br>
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
                <br>
                <input type="submit" value="Login">
            </form>
            
            <!-- Register Form -->
            <p>Don't have an account? <a href="CreateAccount.php">Register here!</a></p>
        </div>

        

        <?php
            session_unset();
            session_destroy();
        ?>
    </body>
</html>
