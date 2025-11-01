<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_id = intval($_POST['application_id']);
    $date = $_POST['interview_date'];
    $time = $_POST['interview_time'];

    // ✅ Generate Google Meet link
    $meet_link = "https://meet.google.com/" . substr(md5(uniqid()), 0, 10);

    $updateSql = "UPDATE job_applications SET interview_date=?, interview_time=?, meet_link=? WHERE id=?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("sssi", $date, $time, $meet_link, $application_id);

    if ($stmt->execute()) {
        echo "<script>
            alert('✅ Interview letter sent successfully!');
            window.location.href = 'interview.php'; 
        </script>";
    } else {
        echo "Error updating record.";
    }
}
$conn->close();
?>