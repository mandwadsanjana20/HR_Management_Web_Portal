<?php
session_start();
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "❌ Database connection failed"]);
    exit();
}

if (!isset($_SESSION['employee_id'])) {
    echo json_encode(["status" => "error", "message" => "❌ Not logged in"]);
    exit();
}

$employee_id = $_SESSION['employee_id'];
$date = date("Y-m-d");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Check-in
    if ($action === 'checkin') {
        $check = $conn->prepare("SELECT id FROM attendance WHERE employee_id=? AND date=?");
        $check->bind_param("is", $employee_id, $date);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(["status" => "error", "message" => "⚠️ Already checked in today"]);
        } else {
            $stmt = $conn->prepare("INSERT INTO attendance (employee_id, date, check_in_time) VALUES (?, ?, NOW())");
            $stmt->bind_param("is", $employee_id, $date);
            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "✅ Checked in successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "❌ Failed to check in"]);
            }
        }
        exit();
    }

    // Check-out
    if ($action === 'checkout') {
        $check = $conn->prepare("SELECT check_in_time FROM attendance WHERE employee_id=? AND date=? AND check_out_time IS NULL");
        $check->bind_param("is", $employee_id, $date);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows === 0) {
            echo json_encode(["status" => "error", "message" => "⚠️ No active check-in found"]);
        } else {
            $row = $result->fetch_assoc();
            $checkInTimestamp = strtotime($row['check_in_time']);
            $nowTimestamp = time();
            $hoursPassed = ($nowTimestamp - $checkInTimestamp) / 3600;

            if ($hoursPassed < 5) {
                echo json_encode(["status" => "error", "message" => "⚠️ You can checkout only after 5 hours"]);
            } else {
                $stmt = $conn->prepare("UPDATE attendance SET check_out_time=NOW() WHERE employee_id=? AND date=?");
                $stmt->bind_param("is", $employee_id, $date);
                if ($stmt->execute()) {
                    echo json_encode(["status" => "success", "message" => "✅ Checked out successfully"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "❌ Failed to check out"]);
                }
            }
        }
        exit();
    }

    echo json_encode(["status" => "error", "message" => "❌ Invalid action"]);
}
