<?php
session_start();
include('../config/database.php');
include('partials/header.php');

// Ensure database connection is established
if (!$conn) {
    die("❌ Error: Database connection failed.");
}

// Fetch only short and long answer questions for evaluation
$sql_answers = "
    SELECT DISTINCT ua.id AS answer_id, u.name AS student_name, q.question, ua.user_answer, ua.score, ua.feedback, ua.status, q.question_type 
    FROM user_answers ua
    JOIN questions q ON ua.question_id = q.id
    JOIN users u ON ua.user_id = u.id  
    WHERE u.role = 'student' 
      AND ua.status = 'pending' 
      AND q.question_type IN ('short_answer', 'long_answer')
";

$result_answers = $conn->query($sql_answers);

if (!$result_answers) {
    die("❌ SQL Error: " . $conn->error);
}

// Handle evaluation action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer_id'])) {
    $answer_id = $_POST['answer_id'];
    $evaluation = $_POST['evaluation'];
    
    // Assign scores based on selection
    $score = ($evaluation === 'correct') ? 10 : 0;
    $feedback = ($evaluation === 'correct') ? 'Good Answer' : 'Incorrect Answer';

    $sql_update = "UPDATE user_answers SET score = ?, feedback = ?, status = 'evaluated' WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("isi", $score, $feedback, $answer_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "✅ Answer evaluated successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "❌ Error: " . $stmt->error;
        $_SESSION['message_type'] = "error";
    }

    // Redirect back to the same page to reflect the changes
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}
?>

<style>
    /* Centering the table */
    .table-container {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 20px;
        overflow-x: auto;
    }

    table {
        width: 100%;
        max-width: 1500px;
        border-collapse: collapse;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        animation: fadeIn 1s ease-in-out;
    }

    th, td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
        word-wrap: break-word;
    }

    th {
        background: linear-gradient(135deg, #1a73e8, #1557b0);
        color: #fff;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        .table-container {
            padding: 0 10px;
        }

        table {
            width: 100%;
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }

        th, td {
            padding: 10px;
            font-size: 14px;
        }
    }
</style>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Question</th>
                <th>User Answer</th>
                <th>Score</th>
                <th>Feedback</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result_answers->num_rows > 0) {
                while ($row = $result_answers->fetch_assoc()) {
            ?>
            <tr>
                <td><?php echo htmlspecialchars($row['question']); ?></td>
                <td><?php echo htmlspecialchars($row['user_answer']); ?></td>
                <td><?php echo isset($row['score']) ? $row['score'] : 'Not Graded'; ?></td>
                <td><?php echo htmlspecialchars($row['feedback'] ?? ''); ?></td>
                <td>
                    <form method="post" action="">
                        <input type="hidden" name="answer_id" value="<?php echo $row['answer_id']; ?>">
                        <button type="submit" name="evaluation" value="correct">✅ Correct</button>
                        <button type="submit" name="evaluation" value="wrong">❌ Wrong</button>
                    </form>
                </td>
            </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='5'>No pending answers for evaluation.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
