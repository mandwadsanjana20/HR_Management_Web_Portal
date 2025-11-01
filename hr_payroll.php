<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "company_db";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$message = "";

// ✅ Handle Update (only updates basic salary, allowance & deduction)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_salary'])) {
    $employee_id = $_POST['employee_id'];
    $basic_salary = floatval($_POST['basic_salary']);
    $allowance = floatval($_POST['allowance']);
    $deduction = floatval($_POST['deduction']);

    $total = ($basic_salary + $allowance) - $deduction;

    // Update or insert payroll
    $check = $conn->query("SELECT id FROM payroll WHERE employee_id='$employee_id'");
    if ($check->num_rows > 0) {
        $conn->query("UPDATE payroll SET basic_salary='$basic_salary', allowance='$allowance', deduction='$deduction', total='$total' WHERE employee_id='$employee_id'");
    } else {
        $conn->query("INSERT INTO payroll (employee_id, basic_salary, allowance, deduction, total) VALUES ('$employee_id', '$basic_salary', '$allowance', '$deduction', '$total')");
    }

    $message = "<div class='success'>✅ Payroll updated successfully for Employee ID: $employee_id</div>";
}

// ✅ Handle Generate Payslips
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['generate_all'])) {
    $job_id = $_POST['job_id'];

    $sql = "
        SELECT 
            e.full_name, e.email, j.title,
            COALESCE(p.basic_salary, 0) AS basic_salary,
            COALESCE(p.allowance, 0) AS allowance,
            COALESCE(p.deduction, 0) AS deduction,
            COALESCE(p.total, 0) AS total,
            b.bank_name, b.account_number, b.ifsc_code
        FROM employees e
        LEFT JOIN jobs j ON e.job_id = j.job_id
        LEFT JOIN payroll p ON e.id = p.employee_id
        LEFT JOIN employee_bank_details b ON e.id = b.employee_id
        WHERE b.bank_name IS NOT NULL AND b.account_number IS NOT NULL
    ";

    if (!empty($job_id)) {
        $sql .= " AND e.job_id = '" . $conn->real_escape_string($job_id) . "'";
    }

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $to = $row['email'];
            $subject = "Payslip - {$row['title']} | {$row['full_name']}";

            $message_html = "
            <html><head><style>
                body { font-family: Arial; }
                .box { border:1px solid #ccc; padding:15px; background:#f9f9f9; border-radius:8px; }
                .amount { font-weight:bold; color:#007bff; }
            </style></head><body>
            <div class='box'>
                <h3>Company Payroll Payslip</h3>
                <p><strong>Name:</strong> {$row['full_name']}</p>
                <p><strong>Job Title:</strong> {$row['title']}</p>
                <p><strong>Basic Salary:</strong> ₹{$row['basic_salary']}</p>
                <p><strong>Allowance:</strong> ₹{$row['allowance']}</p>
                <p><strong>Deduction:</strong> ₹{$row['deduction']}</p>
                <p><strong>Total Salary:</strong> <span class='amount'>₹{$row['total']}</span></p>
                <hr>
                <p><strong>Bank:</strong> {$row['bank_name']} | <strong>A/C:</strong> {$row['account_number']} | <strong>IFSC:</strong> {$row['ifsc_code']}</p>
                <p>This is an auto-generated payslip.</p>
            </div></body></html>";

            // Uncomment when email setup is ready:
            // mail($to, $subject, $message_html, "Content-Type: text/html; charset=UTF-8\r\nFrom: hr@company.com");
        }

        $message = "<div class='success'>✅ Payslips generated successfully for all employees with bank details!</div>";
    } else {
        $message = "<div class='warning'>⚠️ No employees with valid bank details found.</div>";
    }
}

// Job filter
$selected_job = isset($_GET['job_id']) ? trim($_GET['job_id']) : '';
$job_query = "SELECT job_id, title FROM jobs";
$jobs_result = $conn->query($job_query);

$sql = "
    SELECT 
        e.id AS employee_id,
        e.full_name,
        e.email,
        j.title AS job_title,
        COALESCE(p.basic_salary, 0) AS basic_salary,
        COALESCE(p.allowance, 0) AS allowance,
        COALESCE(p.deduction, 0) AS deduction,
        COALESCE(p.total, 0) AS total,
        COALESCE(b.bank_name, '') AS bank_name,
        COALESCE(b.account_number, '') AS account_number,
        COALESCE(b.ifsc_code, '') AS ifsc_code
    FROM employees e
    LEFT JOIN jobs j ON e.job_id = j.job_id
    LEFT JOIN payroll p ON e.id = p.employee_id
    LEFT JOIN employee_bank_details b ON e.id = b.employee_id
";

