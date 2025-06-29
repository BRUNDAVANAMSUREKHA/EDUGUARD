<?php
session_start();
include('../config/database.php');

// Calculate scores for single and multiple choice questions
$sql_choice_questions = "SELECT ua.id AS answer_id, ua.user_id, ua.question_id, ua.user_answer, q.correct_answer, q.question_type 
                         FROM user_answers ua
                         JOIN questions q ON ua.question_id = q.id
                         WHERE q.question_type IN ('single_choice', 'multiple_choice') AND ua.status = 'pending'";

$result_choice_questions = $conn->query($sql_choice_questions);

if ($result_choice_questions->num_rows > 0) {
    while ($row = $result_choice_questions->fetch_assoc()) {
        $user_answer = $row['user_answer'];
        $correct_answer = $row['correct_answer'];
        $score = ($user_answer == $correct_answer) ? 10 : 0;

        $sql_update = "UPDATE user_answers SET score = ?, status = 'evaluated' WHERE id = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("ii", $score, $row['answer_id']);
        $stmt->execute();
    }
}