<?php
$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1️⃣ Find all candidates with employee_type = 1
$sql = "SELECT * FROM candidates WHERE employee_type = 1";
$result = $conn->query($sql);

$promoted_count = 0;

if ($result->num_rows > 0) {
    while ($candidate = $result->fetch_assoc()) {

        $candidate_id = $candidate['id'];

        // 2️⃣ Get the job_id for this candidate where they were selected
        $job_sql = "SELECT job_id FROM job_applications WHERE candidate_id = ? AND selection_status = 'Selected' LIMIT 1";
        $stmt_job = $conn->prepare($job_sql);
        $stmt_job->bind_param("i", $candidate_id);
        $stmt_job->execute();
        $stmt_job->bind_result($job_id);
        $stmt_job->fetch();
        $stmt_job->close();

        if (!$job_id) {
            echo "❌ Candidate ID {$candidate_id} has no selected job. Skipped.<br>";
            continue; // Skip if no selected job
        }

        // 3️⃣ Generate new employee_id
        $emp_result = $conn->query("SELECT MAX(CAST(SUBSTRING(employee_id, 4) AS UNSIGNED)) AS max_no FROM employees");
        $row = $emp_result->fetch_assoc();
        $next_no = $row['max_no'] ? $row['max_no'] + 1 : 1;
        $employee_id = 'EMP' . str_pad($next_no, 3, '0', STR_PAD_LEFT);

        // 4️⃣ Prepare other data
        $full_name = $candidate['full_name'];
        $email = $candidate['email'];
        $password = 'HR123'; // Force new HR password
        $contact = $candidate['contact'];
        $gender = $candidate['gender'];
        $qualification = $candidate['qualification'];
        $dob = $candidate['dob'];
        $photo = $candidate['photo'];
        $experience_status = $candidate['experience_status'];
        $experience_details = $candidate['experience_details'];

        // 5️⃣ Insert into employees
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
            // 6️⃣ Delete from candidates
            $delete_sql = "DELETE FROM candidates WHERE id = ?";
            $stmt_delete = $conn->prepare($delete_sql);
            $stmt_delete->bind_param("i", $candidate_id);
            $stmt_delete->execute();

            $promoted_count++;
            echo "✅ Promoted: {$full_name} | Employee ID: {$employee_id} | Job ID: {$job_id}<br>";
        } else {
            echo "❌ Failed to insert candidate ID {$candidate_id}: {$stmt_insert->error}<br>";
        }
    }
} else {
    echo "✅ No candidates to promote.<br>";
}

echo "<br>✅ Total newly promoted: {$promoted_count}";

$conn->close();
?>
