function deleteExam(examId) {
    if (confirm("Are you sure you want to delete this exam?")) {
        fetch('delete_exam.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'exam_id=' + examId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('exam-' + examId).remove();
                alert("✅ Exam deleted successfully!");
            } else {
                alert("❌ Error: " + data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    }
}
