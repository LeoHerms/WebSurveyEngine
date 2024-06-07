<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: Home.php');
    exit;
}

if (!isset($_SESSION['password'])) {
    header('Location: Home.php');
    exit;
}

// Include the necessary database operations
include "engineDB.php";

$store_db = new EngineDB();
$store_db->connect();

// Retrieve user information from the database
$user_email = $_SESSION['user_email'];
// echo '<script>alert("' . $_SESSION['user_email'] . '");</script>';
$users = $store_db->getUser($user_email);
// echo '<script>alert("' . $users['EMAIL'] . $users['PASSWORD'] . '");</script>';

$store_db->disconnect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Account</title>
    <link rel="stylesheet" href="css/user-styles.css">
    <script>
        function redirectToHome() {
            setTimeout(function() {
                window.location.href = 'Home.php';
            }, 3000); // 3000 milliseconds = 3 seconds
        }

        function showDeleteConfirmation() {
            document.getElementById('delete-confirmation').style.display = 'block';
        }
    </script>
</head>
<body>
    <div class="sidenav">
        <form action="User.php" method="post">
            <button type="submit" name="action" value="view_surveys">View Surveys</button>
            <button type="submit" name="action" value="view_account">View Account</button>
            <button type="submit" name="action" value="update_account">Update Account</button>
            <button type="submit" name="action" value="exit">Exit</button>
        </form>
    </div>

    <div class="content">
        <h1>Your Account Information</h1>
        <table>
            <thead>
                <tr>
                    <th>Current Email</th>
                    <th>Current Password</th> 
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo htmlspecialchars($users['EMAIL']); ?></td>
                    <td><?php echo htmlspecialchars($_SESSION['password']); ?></td>
                </tr>
            <br><br>
            </tbody>
        </table>
        <table>
            
            <br>
            <hr>
            <p>Update Account Information</p>
            <hr>
            <form action="updateAccountHandler.php" method="post">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($users['EMAIL']); ?>" required><br>
                <label for="password">Password (leave blank if not changing):</label>
                <input type="password" id="password" name="password"><br>
                <button type="submit" name="action" value="update">Update</button>
            </form>
            <br>
            <hr>
            <button onclick="showDeleteConfirmation()">Delete Account</button>
            <hr>
            <div id="delete-confirmation" style="display:none;">
                <form action="updateAccountHandler.php" method="post">
                    <br>
                    <label for="confirm_password">Confirm Password to Delete:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required><br>
                    <button type="submit" name="action" value="delete">Confirm Delete</button>
                </form>
            </div>


            <span id="error"><?php echo isset($_SESSION['update_error']) ? $_SESSION['update_error'] : ""; ?></span>
            <span id="success"><?php echo isset($_SESSION['update_success']) ? $_SESSION['update_success'] : ""; ?></span>
        

            <div id="updated">
                <?php
                if (isset($_SESSION['update_success'])) {
                    echo $_SESSION['update_success'] . "<br> <br>";
                    unset($_SESSION['update_success']);
                }
                ?>
            </div>
            <div id="deleted">
                <?php
                if (isset($_SESSION['delete_success'])) {
                    echo $_SESSION['delete_success'] . "<br> <br>";
                    echo "<script>redirectToHome();</script>";
                    unset($_SESSION['delete_success']);
                }
                ?>
            </div>
        </table>
    </div>    

</body>
</html>