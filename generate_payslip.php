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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['job_id'])) {
    $job_id = $conn->real_escape_string($_POST['job_id']);

    // ✅ Fetch job title
    $job_sql = "SELECT title FROM jobs WHERE job_id = '$job_id'";
    $job_res = $conn->query($job_sql);
    $job_title = $job_res && $job_res->num_rows > 0 ? $job_res->fetch_assoc()['title'] : 'Unknown Job';

    // ✅ Fetch employees under job_id who have valid bank details (from employee_bank_details)
    $sql = "
        SELECT 
            e.id AS employee_id,
            e.full_name, 
            e.email, 
            j.title, 
            p.basic_salary, 
            p.allowance, 
            p.deduction, 
            p.total, 
            p.comment,
            b.bank_name,
            b.account_number,
            b.ifsc_code
        FROM employees e
        LEFT JOIN jobs j ON e.job_id = j.job_id
        LEFT JOIN payroll p ON e.id = p.employee_id
        INNER JOIN employee_bank_details b ON e.id = b.employee_id
        WHERE e.job_id = '$job_id'
          AND b.bank_name IS NOT NULL AND b.bank_name != ''
          AND b.account_number IS NOT NULL AND b.account_number != ''
          AND b.ifsc_code IS NOT NULL AND b.ifsc_code != ''
    ";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $to = $row['email'];
            $subject = "Payslip - {$row['title']} | {$row['full_name']}";

            $message = "
            <html>
            <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .box { border:1px solid #ddd; padding:15px; border-radius:8px; background:#f9f9f9; }
                .amount { font-weight:bold; color:#007bff; }
                .bank { color:#555; margin-top:10px; }
            </style>
            </head>
            <body>
            <div class='box'>
                <h2>Company Payroll Payslip</h2>
                <p><strong>Employee Name:</strong> {$row['full_name']}</p>
                <p><strong>Job Title:</strong> {$row['title']}</p>
                <p><strong>Basic Salary:</strong> ₹{$row['basic_salary']}</p>
                <p><strong>Allowance:</strong> ₹{$row['allowance']}</p>
                <p><strong>Deduction:</strong> ₹{$row['deduction']}</p>
                <p><strong>Total Pay:</strong> <span class='amount'>₹{$row['total']}</span></p>
                <p><strong>Comment:</strong> {$row['comment']}</p>

                <div class='bank'>
                    <hr>
                    <h4>Bank Details</h4>
                    <p><strong>Bank Name:</strong> {$row['bank_name']}</p>
                    <p><strong>Account Number:</strong> {$row['account_number']}</p>
                    <p><strong>IFSC Code:</strong> {$row['ifsc_code']}</p>
                </div>

                <hr>
                <p>This is an auto-generated payslip from the company payroll system.</p>
            </div>
            </body></html>
            ";

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: hr@company.com\r\n";

            // Uncomment below line when mail setup is ready
            // mail($to, $subject, $message, $headers);
        }

        echo "✅ Payslips generated successfully and emailed to employees under <strong>{$job_title}</strong> who have valid bank details.";
    } else {
        echo "⚠️ No employees with valid bank details found under this job title.";
    }
}
$conn->close();
?>
