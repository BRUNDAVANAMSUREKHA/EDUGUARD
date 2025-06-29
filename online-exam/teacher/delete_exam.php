<?php
session_start();
if ($_SESSION['role'] !== 'teacher') {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

include('../config/database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true); // Read JSON input
    $exam_id = intval($data['exam_id']);

    // Debugging: Check if exam_id is received correctly
    error_log("Deleting exam with ID: " . $exam_id);

    // ✅ Delete related questions first
    $stmt1 = $conn->prepare("DELETE FROM questions WHERE exam_id = ?");
    $stmt1->bind_param("i", $exam_id);
    if (!$stmt1->execute()) {
        error_log("Error deleting questions: " . $stmt1->error);
        echo json_encode(["success" => false, "message" => "Error deleting questions"]);
        exit();
    }

    // ✅ Delete related results
    $stmt2 = $conn->prepare("DELETE FROM results WHERE exam_id = ?");
    $stmt2->bind_param("i", $exam_id);
    if (!$stmt2->execute()) {
        error_log("Error deleting results: " . $stmt2->error);
        echo json_encode(["success" => false, "message" => "Error deleting results"]);
        exit();
    }

    // ✅ Now delete the exam itself
    $stmt3 = $conn->prepare("DELETE FROM exams WHERE id = ?");
    $stmt3->bind_param("i", $exam_id);
    if ($stmt3->execute()) {
        echo json_encode(["success" => true]);
    } else {
        error_log("Error deleting exam: " . $stmt3->error);
        echo json_encode(["success" => false, "message" => "Database error"]);
    }
    exit();
}
?>