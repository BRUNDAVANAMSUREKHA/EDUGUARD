<?php
session_start();

// Ensure the user is a faculty member
if ($_SESSION['role'] !== 'teacher') {
    header('Location: ../index.php');
    exit();
}

// Include database configuration
include('../config/database.php');
include('partials/header.php');

// Function to auto-correct answers
function autoCorrectAnswers($conn, $exam_id) {
    $sql = "
        SELECT user_answers.id, user_answers.user_id, user_answers.exam_id, user_answers.answer, questions.correct_answer 
        FROM user_answers 
        JOIN questions ON user_answers.question_id = questions.id 
        WHERE user_answers.status = 'pending' 
          AND (questions.question_type = 'multiple_choice' OR questions.question_type = 'single_choice')
          AND user_answers.exam_id = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $exam_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        die("Error fetching answers: " . $conn->error);
    }

    while ($row = $result->fetch_assoc()) {
        $answer_id = $row['id'];
        $student_answer = json_decode($row['answer'], true);
        $correct_answer = json_decode($row['correct_answer'], true);
        $is_correct = ($student_answer === $correct_answer) ? 'correct' : 'incorrect';

        $update_sql = "UPDATE user_answers SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param('si', $is_correct, $answer_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Function to update scores
function updateScores($conn, $exam_id) {
    $score_sql = "
        SELECT user_id, exam_id, SUM(score) AS total_score 
        FROM user_answers 
        WHERE exam_id = ?
        GROUP BY user_id, exam_id
    ";
    $stmt = $conn->prepare($score_sql);
    $stmt->bind_param('i', $exam_id);
    $stmt->execute();
    $score_result = $stmt->get_result();

    if (!$score_result) {
        die("Error calculating scores: " . $conn->error);
    }

    while ($score_row = $score_result->fetch_assoc()) {
        $user_id = $score_row['user_id'];
        $exam_id = $score_row['exam_id'];
        $total_score = $score_row['total_score'];

        $check_sql = "SELECT id FROM results WHERE user_id = ? AND exam_id = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param('ii', $user_id, $exam_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $update_sql = "UPDATE results SET total_score = ? WHERE user_id = ? AND exam_id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param('iii', $total_score, $user_id, $exam_id);
        } else {
            $update_sql = "INSERT INTO results (user_id, exam_id, total_score) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param('iii', $user_id, $exam_id, $total_score);
        }
        $stmt->execute();
        $stmt->close();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_results'])) {
    $exam_id = $_POST['exam_id'];
    $conn->begin_transaction();

    try {
        autoCorrectAnswers($conn, $exam_id);
        updateScores($conn, $exam_id);
        $conn->commit();
        $success_message = "Results verified and scores updated successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        $error_message = "Error: " . $e->getMessage();
    }
}

// Fetch all exams for the dropdown
$exams_sql = "SELECT id, exam_name FROM exams";
$exams_result = $conn->query($exams_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Results</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        /* Centering container */
        body {
    font-family: 'Roboto', sans-serif;
    background: linear-gradient(135deg,rgb(246, 246, 247),rgb(245, 245, 245));
    margin: 0;
    padding: 20px;
    color: #333;
    transition: background-color 0.3s ease, color 0.3s ease;
    display: flex;
}

header {
    position: fixed;
    left: 0;
    top: 0;
    height: 100vh;
    width: 250px;
    background-color: #333; /* Adjust based on your theme */
    color: white;
    padding: 20px;
}

.main-content {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 90vh;
    margin-left: 200px; /* Offset for fixed sidebar */
}

.container {
    background: #fff;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
    max-width: 500px;
    width: 100%;
}


        h1 {
            color: #333;
            margin-bottom: 1.5rem;
            font-size: 1.75rem;
        }

        .message {
            margin-bottom: 1.5rem;
            padding: 1rem;
            border-radius: 5px;
            font-size: 1rem;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        select, button {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        select:focus, button:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        a button {
            background-color: #28a745;
        }

        a button:hover {
            background-color: #218838;
        }

        /* Responsive Adjustments */
        @media (max-width: 600px) {
            .container {
                padding: 1.5rem;
            }
            h1 {
                font-size: 1.5rem;
            }
            .message {
                font-size: 0.9rem;
            }
            select, button {
                font-size: 0.9rem;
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body>
<div class="main-content">
    <div class="container">
        <h1>Verify Results</h1>
        <?php if (isset($success_message)): ?>
            <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php elseif (isset($error_message)): ?>
            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <select name="exam_id" required>
                <option value="">Select Exam</option>
                <?php while ($exam = $exams_result->fetch_assoc()): ?>
                    <option value="<?php echo $exam['id']; ?>"><?php echo htmlspecialchars($exam['exam_name']); ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit" name="verify_results">Verify Results</button>
        </form>
        <a href="results.php"><button>View Results</button></a>
    </div>
</div>

</body>
</html>
