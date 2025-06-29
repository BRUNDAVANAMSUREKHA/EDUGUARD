<?php
include('../config/session.php');
include('../config/database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $department = $_POST['department'];
    $year = $_POST['year'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing the password

    // Check if email already exists
    $check_email_sql = "SELECT id FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_email_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $error = "❌ Error: This email is already registered.";
    } else {
        // Insert new user
        $sql = "INSERT INTO users (name, email, role, department, year, password) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $name, $email, $role, $department, $year, $password);

        if ($stmt->execute()) {
            $success = "✅ User created successfully!";
        } else {
            $error = "❌ Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include('partials/header.php'); ?>
    <?php include('partials/sidebar.php'); ?>

    <div class="main-content p-6 md:ml-64 mt-16">
        <h1 class="text-2xl font-semibold mb-6">Create User</h1>

        <!-- Success and Error Messages -->
        <?php if (isset($success)) { echo "<p class='success bg-green-100 text-green-700 p-3 rounded mb-6'>$success</p>"; } ?>
        <?php if (isset($error)) { echo "<p class='error bg-red-100 text-red-700 p-3 rounded mb-6'>$error</p>"; } ?>

        <!-- Create User Form -->
        <form method="post" action="create_user.php" class="bg-white p-6 rounded-lg shadow-md max-w-2xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="name" placeholder="Name" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" placeholder="Email" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                    <select name="role" id="role" required onchange="toggleSectionField()" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="department" class="block text-sm font-medium text-gray-700">Department</label>
                    <input type="text" name="department" id="department" placeholder="Department" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                    <input type="text" name="year" id="year" placeholder="Year" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div id="sectionField" class="mb-4">
                    <label for="section" class="block text-sm font-medium text-gray-700">Section (For Students)</label>
                    <input type="text" name="section" id="section" placeholder="Section" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" placeholder="Password" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700 transition duration-200">Create User</button>
        </form>
    </div>

    <?php include('partials/footer.php'); ?>

    <script>
        function toggleSectionField() {
            const role = document.getElementById('role').value;
            const sectionField = document.getElementById('sectionField');
            if (role === 'student') {
                sectionField.style.display = 'block';
            } else {
                sectionField.style.display = 'none';
            }
        }

        // Initial call to set the correct visibility
        toggleSectionField();
    </script>
</body>
</html>