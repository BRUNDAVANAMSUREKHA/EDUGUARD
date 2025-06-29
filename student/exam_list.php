<?php
include('../config/session.php');
include('../config/database.php');

$student_id = $_SESSION['user_id']; // Get logged-in student ID

// Fetch only exams the student has NOT attempted
$sql = "SELECT exams.*, courses.course_name
        FROM exams 
        LEFT JOIN results ON exams.id = results.exam_id AND results.student_id = ?
        JOIN courses ON exams.course_id = courses.id
        WHERE exams.date >= CURDATE() AND results.exam_id IS NULL"; // Exclude attempted exams

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam List</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Fix the sidebar to the left */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px; /* Adjust width as needed */
            height: 100vh;
            background-color: #2D3748;
            color: white;
            padding-top: 20px;
        }

        /* Push main content to the right */
        .main-content {
            margin-left: 260px; /* Ensure enough space for sidebar */
            padding: 20px;
        }

        /* Center and move table to the top */
        .exam-table-container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            margin-top: 20px; /* Adjust top spacing */
            background: white;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Responsive Table */
        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }
            thead tr {
                display: none;
            }
            tr {
                border: 1px solid #ddd;
                margin-bottom: 10px;
            }
            td {
                position: relative;
                padding-left: 50%;
                border-bottom: 1px solid #eee;
            }
            td:before {
                position: absolute;
                top: 6px;
                left: 10px;
                font-weight: bold;
                content: attr(data-label);
            }
        }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Sidebar -->
    <div class="sidebar">
        <?php include('partials/header.php'); ?>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1 class="text-2xl font-bold text-center mb-4">Upcoming Exams</h1>
        
        <!-- Exam Table Positioned at the Top -->
        <div class="exam-table-container">
            <table class="w-full border-collapse">
                <thead class="bg-gray-200 text-center">
                    <tr>
                        <th class="px-4 py-2 border">Course</th>
                        <th class="px-4 py-2 border">Exam Name</th>
                        <th class="px-4 py-2 border">Date</th>
                        <th class="px-4 py-2 border">Start Time</th>
                        <th class="px-4 py-2 border">End Time</th>
                        <th class="px-4 py-2 border">Action</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php while ($row = $result->fetch_assoc()) { 
                        $current_time = date("H:i:s");
                        $start_time = date("H:i:s", strtotime($row['start_time']));
                        $end_time = date("H:i:s", strtotime($row['end_time']));
                        $is_exam_over = ($current_time > $end_time);
                    ?>
                    <tr class="border-b">
                        <td class="px-4 py-2 border"><?php echo htmlspecialchars($row['course_name']); ?></td>
                        <td class="px-4 py-2 border"><?php echo htmlspecialchars($row['exam_name']); ?></td>
                        <td class="px-4 py-2 border"><?php echo htmlspecialchars($row['date']); ?></td>
                        <td class="px-4 py-2 border"><?php echo date("h:i A", strtotime($row['start_time'])); ?></td>
                        <td class="px-4 py-2 border"><?php echo date("h:i A", strtotime($row['end_time'])); ?></td>
                        <td class="px-4 py-2 border">
                            <?php if ($is_exam_over) { ?>
                                <span class="text-red-600">Exam time over</span>
                            <?php } else { ?>
                                <a href="instructions.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="text-blue-600 hover:underline">Start Exam</a>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php if ($result->num_rows == 0) { ?>
                        <tr><td colspan="6" class="px-4 py-2 text-center">No available exams.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