if (!empty($selected_job)) {
    $sql .= " WHERE e.job_id = '" . $conn->real_escape_string($selected_job) . "'";
}
$sql .= " ORDER BY e.full_name ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Employee Payroll Report</title>
<style>
body {
    font-family: "Poppins", sans-serif;
    background-color: #f5f7fa;
    padding: 40px;
}
h1 { text-align: center; color: #333; }
.filter-container { text-align: center; margin-bottom: 20px; }
select, button {
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 15px;
}
button {
    background: #007bff;
    color: #fff;
    cursor: pointer;
    border: none;
}
button:hover { background: #0056b3; }
table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
th, td {
    padding: 12px 15px;
    border: 1px solid #ddd;
    text-align: center;
}
th {
    background: #212529;
    color: #fff;
}
  .table-responsive {
    /* Remove max-height and overflow */
    max-height: none;
    overflow-y: visible;
}
   .table thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: #212529;
            color: #fff;
        }
tr:nth-child(even) { background: #f2f2f2; }
tr:hover { background: #e8f0fe; }
.success, .warning {
    text-align: center;
    padding: 10px;
    margin-bottom: 20px;
    border-radius: 8px;
}
.success { background: #d4edda; color: #155724; }
.warning { background: #fff3cd; color: #856404; }
.action-btn {
    padding: 6px 10px;
    border: none;
    border-radius: 5px;
    color: #fff;
    background: #071cffff;
    cursor: pointer;
}
.modal {
    display: none;
    position: fixed;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.3);
    padding: 25px;
    width: 400px;
    z-index: 1000;
}
.modal.active { display: block; }
.overlay {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.6);
    z-index: 999;
}
.overlay.active { display: block; }
.generate-all {
    margin-top: 25px;
    display: flex;
    justify-content: center;
}
.generate-all button {
    background: #28a745;
    font-size: 17px;
    padding: 10px 25px;
    border-radius: 8px;
}
</style>
</head>
<body>

<h1>Employee Payroll Report</h1>

<?php if ($message) echo $message; ?>

<div class="filter-container">
    <form method="GET" action="">
        <label for="job_id"><strong>Filter by Job Title:</strong></label>
        <select name="job_id" id="job_id">
            <option value="">All Jobs</option>
            <?php while ($job = $jobs_result->fetch_assoc()) {
                $selected = ($job['job_id'] == $selected_job) ? "selected" : "";
                echo "<option value='{$job['job_id']}' $selected>{$job['title']}</option>";
            } ?>
        </select>
        <button type="submit">Filter</button>
        <?php if (!empty($selected_job)) { ?>
            <button type="button" onclick="window.location='hr_payroll.php'">Clear</button>
        <?php } ?>
    </form>
</div>

<table class="table table-bordered table-striped table-hover">
   <thead class="thead-dark">
    <tr>
        <th>#</th>
        <th>Employee</th>
        <th>Job Title</th>
        <th>Basic</th>
        <th>Allowance</th>
        <th>Deduction</th>
        <th>Total</th>
        <th>Bank</th>
        <th>Action</th>
    </tr>
    <?php
    if ($result && $result->num_rows > 0) {
        $count = 1;
        while ($row = $result->fetch_assoc()) {
            $bankInfo = !empty($row['bank_name'])
                ? "<span style='color:green;'>{$row['bank_name']}<br>({$row['account_number']})</span>"
                : "<span style='color:red;'>Missing Bank</span>";

            echo "
            <tr>
                <td>{$count}</td>
                <td>{$row['full_name']}</td>
                <td>{$row['job_title']}</td>
                <td>₹{$row['basic_salary']}</td>
                <td>₹{$row['allowance']}</td>
                <td>₹{$row['deduction']}</td>
                <td><b>₹{$row['total']}</b></td>
                <td>{$bankInfo}</td>
                <td><button class='action-btn' onclick=\"openUpdateModal({$row['employee_id']}, {$row['basic_salary']}, {$row['allowance']}, {$row['deduction']})\">Update</button></td>
            </tr>";
            $count++;
        }
    } else {
        echo "<tr><td colspan='9' style='text-align:center;color:red;'>No records found.</td></tr>";
    }
    ?>
</table>

<!-- ✅ Generate Payslip Button -->
<div class="generate-all">
    <form method="POST">
        <input type="hidden" name="job_id" value="<?= htmlspecialchars($selected_job) ?>">
        <button type="submit" name="generate_all">Generate Payslips</button>
    </form>
</div>

<!-- Update Modal -->
<div class="overlay" id="overlay"></div>
<div class="modal" id="updateModal">
    <h3>Update Payroll</h3>
    <form method="POST">
        <input type="hidden" name="employee_id" id="empId">
        <label>Basic Salary:</label>
        <input type="number" step="0.01" name="basic_salary" id="basic_salary" required><br><br>
        <label>Allowance:</label>
        <input type="number" step="0.01" name="allowance" id="allowance" required><br><br>
        <label>Deduction:</label>
        <input type="number" step="0.01" name="deduction" id="deduction" required><br><br>
        <button type="submit" name="update_salary" class="action-btn">Save</button>
        <button type="button" onclick="closeModal()">Cancel</button>
    </form>
</div>

<script>
function openUpdateModal(empId, basic, allowance, deduction) {
    document.getElementById('empId').value = empId;
    document.getElementById('basic_salary').value = basic || 0;
    document.getElementById('allowance').value = allowance || 0;
    document.getElementById('deduction').value = deduction || 0;
    document.getElementById('updateModal').classList.add('active');
    document.getElementById('overlay').classList.add('active');
}
function closeModal() {
    document.getElementById('updateModal').classList.remove('active');
    document.getElementById('overlay').classList.remove('active');
}
</script>

</body>
</html>
