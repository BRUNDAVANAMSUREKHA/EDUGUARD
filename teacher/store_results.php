<?php
session_start();
include('../config/database.php');

// Fetch all evaluated answers and calculate total score
$sql_total_score = "SELECT ua.user_id, u.name AS student_name, SUM(ua.score) AS total_score 
                    FROM user_answers ua
                    JOIN users u ON ua.user_id = u.id
                    WHERE ua.status = 'evaluated'
                    GROUP BY ua.user_id";

$result_total_score = $conn->query($sql_total_score);

if ($result_total_score->num_rows > 0) {
    while ($row = $result_total_score->fetch_assoc()) {
        $user_id = $row['user_id'];
        $total_score = $row['total_score'];

        // Store results in the results table
        $sql_insert_result = "INSERT INTO results (user_id, total_score) VALUES (?, ?)";
        $stmt = $conn->prepare($sql_insert_result);
        $stmt->bind_param("ii", $user_id, $total_score);
        $stmt->execute();
    }
}