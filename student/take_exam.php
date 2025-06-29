<?php
session_start();
include('../config/database.php');

if (!isset($_GET['exam_id'])) {
    header('Location: exams.php');
    exit();
}

$exam_id = $_GET['exam_id'];
$student_id = $_SESSION['user_id'];

// Check if the student has already taken the exam
$check_sql = "SELECT COUNT(*) AS count FROM exam_attempts WHERE student_id='$student_id' AND exam_id='$exam_id'";
$check_result = $conn->query($check_sql);
$check_row = $check_result->fetch_assoc();

if ($check_row['count'] > 0) {
    echo "<script>alert('You have already taken this exam.'); window.location.href='results.php';</script>";
    exit();
}

// Fetch exam questions
$sql = "SELECT * FROM questions WHERE exam_id='$exam_id'";
$result = $conn->query($sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST['answers'] as $question_id => $answer) {
        $answer = is_array($answer) ? json_encode($answer) : $answer;
        $sql = "INSERT INTO answers (student_id, question_id, answer) VALUES ('$student_id', '$question_id', '$answer')";
        $conn->query($sql);
    }
    
    // Mark exam as completed
    $conn->query("INSERT INTO exam_attempts (student_id, exam_id) VALUES ('$student_id', '$exam_id')");
    
    header('Location: results.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Take Exam</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <script src="../assets/js/examSecurity.js"></script>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; text-align: center; padding: 20px; }
        .exam-container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2); }
        .camera-container { margin-top: 20px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; cursor: pointer; margin-top: 20px; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>

    <div class="exam-container">
        <h1>Exam in Progress</h1>
        <div class="camera-container">
            <video id="camera" autoplay playsinline></video>
        </div>

        <form id="exam-form" method="post">
            <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="question">
                <p><?php echo $row['question']; ?></p>
                <input type="text" name="answers[<?php echo $row['id']; ?>]"><br>
            </div>
            <?php } ?>
            <button type="submit">Submit Exam</button>
        </form>
    </div>

    <script>
        // Check if full-screen should be enabled
        if (sessionStorage.getItem("startExam") === "true") {
            document.documentElement.requestFullscreen().catch(console.log);
            sessionStorage.removeItem("startExam");
        }

        // Auto-Submit if Exiting Full-Screen Mode
        document.addEventListener("fullscreenchange", function () {
            if (!document.fullscreenElement) {
                alert("Full-screen mode exited! Exam auto-submitted.");
                document.getElementById("exam-form").submit();
            }
        });

        // Prevent Back Button
        history.pushState(null, null, document.URL);
        window.addEventListener("popstate", function () {
            history.pushState(null, null, document.URL);
        });
    </script>

</body>
</html>
