<?php
include('../config/session.php');
include('../config/database.php');

// Handle User Deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM users WHERE id='$id'";
    if ($conn->query($sql) === TRUE) {
        $success = "✅ User deleted successfully!";
    } else {
        $error = "❌ Error: " . $conn->error;
    }
}

// Fetch All Users
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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
        <div class="w-full max-w-6xl mx-auto bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-3xl font-semibold text-center mb-6">Manage Users</h1>

            <!-- Success and Error Messages -->
            <?php if (isset($success)) { echo "<p class='success bg-green-100 text-green-700 p-4 rounded mb-4'>$success</p>"; } ?>
            <?php if (isset($error)) { echo "<p class='error bg-red-100 text-red-700 p-4 rounded mb-4'>$error</p>"; } ?>

            <!-- User Table -->
            <div class="overflow-x-auto">
                <table class="w-full table-auto bg-white rounded-lg shadow-lg">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="p-4 text-left">Name</th>
                            <th class="p-4 text-left">Email</th>
                            <th class="p-4 text-left">Role</th>
                            <th class="p-4 text-left">Department</th>
                            <th class="p-4 text-left">Year</th>
                            <th class="p-4 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr class="hover:bg-gray-100 transition duration-200">
                                <td class="p-4"><?php echo htmlspecialchars($row['name']); ?></td>
                                <td class="p-4"><?php echo htmlspecialchars($row['email']); ?></td>
                                <td class="p-4"><?php echo htmlspecialchars($row['role']); ?></td>
                                <td class="p-4"><?php echo htmlspecialchars($row['department']); ?></td>
                                <td class="p-4"><?php echo htmlspecialchars($row['year']); ?></td>
                                <td class="p-4">
                                    <a href="manage_users.php?delete=<?php echo $row['id']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this user?');" 
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