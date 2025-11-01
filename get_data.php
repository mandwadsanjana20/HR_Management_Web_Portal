<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "company_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die(json_encode(["error" => "Database connection failed."]));
}

if (!isset($_SESSION['employee_id'])) {
  echo json_encode(["error" => "Employee not logged in."]);
  exit;
}

$employee_id = $_SESSION['employee_id'];

/* ==========================================================
   ✅ 1. Fetch Projects (Progress per Project)
========================================================== */
$sql = "SELECT project_name, progress FROM projects WHERE employee_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

$projects = [];
while ($row = $result->fetch_assoc()) {
  $projects[] = $row;
}
$stmt->close();

/* ==========================================================
   ✅ 2. Fetch Attendance Summary (Present vs Absent)
========================================================== */
// Count present days
$sql_present = "SELECT COUNT(*) AS present_days FROM attendance WHERE employee_id = ?";
$stmt2 = $conn->prepare($sql_present);
$stmt2->bind_param("i", $employee_id);
$stmt2->execute();
$res = $stmt2->get_result()->fetch_assoc();
$present_days = $res['present_days'] ?? 0;
$stmt2->close();

// Total working days in current month
$currentMonth = date('m');
$currentYear  = date('Y');
$total_days   = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
$absent_days  = $total_days - $present_days;

// Month name
$month_name = date('F Y');

/* ==========================================================
   ✅ Final JSON Response
========================================================== */
echo json_encode([
  "projects" => $projects,
  "attendance" => [
    "present" => $present_days,
    "absent" => $absent_days,
    "month" => $month_name
  ]
]);

$conn->close();
?>
