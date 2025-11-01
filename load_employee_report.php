<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "company_db";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$emp_id = intval($_GET['id']);
if (!$emp_id) {
    echo json_encode(["error" => "Invalid employee ID"]);
    exit();
}

// ✅ Fetch employee name
$empQuery = "SELECT full_name FROM employees WHERE id = $emp_id";
$empRow = $conn->query($empQuery)->fetch_assoc();
$empName = $empRow ? $empRow['full_name'] : "Unknown";

// ✅ Fetch payroll data
$query = "
    SELECT 
        DATE_FORMAT(generated_at, '%b %Y') AS month,
        basic_salary, allowance, deduction, total, comment
    FROM payroll
    WHERE employee_id = $emp_id
    ORDER BY generated_at ASC
";
$result = $conn->query($query);
$payroll = [];
while($row = $result->fetch_assoc()) {
    $payroll[] = $row;
}

echo json_encode([
    "employee_name" => $empName,
    "payroll" => $payroll
]);
?>
