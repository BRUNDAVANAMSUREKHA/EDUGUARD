<?php
session_start();
include '../config/database.php';

// Ensure user is logged in as a student
if ($_SESSION['role'] !== 'student') {
    die("❌ You must be logged in to view results.");
}

$exam_id = $_GET['exam_id'];
$total_score = $_GET['score'];

// Fetch exam details
$stmt = $conn->prepare("SELECT * FROM exams WHERE id = ?");
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$exam = $stmt->get_result()->fetch_assoc();

// Fetch the student's short/long answer evaluations
$stmt_answers = $conn->prepare("SELECT ua.question_id, ua.user_answer, ua.score, ua.feedback 
                                FROM user_answers ua
                                JOIN questions q ON ua.question_id = q.id
                                WHERE ua.user_id = ? AND ua.exam_id = ? AND ua.status = 'evaluated'");
$stmt_answers->bind_param("ii", $_SESSION['user_id'], $exam_id);
$stmt_answers->execute();
$answers_result = $stmt_answers->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exam Results</title>
</head>
<body>
    <h1>Exam: <?php echo htmlspecialchars($exam['exam_name']); ?></h1>
    <h2>Your Score: <?php echo $total_score; ?> / <?php echo $exam['total_score']; ?></h2>

    <h3>Short/Long Answer Evaluation:</h3>
    <table>
        <thead>
            <tr>
                <th>Question</th>
                <th>Your Answer</th>
                <th>Score</th>
                <th>Feedback</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $answers_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['question']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_answer']); ?></td>
                    <td><?php echo htmlspecialchars($row['score']); ?></td>
                    <td><?php echo htmlspecialchars($row['feedback']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
