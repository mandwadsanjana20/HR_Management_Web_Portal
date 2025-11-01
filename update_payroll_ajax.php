<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "company_db";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_POST['id'];
$allowance = $_POST['allowance'];
$deduction = $_POST['deduction'];
$comment = $_POST['comment'];

// Update total automatically
$sql = "UPDATE payroll 
        SET allowance='$allowance', deduction='$deduction', total=(basic_salary + $allowance - $deduction), comment='$comment' 
        WHERE id='$id'";

if ($conn->query($sql)) {
    echo "Payroll updated successfully!";
} else {
    echo "Error updating payroll: " . $conn->error;
}
$conn->close();
?>
