<!-- This will be the main page for the admin to view all the users and their information. -->
<?php
session_start();

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'view_users':
        header('Location: viewUsers.php');
        // Next to each user, add a button to view their survey and assign them a survey
        // (To assign them a survey, add an entry to the xref_survey_question_answer_user table with an empty answer)
        exit;
    case 'view_surveys':
        header('Location: viewSurveys.php');
        // In viewSurveys.php, add option at the very bottom to add a survey
        exit;
    case 'view_survey_results':
        header('Location: viewUserSurveyResults.php');
        // List the questions by survey and list the total number of responses for each question's answer
        // Maybe add some statistics
        exit;
    case 'view_questions':
        header('Location: viewQuestions.php');
        exit;
    case 'exit':
        session_destroy();
        header('Location: Home.php');
        exit;
    default:
        break;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
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
        <h1>Welcome to the Admin Dashboard</h1>
        <p>Select from options on left!</p>
    </div>    

</body>
</html>