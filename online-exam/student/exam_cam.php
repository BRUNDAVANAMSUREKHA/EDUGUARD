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

// Fetch questions
$stmt = $conn->prepare("SELECT * FROM questions WHERE exam_id = ?");
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$questions_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Interface</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script defer src="../assets/js/fullscreen.js"></script>
    <script defer src="../assets/js/camera.js"></script>
    <style>
        body {
            display: flex;
            flex-direction: column;
            height: 100vh;
            margin: 0;
            overflow: hidden;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }
        .header {
            background: #4CAF50;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .main-container {
            display: flex;
            flex: 1;
            overflow: hidden;
        }
        .exam-container {
            flex: 3;
            padding: 20px;
            overflow-y: auto;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .camera-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #f0f0f0;
            padding: 20px;
            border-left: 2px solid #ddd;
        }
        video {
            width: 100%;
            max-width: 400px;
            height: auto;
            border-radius: 10px;
            border: 2px solid #ddd;
        }
        .status-panel {
            margin-top: 20px;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
        }
        .status-item {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .status-ok {
            background-color: green;
        }
        .status-error {
            background-color: red;
        }
        .alert {
            color: red;
            font-weight: bold;
            margin-top: 10px;
        }
        .question {
            margin-bottom: 20px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .question p {
            margin: 0 0 10px 0;
            font-weight: bold;
        }
        .question label {
            display: block;
            margin: 5px 0;
        }
        #timer {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 20px;
        }
        button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }
        button:hover {
            background: #45a049;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .modal-content button {
            margin: 10px;
        }
    </style>
</head>
<body onload="enterFullScreen(); lockScreen(); startCamera(); startTimer(<?php echo $exam['time_limit']; ?>); startDetection();">

    <div class="header">
        Exam: <?php echo htmlspecialchars($exam['exam_name']); ?>
    </div>

    <div class="main-container">
        <div class="exam-container">
            <div id="timer">Time Remaining: <span id="time"></span></div>

            <form id="examForm" action="submit_exam.php" method="POST">
                <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">

                <?php while ($row = $questions_result->fetch_assoc()): ?>
                    <div class="question">
                        <p><?php echo htmlspecialchars($row['question']); ?></p>

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
                <?php endwhile; ?>

                <button type="button" onclick="showConfirmationModal()">Submit Exam</button>
            </form>
        </div>

        <div class="camera-container">
            <video id="camera-feed" autoplay></video>
            <div class="status-panel">
                <h2>Detection Status</h2>
                <div class="status-item">
                    <span class="status-indicator status-ok" id="faceIndicator"></span>
                    Face Detection: <span id="faceStatus">Normal</span>
                </div>
                <div class="status-item">
                    <span class="status-indicator status-ok" id="headIndicator"></span>
                    Head Position: <span id="headStatus">Centered</span>
                </div>
                <div class="status-item">
                    <span class="status-indicator status-ok" id="phoneIndicator"></span>
                    Phone Detection: <span id="phoneStatus">No phone detected</span>
                </div>
                <div class="alert" id="alertMessage"></div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <p>Are you sure you want to submit the exam?</p>
            <button onclick="submitExam()">Yes, Submit</button>
            <button onclick="hideConfirmationModal()">Cancel</button>
        </div>
    </div>

    <!-- Include TensorFlow.js, Mediapipe, and custom monitoring script -->
  <!-- TensorFlow.js -->
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/coco-ssd"></script>

<!-- Mediapipe -->
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_detection/face_detection.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/pose/pose.js"></script>
    <script defer src="../assets/js/fullscreen.js"></script>
    <script defer src="../assets/js/camera.js"></script>
    <script defer src="../assets/js/smart-monitoring.js"></script>
    <script>
        // Select DOM elements
const videoElement = document.getElementById('camera-feed');
const faceStatus = document.getElementById('faceStatus');
const headStatus = document.getElementById('headStatus');
const phoneStatus = document.getElementById('phoneStatus');
const alertMessage = document.getElementById('alertMessage');
const faceIndicator = document.getElementById('faceIndicator');
const headIndicator = document.getElementById('headIndicator');
const phoneIndicator = document.getElementById('phoneIndicator');

let malpracticeScore = 0;
let lookingAwayCount = 0;

// Load TensorFlow.js and Mediapipe models
let faceDetector, poseDetector, cocoSsdModel;

