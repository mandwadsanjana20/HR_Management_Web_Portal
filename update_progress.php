<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = intval($_POST['project_id']);
    $progress = intval($_POST['progress']);
    $remarks = $conn->real_escape_string($_POST['remarks']);

    // ✅ Allowed file types
    $allowedExtensions = ($progress >= 100)
        ? ['zip']  // Only ZIP for final submission
        : ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

    $filePath = '';
    if (!empty($_FILES['project_file']['name'])) {
        $fileName = basename($_FILES['project_file']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowedExtensions)) {
            echo "<script>alert('Only " . implode(', ', $allowedExtensions) . " files are allowed for this progress level!'); window.history.back();</script>";
            exit();
        }

        // ✅ Use uploads/progress/ folder
        $uploadDir = "uploads/progress/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        // Rename file safely
        $newFileName = time() . "_" . preg_replace("/[^a-zA-Z0-9_.-]/", "_", $fileName);
        $targetFile = $uploadDir . $newFileName;

        // Move file to correct folder
        if (move_uploaded_file($_FILES['project_file']['tmp_name'], $targetFile)) {
            $filePath = $targetFile; // ✅ Store full relative path
        } else {
            echo "<script>alert('Error uploading file.'); window.history.back();</script>";
            exit();
        }
    }

    // ✅ Update project record
    if (!empty($filePath)) {
        $sql = "UPDATE projects 
                SET progress=?, remarks=?, file_path=?, 
                    status = CASE WHEN ? >= 100 THEN 'Completed' ELSE 'In Progress' END, 
                    updated_at = NOW() 
                WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issii", $progress, $remarks, $filePath, $progress, $project_id);
    } else {
        $sql = "UPDATE projects 
                SET progress=?, remarks=?, 
                    status = CASE WHEN ? >= 100 THEN 'Completed' ELSE 'In Progress' END, 
                    updated_at = NOW() 
                WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isii", $progress, $remarks, $progress, $project_id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Project updated successfully!'); window.location.href='employee_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating project!'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
