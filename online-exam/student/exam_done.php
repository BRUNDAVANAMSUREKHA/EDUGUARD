<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$host = '127.0.0.1';
$db = 'exam_management';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Fetch the logged-in student's ID
$student_id = $_SESSION['user_id'];

// Fetch the student's results that have been evaluated by the teacher
$stmt = $pdo->prepare("
    SELECT r.id, r.score, r.created_at, e.exam_name, c.course_name
    FROM results r
    JOIN exams e ON r.exam_id = e.id
    JOIN courses c ON e.course_id = c.id
    WHERE r.student_id = ? AND r.status = 'evaluated'
    ORDER BY r.created_at DESC
");
$stmt->execute([$student_id]);
$results = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .no-results {
            text-align: center;
            color: #888;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Exam Results</h1>
        <?php if (count($results) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Exam Name</th>
                        <th>Course Name</th>
                        <th>Score</th>
                        <th>Date Attempted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $result): ?>
                        <tr>
                            <td><?= htmlspecialchars($result['exam_name']) ?></td>
                            <td><?= htmlspecialchars($result['course_name']) ?></td>
                            <td><?= htmlspecialchars($result['score']) ?></td>
                            <td><?= htmlspecialchars($result['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-results">No results found. You have not attempted any exams yet or your exams are still being evaluated.</p>
        <?php endif; ?>
    </div>
</body>
</html>