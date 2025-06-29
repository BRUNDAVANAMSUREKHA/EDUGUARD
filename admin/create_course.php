<?php
include('../config/session.php');
include('../config/database.php');

// ✅ Handle Course Deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql_delete = "DELETE FROM courses WHERE id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        $success = "✅ Course deleted successfully!";
    } else {
        $error = "❌ Error deleting course: " . $stmt->error;
    }
}

// ✅ Handle Course Creation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_name = $_POST['course_name'];
    $department = $_POST['department'];
    $section = $_POST['section'];
    $faculty_id = $_POST['faculty_id'];

    $sql = "INSERT INTO courses (course_name, department, section, faculty_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $course_name, $department, $section, $faculty_id);

    if ($stmt->execute()) {
        $success = "✅ Course created successfully!";
    } else {
        $error = "❌ Error: " . $stmt->error;
    }
}

// ✅ Fetch All Courses
$sql_courses = "SELECT courses.id, courses.course_name, courses.department, courses.section, users.name AS faculty_name 
                FROM courses 
                LEFT JOIN users ON courses.faculty_id = users.id";
$result_courses = $conn->query($sql_courses);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Courses</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"></link>
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

        .dark-mode .bg-gray-200 {
            background-color: #374151;
        }

        .dark-mode .hover\:bg-gray-100:hover {
            background-color: #4b5563;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <?php include('partials/header.php'); ?>

    <!-- Sidebar -->
    <?php include('partials/sidebar.php'); ?>

    <!-- Main Content -->
    <div class="main-content p-6 md:ml-64 mt-16">
        <div class="w-full max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-3xl font-semibold text-center mb-6">Manage Courses</h1>

            <!-- Success and Error Messages -->
            <?php if (isset($success)) { echo "<p class='success bg-green-100 text-green-700 p-4 rounded mb-4'>$success</p>"; } ?>
            <?php if (isset($error)) { echo "<p class='error bg-red-100 text-red-700 p-4 rounded mb-4'>$error</p>"; } ?>

            <!-- Course Creation Form -->
            <form method="post" action="create_course.php" class="space-y-4">
                <div>
                    <input type="text" name="course_name" placeholder="Course Name" required class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600 transition duration-200">
                </div>
                <div>
                    <input type="text" name="department" placeholder="Department" required class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600 transition duration-200">
                </div>
                <div>
                    <input type="text" name="section" placeholder="Section" required class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600 transition duration-200">
                </div>
                <div>
                    <select name="faculty_id" required class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600 transition duration-200">
                        <option value="">Select Faculty</option>
                        <?php
                        $faculty_query = "SELECT id, name FROM users WHERE role='teacher'";
                        $faculty_result = $conn->query($faculty_query);
                        while ($faculty = $faculty_result->fetch_assoc()) {
                            echo "<option value='" . $faculty['id'] . "'>" . $faculty['name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full p-3 bg-blue-600 text-white rounded hover:bg-blue-700 transition duration-200">Create Course</button>
                </div>
            </form>

            <!-- Display Course List -->
            <h2 class="text-2xl font-semibold text-center mt-12 mb-6">Course List</h2>
            <div class="overflow-x-auto">
                <table class="w-full table-auto bg-white rounded-lg shadow-lg">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="p-4 text-left">Course Name</th>
                            <th class="p-4 text-left">Department</th>
                            <th class="p-4 text-left">Section</th>
                            <th class="p-4 text-left">Faculty</th>
                            <th class="p-4 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($course = $result_courses->fetch_assoc()) { ?>
                            <tr class="hover:bg-gray-100 transition duration-200">
                                <td class="p-4"><?php echo htmlspecialchars($course['course_name']); ?></td>
                                <td class="p-4"><?php echo htmlspecialchars($course['department']); ?></td>
                                <td class="p-4"><?php echo htmlspecialchars($course['section']); ?></td>
                                <td class="p-4"><?php echo htmlspecialchars($course['faculty_name']); ?></td>
                                <td class="p-4">
                                    <a href="create_course.php?delete_id=<?php echo $course['id']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this course?');" 
                                       class="text-red-600 hover:text-red-800 transition duration-200">Delete</a>
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