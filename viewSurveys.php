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
$query = "SELECT name, description FROM entity_survey";
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
    <title>View Surveys</title>
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
        <h1>All Surveys</h1>
        <?php if (empty($users)): ?>
            <p>No surveys found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['description']); ?></td>
                        </tr> 
                    <?php endforeach; ?>
                <br><br>
                </tbody>
            </table>
            <br>
            <form action="createSurvey.php" method="post">
                <input type="hidden" name="action" value="create_survey">
                <button type="submit">Create Survey</button>
            </form>
        <?php endif; ?>
    </div>    

</body>
</html>