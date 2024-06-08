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

// Retrieve all surveys from the database
$query = "SELECT id_survey, name, description FROM entity_survey";
$result = $store_db->getDb()->query($query);

$surveys = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $surveys[] = $row;
    }
}

// Get the cross reference table for the survey and questions
$crossR = "SELECT id_survey, id_question FROM xref_survey_question";
$crossRefResult = $store_db->getDb()->query($crossR);
$crossQ_S = [];
if ($crossRefResult->num_rows > 0) {
    while($row = $crossRefResult->fetch_assoc()) {
        $crossQ_S[] = $row;
    }
}

// Part for viewing questions and answers (down below)

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
$crossQ_A = [];
if ($crossRefResult->num_rows > 0) {
    while($row = $crossRefResult->fetch_assoc()) {
        $crossQ_A[] = $row;
    }
}

// Gather the entries from the xref_survey_question_answer_user table
$crossR = "SELECT id_survey, id_question, id_answer FROM xref_survey_question_answer_user";
$crossRefResult = $store_db->getDb()->query($crossR);
$crossS_Q_A_U = [];
if ($crossRefResult->num_rows > 0) {
    while($row = $crossRefResult->fetch_assoc()) {
        $crossS_Q_A_U[] = $row;
    }
}


$store_db->disconnect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Results</title>
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
    <h1>User-Survey Results</h1>
    <?php if (empty($surveys)): ?>
        <p>No surveys found</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Survey Title</th>
                    <th>Question</th>
                    <th>Answers</th> 
                </tr>
            </thead>
            <tbody>
                <?php foreach ($surveys as $survey): ?>
                    <?php
                    // Fetch questions for the current survey
                    $surveyQuestions = array_filter($questions, function($question) use ($crossQ_S, $survey) {
                        foreach ($crossQ_S as $crossQ) {
                            if ($crossQ['id_survey'] == $survey['id_survey'] && $crossQ['id_question'] == $question['id_question']) {
                                return true;
                            }
                        }
                        return false;
                    });
                    ?>

                    <?php
                    // Fetch survey responses for the current survey
                    $surveyResponses = array_filter($crossS_Q_A_U, function($response) use ($survey) {
                        return $response['id_survey'] == $survey['id_survey'];
                    });
                    ?>

                    <?php if (!empty($surveyQuestions)): ?>
                        <tr>
                            <td rowspan="<?php echo count($surveyQuestions); ?>">
                                <?php echo htmlspecialchars($survey['name']); ?><br>
                                <?php echo htmlspecialchars($survey['description']); ?>
                            </td>

                            <?php $firstQuestion = true; ?>
                            <?php foreach ($surveyQuestions as $question): ?>
                                <?php if (!$firstQuestion): ?>
                                    <tr>
                                <?php endif; ?>
                                <td><?php echo htmlspecialchars($question['question']); ?></td>
                                <td>
                                    <ul>
                                        <?php foreach ($crossQ_A as $crossRef): ?>
                                            <?php if ($crossRef['id_question'] == $question['id_question']): ?>
                                                <?php foreach ($answers as $answer): ?>
                                                    <?php if ($crossRef['id_answer'] == $answer['id_answer']): ?>
                                                        <?php // Count the number of responses for this answer ?>
                                                        <?php $reponseCount = 0; ?>
                                                        <?php $responseCount = count(array_filter($surveyResponses, function($response) use ($question, $answer) {
                                                            return $response['id_question'] == $question['id_question'] && $response['id_answer'] == $answer['id_answer'];
                                                        })); ?>
                                                        <li><?php echo htmlspecialchars($answer['answer']) . ' (Count: ' . $responseCount . ')'; ?></li>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </td>
                                <?php if ($firstQuestion): ?>
                                    <?php $firstQuestion = false; ?>
                                <?php else: ?>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
    

</body>
</html>