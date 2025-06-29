<?php
session_start();
if ($_SESSION['role'] !== 'student') {
    header('Location: ../index.php');
    exit();
}

// ✅ Ensure exam_id exists
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("❌ Error: Exam ID is missing!");
}
$exam_id = (int) $_GET['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exam Instructions</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        /* Center the UI */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            flex-direction: column;
        }

        h1 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 1rem;
        }

        p {
            font-size: 1.2rem;
            color: #555;
            margin-bottom: 2rem;
            text-align: center;
            max-width: 600px;
        }

        /* Style the button */
        #startExam {
            background-color: #4CAF50; /* Green */
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 1.2rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #startExam:hover {
            background-color: #45a049; /* Darker green on hover */
        }

        /* Camera Feed (Hidden initially) */
        #camera-container {
            display: none;
            margin-top: 20px;
        }

        #camera-feed {
            width: 100%;
            max-width: 400px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body onload="lockScreen();">

    <h1>Exam Instructions</h1>
    <p>Please read the instructions carefully before starting the exam. Ensure you have a stable internet connection and a working camera. The exam will begin once you click the "Start Exam" button.</p>

    <!-- Camera Feed (Hidden initially) -->
    <div id="camera-container">
        <video id="camera-feed" autoplay></video>
    </div>

    <button id="startExam">Start Exam</button>

    <script>
        document.getElementById("startExam").addEventListener("click", async function() {
            try {
                // Request camera access
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                const cameraFeed = document.getElementById("camera-feed");
                cameraFeed.srcObject = stream;

                // Show the camera feed
                document.getElementById("camera-container").style.display = "block";

                // Request full-screen mode
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen();
                } else if (document.documentElement.mozRequestFullScreen) { // Firefox
                    document.documentElement.mozRequestFullScreen();
                } else if (document.documentElement.webkitRequestFullscreen) { // Chrome, Safari, and Opera
                    document.documentElement.webkitRequestFullscreen();
                } else if (document.documentElement.msRequestFullscreen) { // IE/Edge
                    document.documentElement.msRequestFullscreen();
                }

                // Redirect to exam interface after a short delay
                setTimeout(() => {
                    window.location.href = "exam_interface.php?id=<?php echo $exam_id; ?>";
                }, 1000);  // Small delay to ensure full-screen activation
            } catch (error) {
                alert("Camera access is required to start the exam. Please allow camera permissions.");
            }
        });
    </script>

</body>
</html>