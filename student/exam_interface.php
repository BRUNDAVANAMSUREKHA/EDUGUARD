<?php
session_start();
if ($_SESSION['role'] !== 'student') {
    header('Location: ../index.php');
    exit();
}

include '../config/database.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("❌ Error: Exam ID is missing!");
}
$exam_id = (int) $_GET['id'];

// Fetch exam details
$stmt = $conn->prepare("SELECT * FROM exams WHERE id = ?");
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$result = $stmt->get_result();
$exam = $result->fetch_assoc();

if (!$exam) {
    die("❌ Error: Exam not found in the database!");
}

// Fetch questions
$stmt = $conn->prepare("SELECT * FROM questions WHERE exam_id = ?");
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$questions_result = $stmt->get_result();

if ($questions_result->num_rows === 0) {
    die("❌ Error: No questions found for this exam!");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exam Interface</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .exam-container {
            flex: 1;
            padding: 20px;
            background-color: #fff;
            overflow-y: auto;
        }

        .camera-container {
            width: 300px;
            background-color: #2c3e50;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #fff;
        }

        h1 {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        #timer {
            font-size: 18px;
            font-weight: 500;
            color: #e74c3c;
            margin-bottom: 20px;
        }

        .question {
            margin-bottom: 30px;
        }

        .question p {
            font-size: 18px;
            font-weight: 500;
            color: #34495e;
            margin-bottom: 10px;
        }

        label {
            display: block;
            margin: 10px 0;
            font-size: 16px;
            color: #34495e;
        }

        input[type="radio"],
        input[type="checkbox"] {
            margin-right: 10px;
        }

        textarea {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: vertical;
        }

        button[type="submit"] {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button[type="submit"]:hover {
            background-color: #2980b9;
        }

        video {
            width: 100%;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .status-panel {
            background-color: #34495e;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
        }

        .status-item {
            margin-bottom: 10px;
            font-size: 14px;
        }

        .status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .status-ok {
            background-color: #2ecc71;
        }

        .status-error {
            background-color: #e74c3c;
        }

        .alert {
            color: #e74c3c;
            font-weight: bold;
        }

        .progress-bar {
            width: 100%;
            background-color: #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .progress {
            height: 10px;
            background-color: #3498db;
            border-radius: 5px;
            transition: width 0.3s;
        }

        .disabled-form {
            opacity: 0.5;
            pointer-events: none;
        }
    </style>
</head>
<body onload="enterFullScreen(); lockScreen(); startCamera(); startTimer(<?php echo $exam['time_limit']; ?>); startDetection();">

    <div class="exam-container">
        <h1>Exam: <?php echo htmlspecialchars($exam['exam_name']); ?></h1>
        <div id="timer">Time Remaining: <span id="time"></span></div>

        <!-- Progress Bar -->
        <div class="progress-bar">
            <div class="progress" id="progress"></div>
        </div>

        <form id="examForm" action="submit_exam.php" method="POST" class="disabled-form">
            <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">

            <?php
            $totalQuestions = $questions_result->num_rows;
            $currentQuestion = 0;
            while ($row = $questions_result->fetch_assoc()):
                $currentQuestion++;
                $progress = ($currentQuestion / $totalQuestions) * 100;
            ?>
                <div class="question">
                    <p>Question <?php echo $currentQuestion; ?>: <?php echo htmlspecialchars($row['question']); ?></p>

                    <?php 
                    $options = json_decode($row['options'], true);
                    if ($options === null && json_last_error() !== JSON_ERROR_NONE) {
                        $options = [];
                    }

                    if ($row['question_type'] === 'single_choice'): ?>
                        <?php foreach ($options as $option): ?>
                            <label>
                                <input type="radio" name="question_<?php echo $row['id']; ?>" value="<?php echo htmlspecialchars($option); ?>">
                                <?php echo htmlspecialchars($option); ?>
                            </label>
                        <?php endforeach; ?>

                    <?php elseif ($row['question_type'] === 'multiple_choice'): ?>
                        <?php foreach ($options as $option): ?>
                            <label>
                                <input type="checkbox" name="question_<?php echo $row['id']; ?>[]" value="<?php echo htmlspecialchars($option); ?>">
                                <?php echo htmlspecialchars($option); ?>
                            </label>
                        <?php endforeach; ?>

                    <?php elseif ($row['question_type'] === 'short_answer'): ?>
                        <input type="text" name="question_<?php echo $row['id']; ?>">

                    <?php elseif ($row['question_type'] === 'long_answer'): ?>
                        <textarea name="question_<?php echo $row['id']; ?>"></textarea>
                    <?php endif; ?>

                </div>
                <script>
                    document.getElementById('progress').style.width = '<?php echo $progress; ?>%';
                </script>
            <?php endwhile; ?>

            <button type="submit" id="submitButton" disabled onclick="return confirmSubmit();">Submit Exam</button>
        </form>
    </div>

    <!-- Camera Feed -->
    <div class="camera-container">
        <h2>Camera Feed</h2>
        <video id="camera-feed" autoplay></video>
        <div class="status-panel">
            <div class="status-item">
                <span class="status-indicator status-ok"></span>
                Camera Active
            </div>
            <div class="status-item">
                <span class="status-indicator status-ok"></span>
                Full Screen Enabled
            </div>
            <div class="status-item">
                <span class="status-indicator status-ok"></span>
                Malpractice Score: <span id="score">0</span>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/ml5@0.12.2/dist/ml5.min.js" type="text/javascript"></script>
    <script>
        let modelIsLoaded = false;
        let score = 0;
        let detectionActive = true;
        let fullScreenCloseCount = 0;

        // Initialize object detector
        const objectDetector = ml5.objectDetector('cocossd', {}, () => {
            console.log("Model Loaded!");
            modelIsLoaded = true;
            document.getElementById("examForm").classList.remove("disabled-form");
            document.getElementById("submitButton").disabled = false;
            detectObjects(); // Start detection once the model is loaded
        });

        const video = document.getElementById("camera-feed");
        const scoreElement = document.getElementById("score");

        // Access camera
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                video.srcObject = stream;
                video.onloadedmetadata = () => {
                    video.play();
                };
            })
            .catch(err => {
                console.error("Error accessing camera: ", err);
                alert("Camera access is required. Please allow camera permissions.");
            });

        // Object detection function
        function detectObjects() {
            if (!modelIsLoaded || !detectionActive) return;

            objectDetector.detect(video, (err, results) => {
                if (err) {
                    console.error("Detection error: ", err);
                    return;
                }

                let personDetected = false;
                let gadgetsDetected = [];

                results.forEach(element => {
                    if (element.label === "person") {
                        personDetected = true;
                    } else if (["cell phone", "headphones", "earbuds", "laptop", "keyboard", "mouse"].includes(element.label)) {
                        gadgetsDetected.push(element.label);
                    }
                });

                if (!personDetected) {
                    alert("Please ensure you are visible in the camera.");
                }

                if (personDetected && gadgetsDetected.length > 0) {
                    score += gadgetsDetected.length * 5;
                    scoreElement.innerText = score;

                    if (score >= 70) {
                        detectionActive = false;
                        alert("Multiple malpractice detected. Exam will be submitted.");
                        document.getElementById("examForm").submit();
                    }
                }
            });

            // Continuously detect objects
            setTimeout(detectObjects, 1000 / 60);
        }

        // Full-screen handling
        function enterFullScreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.error("Error attempting to enable full-screen mode: ", err);
                });
            }
        }

        function handleFullScreenChange() {
            if (!document.fullscreenElement) {
                fullScreenCloseCount++;
                if (fullScreenCloseCount >= 3) {
                    alert("Full screen closed multiple times. Exam will be submitted.");
                    document.getElementById("examForm").submit();
                } else {
                    enterFullScreen();
                }
            }
        }

        document.addEventListener('fullscreenchange', handleFullScreenChange);
        document.addEventListener('webkitfullscreenchange', handleFullScreenChange);
        document.addEventListener('mozfullscreenchange', handleFullScreenChange);
        document.addEventListener('MSFullscreenChange', handleFullScreenChange);

        // Enter full-screen mode on page load
        enterFullScreen();
    </script>
</body>
</html>