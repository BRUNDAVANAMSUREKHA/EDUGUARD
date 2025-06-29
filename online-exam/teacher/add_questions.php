<?php
ob_start(); // Start output buffering
include('../config/database.php'); // Ensure correct DB connection
include('partials/header.php');

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// Handle form submission to add a question
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['question_type'], $_POST['question'], $_POST['exam_id'])) {
        $exam_id = $_POST['exam_id'];
        $question_type = $_POST['question_type'];
        $question = $_POST['question'];

        // Check if options exist, otherwise store NULL
        if (isset($_POST['options']) && !empty(array_filter($_POST['options']))) {
            $options = json_encode(array_filter($_POST['options'])); // Remove empty values
        } else {
            $options = null;
        }

        // Handle correct answers (if available)
        if (isset($_POST['correct']) && !empty($_POST['correct'])) {
            $correct_answers = json_encode($_POST['correct']);
        } else {
            $correct_answers = null;
        }

        // Insert the question into the database
        $stmt = $conn->prepare("INSERT INTO questions (exam_id, question_type, question, options, answer) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $exam_id, $question_type, $question, $options, $correct_answers);

        if ($stmt->execute()) {
            // Debugging: Check if headers are already sent
            if (headers_sent()) {
                die("Headers already sent. Output started at: " . headers_sent(true));
            }
            // Redirect to the same page with the selected exam to keep the selection
            header("Location: add_questions.php?exam_id=$exam_id&success=1");
            exit();
        } else {
            echo "<p class='text-red-500'>❌ SQL Error: " . $stmt->error . "</p>";
        }
    } else {
        echo "<p class='text-red-500'>❌ Missing required fields.</p>";
    }
}

// Handle question deletion
if (isset($_GET['delete']) && isset($_GET['exam_id'])) {
    $question_id = intval($_GET['delete']); // Ensure it's an integer
    $exam_id = intval($_GET['exam_id']); // Ensure it's an integer

    $stmt = $conn->prepare("DELETE FROM questions WHERE id = ?");
    $stmt->bind_param("i", $question_id);

    if ($stmt->execute()) {
        // Debugging: Check if headers are already sent
        if (headers_sent()) {
            die("Headers already sent. Output started at: " . headers_sent(true));
        }
        // Redirect to keep the selected exam and avoid multiple deletions on refresh
        header("Location: add_questions.php?exam_id=$exam_id&deleted=1");
        exit();
    } else {
        echo "<p class='text-red-500'>❌ SQL Error: " . $stmt->error . "</p>";
    }
}

// Fetch exams
$sql_exams = "SELECT * FROM exams";
$result_exams = $conn->query($sql_exams);

// Fetch questions for the selected exam
$selected_exam_id = $_GET['exam_id'] ?? '';
$questions = [];

