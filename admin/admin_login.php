<?php
session_start();
include('../config/database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ? AND role = 'admin'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_id'] = $row['id'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "❌ Invalid password";
        }
    } else {
        $error = "❌ Admin not found";
    }
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"></link>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-semibold text-center mb-6">Admin Login</h2>
        <?php if (isset($error)) { echo "<p class='text-red-500 text-center mb-4'>$error</p>"; } ?>
        <form method="post" action="" class="space-y-4">
            <div>
                <input type="email" name="email" placeholder="Email" required class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600 transition duration-200">
            </div>
            <div>
                <input type="password" name="password" placeholder="Password" required class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-600 transition duration-200">
            </div>
            <div>
                <button type="submit" class="w-full p-3 bg-blue-600 text-white rounded hover:bg-blue-700 transition duration-200">Login</button>
            </div>
        </form>
    </div>
</body>
</html>
