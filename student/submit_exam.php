<?php
session_start();
include('../config/database.php');

// Ensure the user is a student
if ($_SESSION['role'] !== 'student') {
    header('Location: ../index.php');
    exit();
}

// Ensure exam_id is provided
if (!isset($_POST['exam_id']) || empty($_POST['exam_id'])) {
    die("❌ Error: Exam ID is missing!");
}

$exam_id = (int) $_POST['exam_id'];
$student_id = (int) $_SESSION['user_id'];
$total_score = 0;

// ✅ Check if student already submitted
$stmt_check = $conn->prepare("SELECT id FROM results WHERE user_id = ? AND exam_id = ?");
$stmt_check->bind_param("ii", $student_id, $exam_id);
$stmt_check->execute();
$stmt_check->store_result();
if ($stmt_check->num_rows > 0) {
    // Already submitted
    die("❌ You have already submitted this exam. Resubmission is not allowed.");
}

// Fetch correct answers for the exam
$stmt = $conn->prepare("SELECT id, question_type, correct_answer FROM questions WHERE exam_id = ?");
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $question_id = $row['id'];
    $question_type = $row['question_type'];
    $correct_answer = json_decode($row['correct_answer'], true);

    // Handle invalid or empty JSON data
    if ($correct_answer === null || !is_array($correct_answer)) {
        $correct_answer = [];
    }

    // Check if the question was answered
    if (!isset($_POST["question_$question_id"])) {
        continue;
    }

    $student_answer = $_POST["question_$question_id"];

    // Evaluate the answer
    $status = 'pending';
    $score_value = 0;

    if ($question_type === 'single_choice') {
        if (isset($correct_answer[0]) && $student_answer === $correct_answer[0]) {
            $score_value = 1;
        }
        $status = 'evaluated';
    } elseif ($question_type === 'multiple_choice') {
        if (is_array($student_answer)) {
            sort($student_answer);
            sort($correct_answer);
            if ($student_answer === $correct_answer) {
                $score_value = 2;
            }
            $student_answer = json_encode($student_answer);
        }
        $status = 'evaluated';
    } elseif (is_array($student_answer)) {
        $student_answer = json_encode($student_answer); // for long/short answer if it's array
    }

    $total_score += $score_value;

    // Save the student's answer
    $stmt_insert = $conn->prepare("INSERT INTO user_answers (user_id, exam_id, question_id, user_answer, status, score) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt_insert->bind_param("iiissi", $student_id, $exam_id, $question_id, $student_answer, $status, $score_value);
    $stmt_insert->execute();
}

// Save the total score in the results table
$status_results = 'pending';
$stmt_results = $conn->prepare("INSERT INTO results (user_id, exam_id, total_score, status) VALUES (?, ?, ?, ?)");
$stmt_results->bind_param("iiis", $student_id, $exam_id, $total_score, $status_results);
$stmt_results->execute();

// Redirect
header('Location: exam_done.php?exam_id=' . $exam_id);
exit();
?>
