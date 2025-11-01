<?php
session_start();
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "company_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Check if employee logged in
if (!isset($_SESSION['employee_id'])) {
    echo "<h3>Please login as Employee.</h3>";
    exit();
}

$employee_id = $_SESSION['employee_id'];

// ✅ Fetch payroll + employee name + job title
$query = "
SELECT p.*, e.full_name, j.title AS job_title
FROM payroll p
JOIN employees e ON e.id = p.employee_id
LEFT JOIN jobs j ON e.job_id = j.job_id
WHERE p.employee_id = $employee_id
ORDER BY p.generated_at DESC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payroll History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="text-center mb-4">My Payroll History</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Job Title</th>
                    <th>Basic Salary</th>
                    <th>Allowance</th>
                    <th>Deduction</th>
                    <th>Total</th>
                    <th>Generated Date</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1;
                while ($row = $result->fetch_assoc()): 
                    $full_name = $row['full_name'] ?? '';
                    $job_title = $row['job_title'] ?? '';
                ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($full_name) ?></td>
                        <td><?= htmlspecialchars($job_title) ?></td>
                        <td>₹<?= number_format($row['basic_salary'], 2) ?></td>
                        <td>₹<?= number_format($row['allowance'], 2) ?></td>
                        <td>₹<?= number_format($row['deduction'], 2) ?></td>
                        <td><strong>₹<?= number_format($row['total'], 2) ?></strong></td>
                        <td><?= date('d M Y', strtotime($row['generated_at'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning text-center">No payroll history found.</div>
    <?php endif; ?>
</div>
</body>
</html>
