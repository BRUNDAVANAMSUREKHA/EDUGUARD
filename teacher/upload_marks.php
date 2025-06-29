<?php
session_start();
include('../config/database.php');

// Ensure database connection is established
if (!$conn) {
    die("❌ Error: Database connection failed.");
}

// Fetch all evaluated answers
$sql_evaluated = "SELECT ua.user_id, ua.exam_id, SUM(ua.score) AS total_score 
                  FROM user_answers ua 
                  WHERE ua.status = 'evaluated' 
                  GROUP BY ua.user_id, ua.exam_id";
$result_evaluated = $conn->query($sql_evaluated);

if ($result_evaluated->num_rows > 0) {
    while ($row = $result_evaluated->fetch_assoc()) {
        $student_id = $row['user_id'];
        $exam_id = $row['exam_id'];
        $total_score = $row['total_score'];

        // Insert or update the results table
        $stmt = $conn->prepare("INSERT INTO results (student_id, exam_id, score) VALUES (?, ?, ?) 
                                 ON DUPLICATE KEY UPDATE score = ?");
        $stmt->bind_param("iiii", $student_id, $exam_id, $total_score, $total_score);
        $stmt->execute();
    }
}

header('Location: results.php');
exit();
?>