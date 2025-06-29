<?php
include('../config/session.php');
include('../config/database.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <?php include('partials/header.php'); ?>

    <!-- Main Content -->
    <div class="flex">
        <!-- Sidebar (Hidden on mobile by default) -->
        <div id="sidebar" class="bg-blue-800 text-white w-64 space-y-6 py-7 px-2 fixed inset-y-0 left-0 transform -translate-x-full md:translate-x-0 transition duration-200 ease-in-out">
            <?php include('partials/sidebar.php'); ?>
        </div>

        <!-- Content Area -->
        <div class="flex-1 p-6 md:ml-64 min-h-screen flex items-center justify-center">
            <!-- Dashboard Content -->
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h2 class="text-3xl font-semibold mb-4 text-gray-800">Welcome to the Student Panel</h2>
                <p class="text-gray-700 mb-6">Access your exams and results here.</p>

                <h1 class="text-2xl font-bold text-gray-800 mb-4">Student Dashboard</h1>
                <p class="text-gray-700">Welcome, Student!</p>

                
        </div>
    </div>

    <!-- Footer -->
    <?php include('partials/footer.php'); ?>

    <!-- Scripts -->
    <script>
        // Toggle sidebar for mobile view
        const sidebar = document.getElementById('sidebar');
        const menuButton = document.getElementById('menu-button');

        menuButton.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });
    </script>
</body>
</html>