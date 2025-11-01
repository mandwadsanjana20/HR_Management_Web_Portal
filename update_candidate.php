<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $application_id = intval($_POST['application_id']);
    $status = $_POST['status'];
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : null;

    if ($status === 'Rejected' && empty($reason)) {
        echo "Rejection reason is required.";
        exit();
    }

    if ($status === 'Rejected') {
        $stmt = $conn->prepare("UPDATE job_applications SET status = ?, reason = ? WHERE id = ?");
        $stmt->bind_param("ssi", $status, $reason, $application_id);
    } else {
        $stmt = $conn->prepare("UPDATE job_applications SET status = ?, reason = NULL WHERE id = ?");
        $stmt->bind_param("si", $status, $application_id);
    }

    if ($stmt->execute()) {
        echo "OK";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
