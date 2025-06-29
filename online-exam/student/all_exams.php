<?php
include('../config/session.php');
include('../config/database.php');

$student_id = $_SESSION['user_id']; // Get logged-in student ID

// Fetch all upcoming exams along with results for the student
$sql = "SELECT exams.*, results.id AS result_id 
        FROM exams 
        LEFT JOIN results ON exams.id = results.exam_id AND results.user_id = ?
        WHERE exams.date >= CURDATE()";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Exams</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* General Styles */
        body {
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f0f0f0;
        }

        /* Header */
        header {
            background-color: #1a73e8;
            color: white;
            padding: 15px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }

        /* Main Content */
        .main-content {
            margin-top: 80px; /* Adjust based on header height */
            padding: 20px;
        }

        .content-wrapper {
            width: 100%;
            max-width: 800px;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 0 auto; /* Center the content */
        }

        /* Table Container */
        .table-container {
            width: 100%;
            overflow-x: auto; /* Ensures scrollability on mobile */
            margin-top: 20px;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .exam-completed {
            color: red;
            font-weight: bold;
        }

        .start-exam-link {
            color: #1a73e8;
            text-decoration: none;
            font-weight: bold;
        }

        .start-exam-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include('partials/header.php'); ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="content-wrapper">
            <h1>All Exams</h1>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Exam Name</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Take Exam</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['course_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['exam_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo htmlspecialchars($row['time']); ?></td>
                            <td>
                                <?php if ($row['result_id']): ?>
                                    <span class="exam-completed">Exam Completed</span>
                                <?php else: ?>
                                    <a href="instructions.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="start-exam-link">Start Exam</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include('partials/footer.php'); ?>
</body>
</html>