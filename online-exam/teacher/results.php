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

// Fetch all results
$results_sql = "
    SELECT results.*, users.name AS student_name, exams.exam_name AS exam_name 
    FROM results 
    JOIN users ON results.user_id = users.id 
    JOIN exams ON results.exam_id = exams.id
";
$results_result = $conn->query($results_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">

    <style>
        body {
    font-family: 'Arial', sans-serif;
    background-color: #f8f9fa;
    margin: 0;
    padding: 0;
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
    min-height: auto;
    margin-left: 150px; /* Offset for fixed sidebar */
    padding: 20px;
}
h1 {
            color: #333;
            margin-bottom: 1.5rem;
            font-size: 1.75rem;
        }
.container {
    background: #fff;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 800px;
    width: 100%;
    text-align: center;
}

h1 {
    color: #333;
    margin-bottom: 1.5rem;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1.5rem;
}

table, th, td {
    border: 1px solid #ddd;
}

th, td {
    padding: 0.75rem;
    text-align: left;
}

th {
    background-color: #f2f2f2;
}

.btn {
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.btn:hover {
    background-color: #0056b3;
}

/* Responsive Design */
@media (max-width: 768px) {
    header {
        width: 200px;
    }

    .main-content {
        margin-left: 200px;
        padding: 10px;
    }

    .container {
        padding: 1.5rem;
    }

    th, td {
        font-size: 0.9rem;
    }

    .btn {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }
}

@media (max-width: 600px) {
    header {
        width: 180px;
    }

    .main-content {
        margin-left: 180px;
        padding: 5px;
    }

    .container {
        max-width: 90%;
    }

    table {
        font-size: 0.8rem;
    }
}

    </style>
</head>
<body>
<div class="main-content">
    <div class="container">
        <h1>Results</h1>
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Exam Name</th>
                    <th>Total Score</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $results_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['exam_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_score']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="verify_results.php" class="btn">Verify More Results</a>
    </div>
</div>

</body>
</html>