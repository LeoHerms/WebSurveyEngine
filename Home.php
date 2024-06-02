<?php
    session_start();
?>

<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Survey Dawg</title>
        <link rel="stylesheet" href="css/home-styles.css">
    
    </head>
    <body>
        <h1>Survey Dawg<img src="images/paw.png" alt="Paw print" class="paw-print"></h1>
        <hr>
        <div class="container">   
            <!-- Login Form -->
            <form action="login.php" method="post">
                <label for="username">E-mail:</label>
                <input type="text" name="username" id="username" required>
                <br>
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
                <br>
                <input type="submit" value="Login">
            </form>
            
            <!-- Register Form -->
            <p>Don't have an account? <a href="CreateAccount.php">Register here!</a></p>
        </div>

        <div class="gif-container">
            <img src="images/dawg.gif" alt="Moving Dawg" class="moving-dawg">
        </div>

        <?php
            session_unset();
            session_destroy();
        ?>
    </body>
</html>
