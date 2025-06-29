function startCamera() {
    let video = document.getElementById("camera-feed");

    navigator.mediaDevices.getUserMedia({ video: true })
        .then(function(stream) {
            video.srcObject = stream;
        })
        .catch(function(error) {
            alert("❌ Camera access is required for this exam.");
        });
}
