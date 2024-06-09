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
$id_user = isset($_POST['user_id']) ? $_POST['user_id'] : $_SESSION['editedUserID'];

$query = "SELECT email, password FROM entity_user WHERE id_user = ?";
$stmt = $store_db->getDb()->prepare($query);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$_SESSION['editedUserID'] = $id_user;


$store_db->disconnect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Users</title>
    <link rel="stylesheet" href="css/admin-styles.css">
    
</head>
<body>
    <div class="sidenav">
        <form action="Admin.php" method="post">
            <button type="submit" name="action" value="view_users">View Users</button>
            <button type="submit" name="action" value="view_surveys">View Surveys</button>
            <button type="submit" name="action" value="view_survey_results">View User-Survey Results</button>
            <button type="submit" name="action" value="view_questions">View Questions</button>
            <button type="submit" name="action" value="exit">Exit</button>
        </form>
    </div>

    <div class="content">
        <h1>Edit user : <?php echo $user['email']; ?></h1>
        <?php if (empty($user)): ?>
            <p>No users found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Account details</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $user['email']; ?>
                    </tr>
                    <tr>
                        <td style="text-align: center;">
                            <form class="updateUser" action="updateUser.php" method="post">
                                Fill both fields:
                                <input type="hidden" name="user_id" value="<?php echo $id_user; ?>">
                                <input type="email" name="new_email" placeholder="Provide Email" required>
                                <input type="password" name="new_password" placeholder="Provide Password" required>
                                <button type="submit">Save</button>
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
    </div>    

</body>
</html>