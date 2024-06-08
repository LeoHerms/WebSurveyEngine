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
$user_id = $users['ID_USER'];


// Get stuff retrieved from POST
$survey_id = $_POST['survey_id'] ?? '';
// echo '<script>alert("' . $_POST['survey_id'] . '");</script>';

// Retrieve the specific survey from the database
$query = "SELECT id_survey, name, description FROM entity_survey WHERE id_survey = '$survey_id'";
$result = $store_db->getDb()->query($query);
$survey = $result->fetch_assoc();

// Retrieve the cross reference that maps surveys to questions
$crossS_R = "SELECT id_survey, id_question FROM xref_survey_question WHERE id_survey = '$survey_id'";
$crossRefResult = $store_db->getDb()->query($crossS_R);
$crossSurvey = [];
if ($crossRefResult->num_rows > 0) {
    while($row = $crossRefResult->fetch_assoc()) {
        $crossSurvey[] = $row;
    }
}

// Now based off the cross reference above, we can get the questions for that specific survey
$questions = [];
foreach ($crossSurvey as $cross) {
    $query = "SELECT id_question, question FROM entity_question WHERE id_question = '" . $cross['id_question'] . "'";
    $result = $store_db->getDb()->query($query);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $questions[] = $row;
        }
    }
}

// Retrieve the cross reference that maps questions to answers
$crossQ_R = "SELECT id_question, id_answer FROM xref_question_answer WHERE id_question IN (";
foreach ($questions as $question) {
    $crossQ_R .= "'" . $question['id_question'] . "', ";
}
$crossQ_R = rtrim($crossQ_R, ', ') . ")";
$crossRefResult = $store_db->getDb()->query($crossQ_R);
$crossQuestion = [];
if ($crossRefResult->num_rows > 0) {
    while($row = $crossRefResult->fetch_assoc()) {
        $crossQuestion[] = $row;
    }
}


// Now based off the cross reference above, we can get the answers for that specific question
$answers = [];
foreach ($crossQuestion as $cross) {
    $query = "SELECT id_answer, answer FROM entity_answer WHERE id_answer = '" . $cross['id_answer'] . "'";
    $result = $store_db->getDb()->query($query);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $answers[] = $row;
        }
    }
}

// Get the cross reference table that maps users to surveys to questions to answers (Necessary for if the user has already taken the survey)
$crossU = "SELECT id_survey, id_question, id_answer, id_user FROM xref_survey_question_answer_user WHERE id_survey = '$survey_id' AND id_user = '$user_id'";
$crossRefResult = $store_db->getDb()->query($crossU);
$crossUsers = [];
if ($crossRefResult->num_rows > 0) {
    while($row = $crossRefResult->fetch_assoc()) {
        $crossUsers[] = $row;
    }
}

// DEBUGGING
// // Initialize a string to hold the cross user data
// $crossUserString = '';

// foreach ($crossUsers as $cross) {
//     $crossUserString .= "id_survey: " . $cross['id_survey'] . "\\n";
//     $crossUserString .= "id_question: " . $cross['id_question'] . "\\n";
//     $crossUserString .= "id_answer: " . $cross['id_answer'] . "\\n";
//     $crossUserString .= "id_user: " . $cross['id_user'] . "\\n";
// }

// // Print the fields for cross user inside an alert
// echo '<script>alert("Test:' .  $crossUserString . '");</script>';


$store_db->disconnect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Taking Survey</title>
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
        <h1>Survey on <?php echo $survey['name']; ?></h1>
        <p><?php echo $survey['description']; ?></p>
        <form action="takeSurveyHandler.php" method="post">
            <input type="hidden" name="survey_id" value="<?php echo $survey_id; ?>">
            <input type="hidden" name="user_email" value="<?php echo $user_email; ?>">
            <?php foreach ($questions as $question): ?>
                <p><?php echo $question['question']; ?></p>
                <?php foreach ($answers as $answer): ?>
                    <?php
                        // Check if the user has already answered the question in the survey
                        $hasAnsweredQuestion = false;
                        foreach ($crossUsers as $cross) {
                            if ($cross['id_question'] == $question['id_question'] && $cross['id_answer'] == $answer['id_answer']) {
                                $hasAnsweredQuestion = true;
                                break;
                            }
                        }
                    ?>
                    <?php
                        $showAnswer = false;
                        foreach ($crossQuestion as $cross) {
                            if ($cross['id_question'] == $question['id_question'] && $cross['id_answer'] == $answer['id_answer']) {
                                $showAnswer = true;
                                break;
                            }
                        }
                    ?>
                    <?php if ($showAnswer): ?>
                        <input type="radio" id="<?php echo $answer['id_answer']; ?>" name="<?php echo $question['id_question']; ?>" value="<?php echo $answer['id_answer']; ?>" <?php if ($hasAnsweredQuestion) echo 'checked'; ?>>
                        <label for="<?php echo $answer['id_answer']; ?>"><?php echo $answer['answer']; ?></label><br>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
            <br>
            <input type="submit" value="Submit Survey">
        </form>
        
    </div>    

    

</body>
</html>