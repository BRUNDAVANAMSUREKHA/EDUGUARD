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

// Handle CSV export
if (isset($_POST['export_csv'])) {
    // Re-fetch the results for CSV export
    $export_sql = "
        SELECT users.name AS student_name, exams.exam_name AS exam_name, results.total_score 
        FROM results 
        JOIN users ON results.user_id = users.id 
        JOIN exams ON results.exam_id = exams.id
    ";
    $export_result = $conn->query($export_sql);

    if ($export_result->num_rows > 0) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="results.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, array('Student Name', 'Exam Name', 'Total Score'));

        while ($row = $export_result->fetch_assoc()) {
            fputcsv($output, array(
                $row['student_name'],
                $row['exam_name'],
                $row['total_score']
            ));
        }

        fclose($output);
        exit();
    } else {
        echo "<p>No data to export.</p>";
    }
}
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
        }
        .container {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 100%;
            margin: 2rem auto;
            overflow-x: auto;
        }
        h1 {
            color: #333;
            margin-bottom: 1.5rem;
            text-align: center;
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
            background-color: #007bff;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
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
            margin-right: 10px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-export {
            background-color: #28a745;
        }
        .btn-export:hover {
            background-color: #218838;
        }
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            table {
                font-size: 14px;
            }
            .btn {
                width: 100%;
                margin-bottom: 10px;
                text-align: center;
            }
        }
    </style>
</head>
<body>

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
                <?php
                // Reset the result pointer to reuse the query result
                $results_result->data_seek(0);
                while ($row = $results_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['exam_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_score']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <form method="post" action="">
            <button type="submit" name="export_csv" class="btn btn-export">
                <i class="fas fa-download"></i> Export to CSV
            </button>
            <a href="verify_results.php" class="btn">Verify More Results</a>
        </form>
    </div>
</body>
</html>