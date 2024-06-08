<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: Home.php');
    exit;
}

// Create a textbox for the survey title
// Create a textbox for the survey description

// Create a button to create a question
// Upon creating a question, add a textbox for the question text

// Create a button to create choices for that question
// Upon creating a choice, add a textbox for the choice text

// At the bottom have a submit button that submits the survey to createSurveyHandler.php

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Survey</title>
    <link rel="stylesheet" href="css/admin-styles.css">
    <style>
        .question, .choice {
            margin-top: 10px;
        }
    </style>
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
        <h1>Create a New Survey</h1>
        <form id="surveyForm" action="createSurveyHandler.php" method="post">
            <label for="surveyTitle">Survey Title:</label>
            <input type="text" id="surveyTitle" name="survey_title" required>
            <br>
            <label for="surveyDescription">Survey Description:</label>
            <input type="text" id="surveyDescription" name="survey_description" required>
            <br>
            <div id="questionsContainer">
                <!-- Questions will be added here dynamically -->
            </div>
            <button type="button" id="addQuestionBtn">Add Question</button>
            <br><br>
            <button type="submit">Submit Survey</button>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                let questionCount = 0;

                document.getElementById('addQuestionBtn').addEventListener('click', () => {
                    questionCount++;
                    const questionDiv = document.createElement('div');
                    questionDiv.classList.add('question');
                    questionDiv.innerHTML = `
                        <label for="question${questionCount}">Question ${questionCount}:</label>
                        <input type="text" id="question${questionCount}" name="questions[${questionCount}][text]" required>
                        <button type="button" onclick="addChoice(${questionCount})">Add Choice</button>
                        <div id="choicesContainer${questionCount}" class="choicesContainer">
                            <!-- Choices will be added here dynamically -->
                        </div>
                    `;
                    document.getElementById('questionsContainer').appendChild(questionDiv);
                });
            });

            function addChoice(questionId) {
                const choicesContainer = document.getElementById(`choicesContainer${questionId}`);
                const choiceCount = choicesContainer.children.length + 1;
                const choiceDiv = document.createElement('div');
                choiceDiv.classList.add('choice');
                choiceDiv.innerHTML = `
                    <label for="choice${questionId}_${choiceCount}">Choice ${choiceCount}:</label>
                    <input type="text" id="choice${questionId}_${choiceCount}" name="questions[${questionId}][choices][]">
                `;
                choicesContainer.appendChild(choiceDiv);
            }
        </script>
    </div>
</body>
</html>

