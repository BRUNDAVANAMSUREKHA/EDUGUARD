<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Panel</title>
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

        /* Sidebar Animation */
        #sidebar {
            transition: transform 0.3s ease-in-out;
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

        .dark-mode footer {
            background-color: #1e293b; /* Darker footer for dark mode */
        }

        /* Ensure the sidebar and main content are always at the top */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        #sidebar {
            height: 100vh; /* Full height of the viewport */
            overflow-y: auto; /* Enable scrolling if content overflows */
        }

        .main-content {
            flex: 1;
            overflow-y: auto; /* Enable scrolling for main content */
        }

        /* Header Styles */
        .header {
            background-color: #ffffff;
            padding: 16px 24px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            transition: background-color 0.3s ease-in-out;
        }

        .dark-mode .header {
            background-color: #2d3748;
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
            color: #1a202c;
        }

        .dark-mode .header h1 {
            color: #e2e8f0;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-right .dark-mode-toggle {
            background-color: #1a73e8;
            color: white;
            border: none;
            padding: 8px;
            border-radius: 50%;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }

        .header-right .dark-mode-toggle:hover {
            background-color: #1557b5;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <div class="header">
        <h1>Teacher Panel</h1>
        <div class="header-right">
            <!-- Dark Mode Toggle -->
            <button class="dark-mode-toggle" id="dark-mode-toggle">
                <i class="fas fa-moon text-sm"></i>
            </button>
        </div>
    </div>

    <!-- Sidebar -->
    <div id="sidebar" class="bg-blue-600 text-white w-full md:w-64 fixed z-10 transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out">
        <div class="p-6">
            <h1 class="text-2xl font-semibold text-center mb-6">Teacher Panel</h1>
            <nav>
                <ul>
                <li>
        <a href="dashboard.php" class="block py-2 px-4 rounded text-white hover:bg-blue-700 transition duration-200">
            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
        </a>
    </li>
    <li>
        <a href="create_exam.php" class="block py-2 px-4 rounded text-white hover:bg-blue-700 transition duration-200">
            <i class="fas fa-plus-circle mr-2"></i>Create Exam
        </a>
    </li>
    <li>
        <a href="add_questions.php" class="block py-2 px-4 rounded text-white hover:bg-blue-700 transition duration-200">
            <i class="fas fa-question-circle mr-2"></i>Add Questions
        </a>
    </li>
    <li>
        <a href="evaluate.php" class="block py-2 px-4 rounded text-white hover:bg-blue-700 transition duration-200">
            <i class="fas fa-check-circle mr-2"></i>Evaluate Answers
        </a>
    </li>
    <li>
        <a href="Verify_results.php" class="block py-2 px-4 rounded text-white hover:bg-blue-700 transition duration-200">
            <i class="fas fa-check-double mr-2"></i>Verify Results
        </a>
    </li>
    <li>
        <a href="results.php" class="block py-2 px-4 rounded text-white hover:bg-blue-700 transition duration-200">
            <i class="fas fa-poll mr-2"></i>Results
        </a>
    </li>
    <li>
        <a href="logout.php" class="block py-2 px-4 rounded text-white hover:bg-blue-700 transition duration-200">
            <i class="fas fa-sign-out-alt mr-2"></i>Logout
        </a>
    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Backdrop for Mobile -->
    <div id="backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-0 hidden md:hidden"></div>

    <!-- Main Content -->
    <div class="main-content p-6 md:ml-64 mt-16"> <!-- Added mt-16 to account for header height -->
        <!-- Mobile Menu Button -->
        <button id="menu-button" class="md:hidden mb-4 p-2 bg-blue-600 text-white rounded focus:outline-none hover:bg-blue-700 transition duration-200">
            <i class="fas fa-bars"></i>
        </button>


    <!-- Loading Spinner -->
    <div id="loading-spinner" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-600"></div>
    </div>

    <script>
        // Toggle Sidebar on Mobile
        const sidebar = document.getElementById('sidebar');
        const menuButton = document.getElementById('menu-button');
        const backdrop = document.getElementById('backdrop');

        menuButton.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        });

        backdrop.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
        });

        // Highlight Active Link
        const currentPage = window.location.pathname.split('/').pop();
        const links = document.querySelectorAll('#sidebar a');

        links.forEach(link => {
            if (link.getAttribute('href') === currentPage) {
                link.classList.add('active');
            }
        });

        // Dark Mode Toggle
        const darkModeToggle = document.getElementById('dark-mode-toggle');
        const body = document.body;

        darkModeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            const icon = darkModeToggle.querySelector('i');
            if (body.classList.contains('dark-mode')) {
                icon.classList.replace('fa-moon', 'fa-sun');
            } else {
                icon.classList.replace('fa-sun', 'fa-moon');
            }
        });

        // Show/Hide Loading Spinner (Example Usage)
        function showSpinner() {
            document.getElementById('loading-spinner').classList.remove('hidden');
        }

        function hideSpinner() {
            document.getElementById('loading-spinner').classList.add('hidden');
        }

        // Example: Simulate loading
        showSpinner();
        setTimeout(hideSpinner, 2000); // Hide spinner after 2 seconds
    </script>
</body>
</html>