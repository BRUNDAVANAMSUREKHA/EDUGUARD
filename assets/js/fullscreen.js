let exitCount = 0;

document.addEventListener("DOMContentLoaded", function() {
    lockScreen();

    // Prevent ESC key from exiting full-screen
    document.addEventListener("keydown", function(event) {
        if (event.key === "Escape") {
            event.preventDefault();
            alert("⚠️ Full-screen mode is required for this exam!");
        }
    });

    // Prevent tab switching
    document.addEventListener("visibilitychange", function() {
        if (document.hidden) {
            exitCount++;
            alert("⚠️ Warning: Tab switching is not allowed! (" + exitCount + "/2)");
            goFullScreen();

            if (exitCount >= 2) {
                alert("❌ Exam auto-submitted due to rule violation.");
                document.getElementById("examForm").submit();
            }
        }
    });

    // Detect full-screen exit and force back
    document.addEventListener("fullscreenchange", checkFullScreen);
    document.addEventListener("mozfullscreenchange", checkFullScreen);
    document.addEventListener("webkitfullscreenchange", checkFullScreen);
    document.addEventListener("msfullscreenchange", checkFullScreen);
});

// Force Full-Screen Mode
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

// Lock Full-Screen Mode
function lockScreen() {
    goFullScreen();

    window.addEventListener("keydown", function(event) {
        if (event.key === "Escape") {
            event.preventDefault();
            alert("⚠️ You cannot exit full-screen mode during the exam!");
        }
    });

    document.addEventListener("fullscreenchange", checkFullScreen);
}

// If Full-Screen is Exited, Auto-Submit Exam
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

// Enable Camera
function startCamera() {
    let video = document.getElementById("camera-feed");
    document.getElementById("camera-container").style.display = "block";
    
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(function(stream) {
            video.srcObject = stream;
        })
        .catch(function(error) {
            alert("❌ Camera access is required for this exam.");
        });
}
