<?php
include('../config/session.php');
include('../config/database.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* General Styles */
        :root {
            --primary-color: #1a73e8;
            --primary-hover: #1557b5;
            --text-color: #202124;
            --text-secondary: #5f6368;
            --bg-color: #f0f0f0;
            --card-bg: #fff;
            --sidebar-bg: #fff;
            --header-bg: #fff;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --hover-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            --border-color: #e0e0e0;
            --status-active: #4CAF50;
            --status-inactive: #F44336;
            --transition-speed: 0.3s;
        }

        .dark-mode {
            --primary-color: #4285f4;
            --primary-hover: #5a95f5;
            --text-color: #e0e0e0;
            --text-secondary: #b0b0b0;
            --bg-color: #202124;
            --card-bg: #2c2c2e;
            --sidebar-bg: #2c2c2e;
            --header-bg: #2c2c2e;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            --hover-shadow: 0 6px 12px rgba(0, 0, 0, 0.4);
            --border-color: #3a3a3c;
            --status-active: #66BB6A;
            --status-inactive: #EF5350;
        }

        body {
            font-family: 'Roboto', Arial, sans-serif;
            background-color: var(--bg-color);
            margin: 0;
            padding: 0;
            color: var(--text-color);
            transition: background-color var(--transition-speed);
        }

        /* Header */
        .header {
            background-color: var(--header-bg);
            padding: 15px 20px;
            box-shadow: var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            right: 0;
            left: 80px;
            z-index: 100;
            transition: left var(--transition-speed);
        }

        .header h2 {
            margin: 0;
            font-weight: 500;
            font-size: 1.5rem;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* Dark Mode Toggle */
        .toggle-mode {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color var(--transition-speed);
        }

        .toggle-mode:hover {
            background-color: var(--primary-hover);
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            height: 100vh;
            width: 80px;
            background-color: var(--sidebar-bg);
            box-shadow: var(--shadow);
            transition: width var(--transition-speed);
            overflow: hidden;
            z-index: 200;
        }

        .sidebar:hover {
            width: 250px;
        }

        .sidebar-header {
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .sidebar-header img {
            width: 40px;
            height: 40px;
            margin-right: 15px;
        }

        .sidebar-header h3 {
            white-space: nowrap;
            opacity: 0;
            transition: opacity var(--transition-speed);
        }

        .sidebar:hover .sidebar-header h3 {
            opacity: 1;
        }

        .sidebar-menu {
            padding: 0;
            list-style-type: none;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            text-decoration: none;
            color: var(--text-color);
            transition: background-color var(--transition-speed);
        }

        .sidebar-menu a:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .sidebar-menu a i {
            font-size: 18px;
            margin-right: 15px;
            width: 20px;
            text-align: center;
        }

        .sidebar-menu a span {
            white-space: nowrap;
        }

        /* Main Content */
        .main-content {
            margin-left: 80px;
            padding: 80px 20px 20px;
            transition: margin-left var(--transition-speed);
        }

        /* Welcome Section */
        .welcome-message {
            background: var(--card-bg);
            padding: 24px;
            border-radius: 8px;
            box-shadow: var(--shadow);
            text-align: center;
            margin-bottom: 20px;
        }

        .welcome-message p {
            font-size: 18px;
            color: var(--text-secondary);
            margin: 0;
        }

        /* Card Styles */
        .feature-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 8px;
            box-shadow: var(--shadow);
            text-align: center;
            transition: transform var(--transition-speed), box-shadow var(--transition-speed);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }

        .card h2 {
            color: var(--text-color);
            font-weight: 500;
            margin-bottom: 10px;
        }

        .card p {
            color: var(--text-secondary);
            font-size: 14px;
            margin: 0;
        }

        .card a {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            transition: background-color var(--transition-speed);
        }

        .card a:hover {
            background-color: var(--primary-hover);
        }

        /* Footer */
        .footer {
            background-color: var(--card-bg);
            padding: 15px 20px;
            text-align: center;
            margin-top: 30px;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }

        .footer p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 14px;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
            }
            
            .sidebar.active {
                width: 250px;
            }
            
            .header {
                left: 0;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .menu-toggle {
                display: block;
                font-size: 24px;
                cursor: pointer;
            }
            
            .feature-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include('partials/header.php'); ?>
    <?php include('partials/sidebar.php'); ?>
    <div class="main-content">
        

        <!-- Welcome Message -->
        <div class="welcome-message">
            <p>Welcome, Admin!</p>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-3xl font-semibold mb-4">Welcome to the Admin Panel</h2>
                <p class="text-gray-700">Manage your online exam system efficiently.</p>
            </div>
        </div>

        
    </div>
    <?php include('partials/footer.php'); ?>
</body>
</html>