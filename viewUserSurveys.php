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
$users = $store_db->getUser($user_email);

// Retrieve all the surveys from the database
$query = "SELECT id_survey, name, description FROM entity_survey";
$result = $store_db->getDb()->query($query);
$surveys = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $surveys[] = $row;
    }
}

// Get the cross reference table that maps users to surveys to questions to answers
$crossR = "SELECT id_survey, id_question, id_answer, id_user FROM xref_survey_question_answer_user";
$crossRefResult = $store_db->getDb()->query($crossR);
$crossRefs = [];
if ($crossRefResult->num_rows > 0) {
    while($row = $crossRefResult->fetch_assoc()) {
        $crossRefs[] = $row;
    }
}

$store_db->disconnect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Surveys</title>
    <link rel="stylesheet" href="css/user-styles.css">
    
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
                    <?php foreach ($surveys as $survey): ?>
                        <?php foreach ($crossRefs as $crossRef): ?>
                            <?php if ($crossRef['id_user'] == $users['ID_USER'] && $crossRef['id_survey'] == $survey['id_survey']): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($survey['name']); ?></td>
                                    <td><?php echo htmlspecialchars($survey['description']); ?></td>
                                    <td>
                                        <form class="takeSurvey" action="takeSurvey.php" method="post">
                                            <input type="hidden" name="survey_id" value="<?php echo $survey['id_survey']; ?>">
                                            <button type="submit">Take Survey</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php break; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <br><br>
                </tbody>
            </table>
        <?php endif; ?>
    </div>    

</body>
</html>