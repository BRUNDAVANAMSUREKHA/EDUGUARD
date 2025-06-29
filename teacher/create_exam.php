<?php
session_start();
if ($_SESSION['role'] !== 'teacher') {
    header('Location: ../index.php');
    exit();
}

include('../config/database.php');

// Handle Exam Creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_exam'])) {
    $course_id = $_POST['course_id'];
    $exam_name = $_POST['exam_name'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $time_limit = $_POST['time_limit'];

    // Validate start and end time
    if ($start_time >= $end_time) {
        $_SESSION['error'] = "End time must be later than start time.";
        header("Location: create_exam.php");
        exit();
    }

    $sql = "INSERT INTO exams (course_id, exam_name, description, date, start_time, end_time, time_limit) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssi", $course_id, $exam_name, $description, $date, $start_time, $end_time, $time_limit);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Exam created successfully.";
        header("Location: create_exam.php"); // Prevents duplicate submission
        exit();
    } else {
        $_SESSION['error'] = "Error creating exam: " . $stmt->error;
    }
}

// Fetch Existing Exams
$sql = "SELECT exams.id, exams.exam_name, exams.date, exams.start_time, exams.end_time, exams.time_limit, 
               courses.course_name, courses.section 
        FROM exams 
        LEFT JOIN courses ON exams.course_id = courses.id 
        ORDER BY exams.date DESC";
$result = $conn->query($sql);

// Fetch Courses List (only assigned to the faculty)
$faculty_id = $_SESSION['user_id']; // Assuming faculty ID is stored in session
$course_sql = "SELECT id, course_name, section FROM courses WHERE faculty_id = ?";
$course_stmt = $conn->prepare($course_sql);
$course_stmt->bind_param("i", $faculty_id);
$course_stmt->execute();
$course_result = $course_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create & Manage Exams</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Active Link */
        .active {
            background-color: #1e40af; /* Darker blue */
            font-weight: 600;
            transition: background-color 0.3s ease-in-out;
        }

        /* Dark Mode Styles */
        .dark-mode {
            background-color: #1a202c;
            color: #e2e8f0;
        }

        .dark-mode .bg-white {
            background-color: #2d3748;
            color: #e2e8f0;
        }

        .dark-mode .text-gray-700 {
            color: #cbd5e0;
        }

        .dark-mode .bg-blue-600 {
            background-color: #1e40af;
        }

        .dark-mode .hover\:bg-blue-700:hover {
            background-color: #1e3a8a;
        }

        .dark-mode .header {
            background-color: #2d3748;
        }

        .dark-mode .header h1 {
            color: #e2e8f0;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <?php include('partials/header.php'); ?>

    <!-- Sidebar -->
   

    <!-- Main Content -->
    <div class="main-content p-6 md:ml-6 mt-1">
        <h1 class="text-3xl font-bold mb-6">Create & Manage Exams</h1>

        <!-- Success and Error Messages -->
        <?php 
        if (isset($_SESSION['success'])) { 
            echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6'>" . $_SESSION['success'] . "</div>"; 
            unset($_SESSION['success']); 
        }
        if (isset($_SESSION['error'])) { 
            echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6'>" . $_SESSION['error'] . "</div>"; 
            unset($_SESSION['error']); 
        }
        ?>

        <!-- Create New Exam Form -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-2xl font-semibold mb-4">Create New Exam</h2>
            <form method="POST" action="create_exam.php" class="space-y-4">
                <div>
                    <label for="course_id" class="block text-sm font-medium text-gray-700">Course</label>
                    <select name="course_id" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                        <option value="">Select Course</option>
                        <?php while ($row = $course_result->fetch_assoc()) { ?>
                            <option value="<?php echo $row['id']; ?>">
                                <?php echo htmlspecialchars($row['course_name'] . " - " . $row['section']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div>
                    <label for="exam_name" class="block text-sm font-medium text-gray-700">Exam Name</label>
                    <input type="text" name="exam_name" placeholder="Exam Name" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Exam Description</label>
                    <textarea name="description" placeholder="Exam Description" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm"></textarea>
                </div>

                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">Exam Date</label>
                    <input type="date" name="date" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                </div>

                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                    <input type="time" name="start_time" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                    <input type="time" name="end_time" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                </div>

                <div>
                    <label for="time_limit" class="block text-sm font-medium text-gray-700">Time Limit (minutes)</label>
                    <input type="number" name="time_limit" placeholder="Time Limit" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                </div>

                <button type="submit" name="submit_exam" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200">
                    Create Exam
                </button>
            </form>
        </div>

        <!-- Existing Exams Table -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4">Existing Exams</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="px-4 py-2">Course</th>
                            <th class="px-4 py-2">Section</th>
                            <th class="px-4 py-2">Exam Name</th>
                            <th class="px-4 py-2">Date</th>
                            <th class="px-4 py-2">Start Time</th>
                            <th class="px-4 py-2">End Time</th>
                            <th class="px-4 py-2">Time Limit</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2"><?php echo htmlspecialchars($row['course_name']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($row['section']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($row['exam_name']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($row['date']); ?></td>
                            <td class="px-4 py-2"><?php echo date("h:i A", strtotime($row['start_time'])); ?></td>
                            <td class="px-4 py-2"><?php echo date("h:i A", strtotime($row['end_time'])); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($row['time_limit'] . " min"); ?></td>
                            <td class="px-4 py-2">
                                <button onclick="deleteExam(<?php echo $row['id']; ?>)" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition duration-200">
                                    Delete
                                </button>
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

    <script>
        // Delete Exam Function
        function deleteExam(examId) {
    if (confirm("Are you sure you want to delete this exam?")) {
        console.log("Deleting exam with ID:", examId); // Debugging
        fetch(`delete_exam.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ exam_id: examId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Exam deleted successfully.");
                window.location.reload();
            } else {
                alert("Error deleting exam: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
        });
    }
}
    </script>
</body>
</html>