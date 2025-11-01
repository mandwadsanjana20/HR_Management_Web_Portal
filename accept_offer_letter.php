<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$conn = new mysqli("localhost", "root", "", "company_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Only allow logged-in candidates
if (!isset($_SESSION['candidate_email'])) {
    exit("Unauthorized access");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id'])) {
    $application_id = intval($_POST['application_id']);
    $user_email = $_SESSION['candidate_email'];

    // ✅ Get candidate details
    $stmt = $conn->prepare("SELECT * FROM candidates WHERE email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        exit("Candidate not found");
    }
    $candidate = $result->fetch_assoc();
    $candidate_id = $candidate['id'];
    $stmt->close();

    // ✅ Get Job ID from job_applications
    $stmt = $conn->prepare("SELECT job_id FROM job_applications WHERE id = ? AND candidate_id = ?");
    $stmt->bind_param("ii", $application_id, $candidate_id);
    $stmt->execute();
    $stmt->bind_result($job_id);
    $stmt->fetch();
    $stmt->close();
    // Check if candidate is already promoted
$check_emp = $conn->prepare("SELECT id FROM employees WHERE candidate_id = ?");
$check_emp->bind_param("i", $candidate_id);
$check_emp->execute();
$check_result = $check_emp->get_result();

if ($check_result->num_rows > 0) {
    // Candidate is already promoted
    $_SESSION['message'] = "Candidate has already been promoted to Employee.";
    header("Location: candidate_dashboard.php");
    exit();
}
$check_emp->close();


    // ✅ Generate Employee ID
    $emp_result = $conn->query("SELECT MAX(CAST(SUBSTRING(employee_id, 4) AS UNSIGNED)) AS max_no FROM employees");
    $row = $emp_result->fetch_assoc();
    $next_no = $row['max_no'] ? $row['max_no'] + 1 : 1;
    $employee_id = 'EMP' . str_pad($next_no, 3, '0', STR_PAD_LEFT);

    // ✅ Prepare Employee Data
    $full_name = $candidate['full_name'];
    $email = $candidate['email'];
    $password = 'HR123'; // default password
    $contact = $candidate['contact'];
    $gender = $candidate['gender'];
    $qualification = $candidate['qualification'];
    $dob = $candidate['dob'];
    $photo = $candidate['photo'];
    $experience_status = $candidate['experience_status'];
    $experience_details = $candidate['experience_details'];

    // ✅ Insert into employees
    $insert_sql = "INSERT INTO employees (
        employee_id, candidate_id, full_name, email, password, contact,
        gender, qualification, dob, photo, experience_status, experience_details, job_id, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt_insert = $conn->prepare($insert_sql);
    $stmt_insert->bind_param(
        "sisssssssssis",
        $employee_id,
        $candidate_id,
        $full_name,
        $email,
        $password,
        $contact,
        $gender,
        $qualification,
        $dob,
        $photo,
        $experience_status,
        $experience_details,
        $job_id
    );

    if ($stmt_insert->execute()) {
        // ✅ Update job_application offer_status
        $stmt_update = $conn->prepare("UPDATE job_applications SET offer_status = 'Accepted' WHERE id = ? AND candidate_id = ?");
        $stmt_update->bind_param("ii", $application_id, $candidate_id);
        $stmt_update->execute();
        $stmt_update->close();

        // ✅ Mark candidate as promoted instead of deleting
$stmt_promote = $conn->prepare("UPDATE candidates SET status = 'Promoted' WHERE id = ?");
$stmt_promote->bind_param("i", $candidate_id);
$stmt_promote->execute();
$stmt_promote->close();

$_SESSION['message'] = "Offer accepted! Candidate promoted to Employee (ID: $employee_id)";
header("Location: candidate_dashboard.php");
exit();

    } else {
        echo "Error promoting candidate: " . $stmt_insert->error;
    }

    $stmt_insert->close();
}

$conn->close();
?>
