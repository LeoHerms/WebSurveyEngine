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

// Retrieve all questions from the database
$query = "SELECT id_question, question FROM entity_question";
$result = $store_db->getDb()->query($query);
$questions = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
}

$answerQuery = "SELECT id_answer, answer FROM entity_answer";
$answerResult = $store_db->getDb()->query($answerQuery);
$answers = [];
if ($answerResult->num_rows > 0) {
    while($row = $answerResult->fetch_assoc()) {
        $answers[] = $row;
    }
}

$crossR = "SELECT id_question, id_answer FROM xref_question_answer";
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
    <title>View Questions</title>
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
        <h1>All Questions</h1>
        <?php if (empty($questions)): ?>
            <p>No questions found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Question</th>
                        <th>Choices</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($questions as $question): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($question['question']); ?></td>
                            <td>
                                <ul>
                                    <?php foreach ($crossRefs as $crossRef): ?>
                                        <?php if ($crossRef['id_question'] == $question['id_question']): ?>
                                            <?php foreach ($answers as $answer): ?>
                                                <?php if ($crossRef['id_answer'] == $answer['id_answer']): ?>
                                                    <li><?php echo htmlspecialchars($answer['answer']); ?></li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                        </tr> 
                    <?php endforeach; ?>
                <br><br>
                </tbody>
            </table>
        <?php endif; ?>
    </div>    

</body>
</html>