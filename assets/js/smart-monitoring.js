// Import TensorFlow.js, Mediapipe, and other necessary libraries
import * as tf from '@tensorflow/tfjs';
import * as faceDetection from '@mediapipe/face_detection';
import * as poseDetection from '@mediapipe/pose';
import * as faceMesh from '@mediapipe/face_mesh';
import * as gazeEstimation from '@mediapipe/gaze_estimation';
import * as handPoseDetection from '@mediapipe/hands';

// Initialize models
const faceDetector = new faceDetection.FaceDetection({
    locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/face_detection/${file}`,
});
faceDetector.setOptions({
    model: 'short',
    minDetectionConfidence: 0.5,
});

const poseEstimator = new poseDetection.Pose({
    locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/pose/${file}`,
});
poseEstimator.setOptions({
    modelComplexity: 1,
    minDetectionConfidence: 0.5,
    minTrackingConfidence: 0.5,
});

const faceMesher = new faceMesh.FaceMesh({
    locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${file}`,
});
faceMesher.setOptions({
    maxNumFaces: 1,
    minDetectionConfidence: 0.5,
    minTrackingConfidence: 0.5,
});

const gazeEstimator = new gazeEstimation.GazeEstimation({
    locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/gaze_estimation/${file}`,
});
gazeEstimator.setOptions({
    maxNumFaces: 1,
    minDetectionConfidence: 0.5,
    minTrackingConfidence: 0.5,
});

const handDetector = new handPoseDetection.Hands({
    locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/hands/${file}`,
});
handDetector.setOptions({
    maxNumHands: 2,
    minDetectionConfidence: 0.5,
    minTrackingConfidence: 0.5,
});

// Load YOLO model for object detection
const YOLO_MODEL_URL = 'https://path/to/your/yolo/model.json';
let yoloModel;
async function loadYOLOModel() {
    yoloModel = await tf.loadGraphModel(YOLO_MODEL_URL);
    console.log('YOLO model loaded');
}
loadYOLOModel();

// Global variables
let malpracticeScore = 0;
let lookingAwayCount = 0;
let lastPosition = null;
let frameNotDetectedFor = 0;

// Function to detect cheating
async function detectCheating(videoElement) {
    const statusUpdates = {
        faceStatus: 'Normal',
        headStatus: 'Centered',
        phoneStatus: 'No phone detected',
        gazeStatus: 'Looking at screen',
        handStatus: 'No suspicious hand gestures',
        malpracticeScore: malpracticeScore,
    };

    // Face detection
    const faceResults = await faceDetector.detect(videoElement);
    if (faceResults.detections.length > 1) {
        statusUpdates.faceStatus = 'Multiple faces detected!';
        malpracticeScore += 30;
    }

    // Head position detection
    const poseResults = await poseEstimator.detect(videoElement);
    if (poseResults.poseLandmarks) {
        const nose = poseResults.poseLandmarks[poseDetection.POSE_LANDMARKS.NOSE];
        if (nose.x < 0.2 || nose.x > 0.8 || nose.y < 0.2 || nose.y > 0.8) {
            statusUpdates.headStatus = 'Looking away!';
            malpracticeScore += 20;
            lookingAwayCount += 1;
        }
    }

    // Gaze estimation
    const gazeResults = await gazeEstimator.estimateGaze(videoElement);
    if (gazeResults && gazeResults.gazeVector) {
        const gazeVector = gazeResults.gazeVector;
        if (gazeVector[0] < -0.5 || gazeVector[0] > 0.5 || gazeVector[1] < -0.5 || gazeVector[1] > 0.5) {
            statusUpdates.gazeStatus = 'Looking away from screen!';
            malpracticeScore += 15;
        }
    }

    // Hand gesture detection
    const handResults = await handDetector.detect(videoElement);
    if (handResults.multiHandLandmarks) {
        for (const landmarks of handResults.multiHandLandmarks) {
            const thumbTip = landmarks[handPoseDetection.HandLandmark.THUMB_TIP];
            const indexTip = landmarks[handPoseDetection.HandLandmark.INDEX_FINGER_TIP];
            if (Math.abs(thumbTip.x - indexTip.x) < 0.1 && Math.abs(thumbTip.y - indexTip.y) < 0.1) {
                statusUpdates.handStatus = 'Suspicious hand gesture detected!';
                malpracticeScore += 25;
            }
        }
    }

    // Phone detection using YOLO
    if (yoloModel) {
        const input = tf.browser.fromPixels(videoElement).expandDims(0);
        const resizedInput = tf.image.resizeBilinear(input, [416, 416]);
        const predictions = await yoloModel.executeAsync(resizedInput);
        const phoneDetected = processYOLOPredictions(predictions);
        if (phoneDetected) {
            statusUpdates.phoneStatus = 'Phone detected!';
            malpracticeScore += 40;
        }
    }

    // Update malpractice score
    statusUpdates.malpracticeScore = malpracticeScore;

    // Save screenshot if malpractice detected
    if (malpracticeScore >= 90) {
        const canvas = document.createElement('canvas');
        canvas.width = videoElement.videoWidth;
        canvas.height = videoElement.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(videoElement, 0, 0, canvas.width, canvas.height);
        const screenshot = canvas.toDataURL('image/jpeg');
        saveScreenshot(screenshot);
    }

    return statusUpdates;
}

// Function to process YOLO predictions
function processYOLOPredictions(predictions) {
    const CELL_PHONE_CLASS_ID = 67; // Replace with the correct class ID for phones
    const threshold = 0.5;

    for (let i = 0; i < predictions.shape[0]; i++) {
        const classId = predictions[i][5];
        const confidence = predictions[i][4];
        if (classId === CELL_PHONE_CLASS_ID && confidence > threshold) {
            return true;
        }
    }
    return false;
}

// Function to save screenshot
function saveScreenshot(screenshot) {
    const link = document.createElement('a');
    link.href = screenshot;
    link.download = `cheating_proof_${new Date().toISOString()}.jpg`;
    link.click();
}

// WebSocket connection for real-time updates
const socket = new WebSocket('ws://your-server-address:port');

socket.onopen = () => {
    console.log('WebSocket connection established');
};

socket.onmessage = (event) => {
    const data = JSON.parse(event.data);
    console.log('Received data:', data);
};

socket.onclose = () => {
    console.log('WebSocket connection closed');
};

// Start detection loop
async function startDetection() {
    const videoElement = document.getElementById('camera-feed');
    while (true) {
        const statusUpdates = await detectCheating(videoElement);
        socket.send(JSON.stringify(statusUpdates));
        await new Promise((resolve) => setTimeout(resolve, 100)); // Small delay
    }
}

// Start detection when the page loads
window.onload = () => {
    startDetection();
};