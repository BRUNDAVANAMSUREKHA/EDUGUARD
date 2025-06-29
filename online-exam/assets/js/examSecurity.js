let exitCount = 0;

function startExam() {
    startWebcam();
    goFullScreen();

    // Prevent tab switch
    document.addEventListener("visibilitychange", function() {
        if (document.hidden) {
            exitCount++;
            alert("⚠️ Warning: Tab switching detected! (" + exitCount + "/2)");
            if (exitCount >= 2) {
                alert("❌ Exam auto-submitted due to rule violation.");
                document.getElementById("examForm").submit();
            }
        }
    });

    // Prevent copy-paste
    document.addEventListener("copy", (event) => {
        event.preventDefault();
        alert("❌ Copying is disabled!");
    });
    document.addEventListener("paste", (event) => {
        event.preventDefault();
        alert("❌ Pasting is disabled!");
    });

    // Detect fullscreen exit
    document.addEventListener("fullscreenchange", checkFullScreen);
    document.addEventListener("mozfullscreenchange", checkFullScreen);
    document.addEventListener("webkitfullscreenchange", checkFullScreen);
    document.addEventListener("msfullscreenchange", checkFullScreen);
}

function startWebcam() {
    const video = document.getElementById("webcam");
    navigator.mediaDevices.getUserMedia({ video: true })
        .then((stream) => {
            video.srcObject = stream;
        })
        .catch((error) => {
            alert("❌ Webcam access is required for this exam!");
        });
}

function goFullScreen() {
    let elem = document.documentElement;
    if (elem.requestFullscreen) {
        elem.requestFullscreen();
    } else if (elem.mozRequestFullScreen) {
        elem.mozRequestFullScreen();
    } else if (elem.webkitRequestFullscreen) {
        elem.webkitRequestFullscreen();
    } else if (elem.msRequestFullscreen) {
        elem.msRequestFullscreen();
    }
}

function checkFullScreen() {
    if (!document.fullscreenElement && !document.mozFullScreenElement && 
        !document.webkitFullscreenElement && !document.msFullscreenElement) {
        exitCount++;
        alert("⚠️ Warning: Full-screen mode is required! (" + exitCount + "/2)");
        goFullScreen();

        if (exitCount >= 2) {
            alert("❌ Exam auto-submitted due to rule violation.");
            document.getElementById("examForm").submit();
        }
    }
}
