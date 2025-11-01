<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Fetch employee bank details with employee name
$sql = "
    SELECT 
        b.id,
        e.full_name AS employee_name,
        b.bank_name,
        b.account_number,
        b.ifsc_code,
        b.branch_name,
        b.updated_at
    FROM employee_bank_details b
    LEFT JOIN employees e ON b.employee_id = e.id
    ORDER BY b.updated_at DESC
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Employee Bank Details</title>
<style>
body {
    font-family: "Poppins", sans-serif;
    background: #f5f7fa;
    margin: 0;
    padding: 30px;
}
h2 {
    text-align: center;
    color: #007bff;
    margin-bottom: 25px;
}
table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}
th, td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}
th {
    background-color: #212529;
    color: #fff;
    text-transform: uppercase;
    font-size: 14px;
}
tr:hover {
    background-color: #f1f1f1;
}
td {
    color: #333;
    font-size: 15px;
}
.no-data {
    text-align: center;
    color: red;
    font-weight: 600;
    margin-top: 20px;
}
</style>
</head>
<body>
    <h2>Employee Bank Details</h2>

    <table>
        <tr>
            <th>#</th>
            <th>Employee Name</th>
            <th>Bank Name</th>
            <th>Account Number</th>
            <th>IFSC Code</th>
            <th>Branch Name</th>
            <th>Last Updated</th>
        </tr>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php $i = 1; while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($row['employee_name'] ?? 'Unknown') ?></td>
                    <td><?= htmlspecialchars($row['bank_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['account_number'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['ifsc_code'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['branch_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['updated_at'] ?? '-') ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="no-data">No bank details found</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>
<?php $conn->close(); ?>