if ($selected_exam_id) {
    $stmt = $conn->prepare("SELECT * FROM questions WHERE exam_id = ?");
    $stmt->bind_param("i", $selected_exam_id);
    $stmt->execute();
    $result_questions = $stmt->get_result();
    
    while ($row = $result_questions->fetch_assoc()) {
        $questions[] = $row;
    }
}
ob_end_flush(); // End output buffering and send output to the browser
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Questions</title>
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

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateX(-10px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
    <script>
        function updateForm() {
            const type = document.getElementById('question_type').value;
            const optionsContainer = document.getElementById('options_container');

            if (type === 'multiple_choice' || type === 'single_choice') {
                optionsContainer.classList.remove('hidden');
                updateOptions(type);
            } else {
                optionsContainer.classList.add('hidden');
            }
        }

        function updateOptions(type) {
            const optionsDiv = document.getElementById('options');
            optionsDiv.innerHTML = '';
            
            for (let i = 1; i <= 2; i++) {
                addOption(type);
            }
        }

        function addOption(type) {
            const optionsDiv = document.getElementById('options');
            const optionDiv = document.createElement('div');
            const optionCount = optionsDiv.children.length + 1;

            optionDiv.className = 'flex items-center gap-2 mb-2';
            optionDiv.innerHTML = `
                <input type="text" name="options[]" placeholder="Option ${optionCount}" required class="w-full p-2 border border-gray-300 rounded-md shadow-sm">
                ${type === 'multiple_choice' 
                    ? `<input type="checkbox" name="correct[]" value="${optionCount}" class="form-checkbox h-5 w-5 text-blue-600"> Correct`
                    : `<input type="radio" name="correct[]" value="${optionCount}" required class="form-radio h-5 w-5 text-blue-600"> Correct`}
                <button type="button" onclick="this.parentElement.remove()" class="bg-red-500 text-white px-2 py-1 rounded-md hover:bg-red-600 transition duration-200">❌</button>
            `;
            optionsDiv.appendChild(optionDiv);
        }
    </script>
</head>
<body class="bg-gray-100">

    <!-- Main Content -->
    <div class="main-content p-6 md:ml-6 mt-16">
        <h1 class="text-3xl font-bold text-blue-600 mb-6">Add Questions</h1>

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

        <!-- Exam Selection -->
        <form method="get" action="add_questions.php" class="bg-white p-6 rounded-lg shadow-md mb-8">
            <label class="block text-sm font-medium text-gray-700">Select Exam:</label>
            <select name="exam_id" onchange="this.form.submit()" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                <option value="">Select Exam</option>
                <?php while ($row = $result_exams->fetch_assoc()) { ?>
                <option value="<?php echo $row['id']; ?>" 
                    <?php echo ($row['id'] == $selected_exam_id) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($row['exam_name']); ?>
                </option>
                <?php } ?>
            </select>
        </form>

        <?php if ($selected_exam_id): ?>
        <!-- Add Questions Form -->
        <form method="post" action="add_questions.php" class="bg-white p-6 rounded-lg shadow-md mb-8">
            <input type="hidden" name="exam_id" value="<?php echo $selected_exam_id; ?>">

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Question Type:</label>
                <select name="question_type" id="question_type" required onchange="updateForm()" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="single_choice">Single Choice</option>
                    <option value="short_answer">Short Answer</option>
                    <option value="long_answer">Long Answer</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Question:</label>
                <textarea name="question" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm"></textarea>
            </div>

            <div id="options_container" class="hidden mb-4">
                <h3 class="text-lg font-semibold mb-2">Options</h3>
                <div id="options" class="space-y-2"></div>
                <button type="button" onclick="addOption(document.getElementById('question_type').value)" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-200">➕ Add Option</button>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200">Add Question</button>
        </form>

        <!-- Existing Questions Table -->
        <h2 class="text-2xl font-bold text-blue-600 mb-4">Existing Questions</h2>
        <div class="overflow-x-auto bg-white rounded-lg shadow-md">
            <table class="min-w-full">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="px-4 py-2">Question</th>
                        <th class="px-4 py-2">Options</th>
                        <th class="px-4 py-2">Answer</th>
                        <th class="px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($questions)): ?>
                        <?php foreach ($questions as $row): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2"><?php echo htmlspecialchars($row['question']); ?></td>
                            <td class="px-4 py-2">
                                <?php 
                                $options = json_decode($row['options'], true);
                                if ($options) {
                                    echo implode("<br>", array_map("htmlspecialchars", $options));
                                }
                                ?>
                            </td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars(implode(", ", json_decode($row['answer'], true) ?? [])); ?></td>
                            <td class="px-4 py-2">
                                <a href="add_questions.php?delete=<?php echo $row['id']; ?>&exam_id=<?php echo $selected_exam_id; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this question?')" 
                                   class="text-red-500 hover:text-red-700 transition duration-200">
                                   ❌ Delete
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="px-4 py-2 text-center">No questions added yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <?php include('partials/footer.php'); ?>
</body>
</html>