let tabSwitchCount = 0;

function startExam() {
    // Enter full-screen mode
    document.documentElement.requestFullscreen();

    // Disable right-click and copy
    document.addEventListener('contextmenu', (e) => e.preventDefault());
    document.addEventListener('copy', (e) => e.preventDefault());

    // Timer
    const endTime = new Date("<?php echo $exam['end_time']; ?>").getTime();
    const timer = setInterval(() => {
        const now = new Date().getTime();
        const timeRemaining = endTime - now;
        if (timeRemaining <= 0) {
            clearInterval(timer);
            document.getElementById('examForm').submit();
        } else {
            const hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);
            document.getElementById('time').innerText = `${hours}h ${minutes}m ${seconds}s`;
        }
    }, 1000);

    // Prevent tab switching
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'hidden') {
            tabSwitchCount++;
            if (tabSwitchCount >= 3) {
                alert('You have switched tabs too many times. Your exam will be submitted automatically.');
                document.getElementById('examForm').submit();
            }
        }
    });
}