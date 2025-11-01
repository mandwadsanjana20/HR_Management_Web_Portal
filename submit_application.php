<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Candidate must be logged in
if (!isset($_SESSION['candidate_email'])) {
    echo "<script>alert('Please log in.'); window.location.href='login.php';</script>";
    exit;
}

$candidate_email = $_SESSION['candidate_email'];
$email_escaped = $conn->real_escape_string($candidate_email);

// Find candidate ID and current resume path
$result = $conn->query("SELECT id, resume FROM candidates WHERE email = '$email_escaped'");
if (!$result || $result->num_rows == 0) {
    echo "Candidate not found.";
    exit;
}

$row = $result->fetch_assoc();
$candidate_id = $row['id'];
$resume_path = $row['resume'] ?? '';

// Get form data safely and trim
$name = trim($_POST['name']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$cover_letter = trim($_POST['cover_letter']);
$job_id = intval($_POST['job_id']);

// === SKILLS HANDLING ===
// Get skills array from POST (multi-select input)
$skills_arr = isset($_POST['skills']) && is_array($_POST['skills']) ? $_POST['skills'] : [];
// Sanitize each skill string
$clean_skills = array_map(function($skill) use ($conn) {
    return $conn->real_escape_string(trim($skill));
}, $skills_arr);
// Join into comma-separated string for DB
$skills_str = implode(',', $clean_skills);

// Handle resume upload if new file provided
if (isset($_FILES["resume"]) && $_FILES["resume"]["size"] > 0) {
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Validate file type and size
    $file_tmp = $_FILES["resume"]["tmp_name"];
    $file_name = basename($_FILES["resume"]["name"]);
    $file_size = $_FILES["resume"]["size"];
    $file_type = mime_content_type($file_tmp);
    $allowed_types = ['application/pdf'];

    if (!in_array($file_type, $allowed_types)) {
        echo "<script>alert('Only PDF files are allowed for resume.'); history.back();</script>";
        exit;
    }

    if ($file_size > 2 * 1024 * 1024) {
        echo "<script>alert('Resume file size must be under 2MB.'); history.back();</script>";
        exit;
    }

    $new_file_name = time() . "_" . preg_replace("/[^a-zA-Z0-9\._-]/", "", $file_name);
    $target_file = $upload_dir . $new_file_name;

    if (move_uploaded_file($file_tmp, $target_file)) {
        $resume_path = $conn->real_escape_string($target_file);

        // Update candidate's resume path securely
        $update_stmt = $conn->prepare("UPDATE candidates SET resume = ? WHERE id = ?");
        $update_stmt->bind_param("si", $resume_path, $candidate_id);
        $update_stmt->execute();
        $update_stmt->close();
    } else {
        echo "<script>alert('Failed to upload resume.'); history.back();</script>";
        exit;
    }
}

// Insert job application with skills column added
$stmt = $conn->prepare("
  INSERT INTO job_applications (candidate_id, job_id, name, email, phone, resume, cover_letter)
  VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param("iisssss", $candidate_id, $job_id, $name, $email, $phone, $resume_path, $cover_letter);

if ($stmt->execute()) {
    echo "<script>alert('Application submitted successfully!'); window.location.href='job_post.php';</script>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
