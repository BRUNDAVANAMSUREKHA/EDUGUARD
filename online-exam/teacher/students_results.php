<?php
session_start();
include('../config/database.php');
include('partials/header.php');

$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

// Fetch result for the logged-in student
$sql_student_result = "SELECT r.user_id, u.name AS student_name, r.total_score 
                       FROM results r
                       JOIN users u ON r.user_id = u.id
                       WHERE r.user_id = ?";

$stmt = $conn->prepare($sql_student_result);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_student_result = $stmt->get_result();

if (!$result_student_result) {
    die("❌ SQL Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Results</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <h1>My Results</h1>

    <table border="1">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Total Score</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result_student_result->num_rows > 0) {
                while ($row = $result_student_result->fetch_assoc()) {
            ?>
            <tr>
                <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                <td><?php echo htmlspecialchars($row['total_score']); ?></td>
            </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='2'>No results found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>