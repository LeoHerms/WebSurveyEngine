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

// Retrieve all users from the database
$query = "SELECT id_user, email, is_admin FROM entity_user";
$result = $store_db->getDb()->query($query);

$users = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

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
        <h1>All Users</h1>
        <?php if (empty($users)): ?>
            <p>No users found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Admin</th> 
                        <th>Assign</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo $user['is_admin'] ? 'Yes' : 'No'; ?></td>
                            <td style="text-align: center;">
                                <?php if (!$user['is_admin']): ?>
                                    <form class="assignSurvey" action="assignSurvey.php" method="post">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id_user']; ?>">
                                        <button type="submit">Assign surveys</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <?php if (!$user['is_admin']): ?>
                                    <form class="editUser" action="editUser.php" method="post">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id_user']; ?>">
                                        <button type="submit">Edit User</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <?php if (!$user['is_admin']): ?>
                                    <form class="deleteUser" action="deleteUser.php" method="post">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id_user']; ?>">
                                        <button type="submit" onclick="return confirm('Are you sure you want to delete this user?')">Delete User</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr> 
                    <?php endforeach; ?>
                
                </tbody>
            </table>
        <?php endif; ?>
    </div>    

</body>
</html>