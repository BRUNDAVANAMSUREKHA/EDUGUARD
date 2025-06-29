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

// Fetch the student's results
$stmt = $pdo->prepare("
    SELECT r.id, r.total_score AS score, r.created_at, e.exam_name, c.course_name
    FROM results r
    JOIN exams e ON r.exam_id = e.id
    JOIN courses c ON e.course_id = c.id
    WHERE r.user_id = ?
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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Ensure table is at the top */
        .main-content {
            margin-left: 16rem; /* Matches sidebar width */
            padding: 50px; /* Remove padding to push content up */
            position: absolute;
            top: 0;
            margin-top: 80px; /* Adjust based on header height */
            width: 100%;
        }
        .table-container {
            width: 100%;
            max-width: 900px; /* Adjust width */
            margin: 0 auto; /* Center horizontally */
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 20px;
        }
        .results-table {
            width: 100%;
            border-collapse: collapse;
        }
        .results-table th,
        .results-table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        .results-table th {
            background-color: #f9fafb;
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            font-size: 0.75rem;
        }
        .results-table tbody tr:hover {
            background-color: #f3f4f6;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <?php include('partials/header.php'); ?>

    <!-- Sidebar -->
    <?php include('partials/sidebar.php'); ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="table-container">
            <h1 class="text-2xl font-bold text-center mb-4">Exam Results</h1>
            
            <table class="results-table">
                <thead>
                    <tr>
                        <th>Exam Name</th>
                        <th>Course Name</th>
                        <th>Score</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($results)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-gray-500">No results found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($results as $result): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($result['exam_name']); ?></td>
                                <td><?php echo htmlspecialchars($result['course_name']); ?></td>
                                <td><?php echo htmlspecialchars($result['score']); ?></td>
                                <td><?php echo htmlspecialchars($result['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
