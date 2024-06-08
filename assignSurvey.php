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

$userID = $_POST['user_id'];
// Find user email from the database query
$query = "SELECT email FROM entity_user WHERE id_user = $userID";
$result = $store_db->getDb()->query($query);
$thisUserEmail = $result->fetch_assoc()['email'];


// Retrieve all the surveys from the database
// These will be listed and will have a checkbox next to them to assign to the specific user
// Once the checkbox is checked, the survey will be assigned to the user
// My implementation will make the checkboxes permanent, so if the user has already taken the survey, the checkbox will be disabled
$query = "SELECT id_survey, name, description FROM entity_survey";
$result = $store_db->getDb()->query($query);
$surveys = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $surveys[] = $row;
    }
}

// Retrieve the cross reference table that maps surveys to questions to answers to users
// Using this table to check if the user has already taken the survey or has already been assigned the survey
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
    <title>Assign Surveys</title>
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
        <h1>Assign surveys to user : <?php echo htmlspecialchars($thisUserEmail); ?></h1>
        <p>Boxes will be checked if the user has already been assigned the survey.</p>
        <?php if (empty($surveys)): ?>
            <p>No surveys found.</p>
        <?php else: ?>
            <form action="assignSurveyHandler.php" method="post">
                <table>
                    <thead>
                        <tr>
                            <th>Survey Name</th>
                            <th>Survey Description</th>
                            <th>Assign</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($surveys as $survey): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($survey['name']); ?></td>
                                <td><?php echo htmlspecialchars($survey['description']); ?></td>
                                <td style="text-align: center;">
                                    <?php $surveyId = $survey['id_survey']; ?>
                                    <?php $assigned = false; ?>
                                    <?php foreach ($crossRefs as $crossRef): ?>
                                        <?php if ($crossRef['id_survey'] == $surveyId && $crossRef['id_user'] == $userID): ?>
                                            <?php $assigned = true; ?>
                                            <?php break; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <input type="hidden" name="survey_ids[]" value="<?php echo htmlspecialchars($surveyId); ?>">
                                    <input type="checkbox" name="assign_surveys[]" value="<?php echo htmlspecialchars($surveyId); ?>" <?php if ($assigned) echo 'checked disabled'; ?>>Assign this survey
                                </td>
                            </tr> 
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <br>
                <button type="submit">Assign Surveys!</button>
            </form>

        <?php endif; ?>
    </div>    

</body>
</html>