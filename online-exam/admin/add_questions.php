<?php
include('../config/session.php');
include('../config/database.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = $_POST['course_id'];
    $question_type = $_POST['question_type'];
    $question = $_POST['question'];
    $options = isset($_POST['options']) ? json_encode($_POST['options']) : null;
    $correct_answers = isset($_POST['correct']) ? json_encode($_POST['correct']) : null;

    if (($question_type === 'multiple_choice' || $question_type === 'single_choice') && empty($options)) {
        $error = "Options are required for Multiple Choice and Single Choice questions.";
    } else {
        $sql = "INSERT INTO questions (course_id, question_type, question, options, answer) VALUES ('$course_id', '$question_type', '$question', '$options', '$correct_answers')";
        if ($conn->query($sql) === TRUE) {
            $success = "Question added successfully";
        } else {
            $error = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Handle question deletion
if (isset($_GET['delete'])) {
    $question_id = $_GET['delete'];
    $sql = "DELETE FROM questions WHERE id='$question_id'";
    if ($conn->query($sql) === TRUE) {
        $success = "Question deleted successfully";
    } else {
        $error = "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch courses and questions
$sql_courses = "SELECT * FROM courses";
$result_courses = $conn->query($sql_courses);

$sql_questions = "SELECT * FROM questions";
$result_questions = $conn->query($sql_questions);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Questions</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <script src="../assets/js/scripts.js"></script>
</head>
<body>
    <?php include('partials/header.php'); ?>
    <?php include('partials/sidebar.php'); ?>
    <div class="main-content">
        <h1>Add Questions</h1>
        <?php if (isset($success)) { echo "<p class='success'>$success</p>"; } ?>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form method="post" action="add_questions.php" id="question_form">
            <select name="course_id" required>
                <option value="">Select Course</option>
                <?php while ($row = $result_courses->fetch_assoc()) { ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['course_name']; ?></option>
                <?php } ?>
            </select>
            <select name="question_type" id="question_type" required onchange="toggleOptions()">
                <option value="multiple_choice">Multiple Choice (Checkbox)</option>
                <option value="single_choice">Single Choice (Radio Button)</option>
                <option value="short_answer">Short Answer (Text Input)</option>
                <option value="long_answer">Long Answer (Textarea)</option>
            </select>
            <textarea name="question" placeholder="Question" required></textarea>
            <div id="options_container" style="display: none;">
                <div id="options">
                    <div class="option">
                        <input type="text" name="options[]" placeholder="Option" required>
                        <input type="checkbox" name="correct[]" value="0"> Select Correct Answer
                    </div>
                </div>
                <button type="button" onclick="addOption()">Add Option</button>
            </div>
            <textarea name="answer" id="answer" placeholder="Answer" required style="display: none;"></textarea>
            <button type="submit">Add Question</button>
        </form>
        
        <h2>List of Added Questions</h2>
        <table>
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Question Type</th>
                    <th>Question</th>
                    <th>Options</th>
                    <th>Correct Answer</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_questions->fetch_assoc()) { 
                    $course_id = $row['course_id'];
                    $course_name = $conn->query("SELECT course_name FROM courses WHERE id='$course_id'")->fetch_assoc()['course_name'];
                    $options = json_decode($row['options'], true);
                    $correct_answers = json_decode($row['answer'], true);
                ?>
                <tr>
                    <td><?php echo $course_name; ?></td>
                    <td><?php echo $row['question_type']; ?></td>
                    <td><?php echo $row['question']; ?></td>
                    <td>
                        <?php if ($options) {
                            foreach ($options as $key => $option) {
                                echo $option . "<br>";
                            }
                        } ?>
                    </td>
                    <td>
                        <?php if ($correct_answers) {
                            foreach ($correct_answers as $correct) {
                                echo $correct . "<br>";
                            }
                        } ?>
                    </td>
                    <td><a href="add_questions.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this question?')">Delete</a></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php include('partials/footer.php'); ?>
</body>
</html>

<script>
function toggleOptions() {
    const questionType = document.getElementById('question_type').value;
    const optionsContainer = document.getElementById('options_container');
    const answer = document.getElementById('answer');
    if (questionType === 'multiple_choice' || questionType === 'single_choice') {
        optionsContainer.style.display = 'block';
        answer.style.display = 'none';
    } else {
        optionsContainer.style.display = 'none';
        answer.style.display = 'block';
    }
    resetOptions();
}

function addOption() {
    const optionsDiv = document.getElementById('options');
    const optionDiv = document.createElement('div');
    optionDiv.classList.add('option');
    optionDiv.innerHTML = `
        <input type="text" name="options[]" placeholder="Option" required>
        <input type="checkbox" name="correct[]" value="0"> Select Correct Answer
    `;
    optionsDiv.appendChild(optionDiv);
}

function resetOptions() {
    const optionsDiv = document.getElementById('options');
    optionsDiv.innerHTML = `
        <div class="option">
            <input type="text" name="options[]" placeholder="Option" required>
            <input type="checkbox" name="correct[]" value="0"> Select Correct Answer
        </div>
    `;
}
</script>