async function loadModels() {
    try {
        // Load COCO-SSD model for phone detection
        console.log('Loading COCO-SSD model...');
        cocoSsdModel = await cocoSsd.load();
        console.log('COCO-SSD model loaded successfully.');

        // Load Mediapipe Face Detection
        console.log('Loading Mediapipe Face Detection model...');
        faceDetector = new FaceDetector({ maxDetectedFaces: 2 });
        console.log('Mediapipe Face Detection model loaded successfully.');

        // Load Pose Detection for head position
        console.log('Loading Mediapipe Pose Detection model...');
        poseDetector = await poseDetection.createDetector(poseDetection.SupportedModels.MoveNet);
        console.log('Mediapipe Pose Detection model loaded successfully.');

    } catch (error) {
        console.error('Error loading models:', error);
        alert('Failed to load detection models. Please refresh the page.');
    }
}

// Start webcam
async function startCamera() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        videoElement.srcObject = stream;
        console.log('Camera access granted.');
    } catch (error) {
        console.error('Error accessing the camera:', error);
        alert('Camera access denied. Please allow camera access to continue the exam.');
    }
}

// Detect malpractice
async function detectCheating() {
    const statusUpdates = {
        face_status: 'Normal',
        head_status: 'Centered',
        phone_status: 'No phone detected',
        malpractice_score: malpracticeScore
    };

    try {
        // Face Detection
        const faces = await faceDetector.detect(videoElement);
        if (faces.length === 0) {
            statusUpdates.face_status = 'No face detected!';
            malpracticeScore += 30;
        } else if (faces.length > 1) {
            statusUpdates.face_status = 'Multiple faces detected!';
            malpracticeScore += 50;
        }

        // Head Position Detection
        const poses = await poseDetector.estimatePoses(videoElement);
        if (poses.length > 0) {
            const nose = poses[0].keypoints.find(kp => kp.name === 'nose');
            if (nose && (nose.x < 0.2 || nose.x > 0.8 || nose.y < 0.2 || nose.y > 0.8)) {
                statusUpdates.head_status = 'Looking away!';
                malpracticeScore += 20;
                lookingAwayCount += 1;
            }
        }

        // Phone Detection
        const predictions = await cocoSsdModel.detect(videoElement);
        const phoneDetected = predictions.some(p => p.class === 'cell phone' && p.score > 0.5);
        if (phoneDetected) {
            statusUpdates.phone_status = 'Phone detected!';
            malpracticeScore += 40;
        }

        // Update malpractice score
        statusUpdates.malpractice_score = malpracticeScore;

        // Auto-submit exam if malpractice score is high
        if (malpracticeScore >= 80) {
            alertMessage.textContent = "Malpractice detected! Exam will be auto-submitted.";
            autoSubmitExam();
        }
    } catch (error) {
        console.error('Error during detection:', error);
    }

    updateStatus(statusUpdates);
}

// Update status indicators in UI
function updateStatus(status) {
    faceStatus.textContent = status.face_status;
    headStatus.textContent = status.head_status;
    phoneStatus.textContent = status.phone_status;

    faceIndicator.className = `status-indicator ${status.face_status === 'Normal' ? 'status-ok' : 'status-error'}`;
    headIndicator.className = `status-indicator ${status.head_status === 'Centered' ? 'status-ok' : 'status-error'}`;
    phoneIndicator.className = `status-indicator ${status.phone_status === 'No phone detected' ? 'status-ok' : 'status-error'}`;
}

// Start detection loop
async function startDetection() {
    await loadModels();
    await startCamera();

    setInterval(detectCheating, 1000); // Run detection every second
}

// Auto-submit the exam
function autoSubmitExam() {
    alert('Malpractice detected! Your exam will be submitted automatically.');
    document.getElementById('examForm').submit();
}

// Ensure fullscreen mode is activated by user gesture
function enterFullScreen() {
    const elem = document.documentElement;
    if (elem.requestFullscreen) {
        elem.requestFullscreen();
    } else if (elem.mozRequestFullScreen) { // Firefox
        elem.mozRequestFullScreen();
    } else if (elem.webkitRequestFullscreen) { // Chrome, Safari, Opera
        elem.webkitRequestFullscreen();
    } else if (elem.msRequestFullscreen) { // IE/Edge
        elem.msRequestFullscreen();
    }
}

// Event listener to ensure fullscreen mode is triggered by user action
document.getElementById("startExamButton").addEventListener("click", () => {
    enterFullScreen();
    startDetection();
});

        
    </script>
</body>
</html>
