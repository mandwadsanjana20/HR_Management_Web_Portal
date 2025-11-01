<?php
$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'] ?? 0;

// Fetch payslip details with employee name and job title
$sql = "
    SELECT 
    p.*, 
    e.full_name AS employee_name, 
    j.title AS job_title
    FROM payroll p
    JOIN employees e ON p.employee_id = e.id
    LEFT JOIN jobs j ON e.job_id = j.job_id
    WHERE p.id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$p = $result->fetch_assoc();
$stmt->close();
$conn->close();

// If no record found
if (!$p) {
    die('<h2 style="color:red;text-align:center;margin-top:50px;">⚠️ Payslip not found!</h2>');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payslip - <?= htmlspecialchars($p['employee_name'] ?? 'Employee') ?></title>
<style>
body {
    font-family: "Poppins", sans-serif;
    background: #f5f7fa;
    margin: 0;
    padding: 40px;
}
.container {
    background: #fff;
    padding: 40px 60px;
    max-width: 850px;
    margin: auto;
    border-radius: 12px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.1);
}
.header {
    text-align: center;
    border-bottom: 3px solid #007bff;
    padding-bottom: 10px;
    margin-bottom: 30px;
}
.header h1 {
    margin: 0;
    font-size: 28px;
    color: #007bff;
}
.header p {
    margin: 5px;
    color: #555;
}
.title {
    text-align: center;
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 10px;
}
.sub-title {
    text-align: center;
    color: #555;
    margin-bottom: 25px;
}
.info-section {
    margin-bottom: 25px;
}
.info-section p {
    font-size: 15px;
    margin: 5px 0;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}
th, td {
    border: 1px solid #ddd;
    padding: 10px 15px;
    font-size: 15px;
}
th {
    background-color: #007bff;
    color: #fff;
    text-align: left;
}
.total-row th, .total-row td {
    font-weight: bold;
    background: #f2f2f2;
}
.netpay-section {
    margin-top: 20px;
    background: #e9f3ff;
    padding: 15px;
    border-radius: 6px;
    font-size: 17px;
}
.footer {
    text-align: center;
    margin-top: 40px;
    color: #555;
    font-size: 14px;
}
button {
    margin-top: 25px;
    padding: 10px 20px;
    border: none;
    background: #007bff;
    color: white;
    border-radius: 6px;
    cursor: pointer;
}
button:hover {
    background: #0056b3;
}
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="tech.jpg" alt="Logo" height="80" width="150">
        <p>Unit 4, Holler Tower, San Diego, CA 92101</p>
        <p>inquire@smithbrand.mail</p>
    </div>

    <div class="title">PAYSLIP</div>
    <div class="sub-title">
        Pay Period: <?= htmlspecialchars(date("F d, Y", strtotime($p['generated_at'] ?? date('Y-m-d')))) ?> - <?= date("F d, Y") ?>
    </div>

    <div class="info-section">
        <p><strong>Employee Name:</strong> <?= htmlspecialchars($p['employee_name'] ?? '') ?></p>
        <p><strong>Employee ID:</strong> <?= htmlspecialchars($p['employee_id'] ?? '') ?></p>
        <p><strong>Job Title:</strong> <?= htmlspecialchars($p['job_title'] ?? 'N/A') ?></p>
    </div>

    <table>
        <tr>
            <th>Earnings</th>
            <th>Amount (₹)</th>
            <th>Deductions</th>
            <th>Amount (₹)</th>
        </tr>
        <tr>
            <td>Base Salary</td>
            <td><?= number_format((float)($p['basic_salary'] ?? 0), 2) ?></td>
            <td>Taxes</td>
            <td><?= number_format((float)(($p['deduction'] ?? 0) / 2), 2) ?></td>
        </tr>
        <tr>
            <td>Allowance</td>
            <td><?= number_format((float)($p['allowance'] ?? 0), 2) ?></td>
            <td>Other Deductions</td>
            <td><?= number_format((float)(($p['deduction'] ?? 0) / 2), 2) ?></td>
        </tr>
        <tr class="total-row">
            <td>Total Earnings</td>
            <td><?= number_format((float)(($p['basic_salary'] ?? 0) + ($p['allowance'] ?? 0)), 2) ?></td>
            <td>Total Deductions</td>
            <td><?= number_format((float)($p['deduction'] ?? 0), 2) ?></td>
        </tr>
    </table>

    <div class="netpay-section">
        <strong>Net Pay:</strong> ₹<?= number_format((float)($p['total'] ?? 0), 2) ?>
    </div>

    <button onclick="window.print()">🖨️ Print Payslip</button>

    <div class="footer">
        If you need further assistance, please contact HR at <strong>inquire@smithbrand.mail</strong>.
    </div>
</div>
</body>
</html>
