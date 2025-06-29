<?php
session_start();
include('../config/database.php');

// Ensure only logged-in teachers can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit();
}

// Prevent browser caching of dashboard (important)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <script>
        // Disable back button after logout
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };
    </script>
</head>
<body>
    <?php include('partials/header.php'); ?>
    <div class="main-content">
        <h1>Teacher Dashboard</h1>
        <p>Welcome, Teacher!</p>
        <a href="logout.php" class="btn">Logout</a>
    </div>
    <?php include('partials/footer.php'); ?>
</body>
</html>
