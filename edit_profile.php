<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Ensure candidate is logged in
if (!isset($_SESSION['candidate_email'])) {
    echo "Unauthorized access!";
    exit;
}

$email = $_SESSION['candidate_email'];

// ✅ Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $contact = trim($_POST['contact']);
    $gender = trim($_POST['gender']);
    $qualification = trim($_POST['qualification']);
    $dob = trim($_POST['dob']);
    $experience_status = trim($_POST['experience_status']);
    $experience_details = trim($_POST['experience_details']);

    // ✅ Handle photo upload
    $photo = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo_tmp = $_FILES['photo']['tmp_name'];
        $photo_name = basename($_FILES['photo']['name']);
        $photo_target = "uploads/photos/" . uniqid() . "_" . $photo_name;
        if (move_uploaded_file($photo_tmp, $photo_target)) {
            $photo = $photo_target;
        }
    }

    // ✅ Handle resume upload
    $resume = null;
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $resume_tmp = $_FILES['resume']['tmp_name'];
        $resume_name = basename($_FILES['resume']['name']);
        $resume_target = "uploads/resumes/" . uniqid() . "_" . $resume_name;
        if (move_uploaded_file($resume_tmp, $resume_target)) {
            $resume = $resume_target;
        }
    }

    // ✅ Build dynamic SQL
    $update_sql = "UPDATE candidates SET full_name=?, contact=?, gender=?, qualification=?, dob=?, experience_status=?, experience_details=?";
    $params = [$full_name, $contact, $gender, $qualification, $dob, $experience_status, $experience_details];
    $types = "sssssss";

    if ($photo) {
        $update_sql .= ", photo=?";
        $params[] = $photo;
        $types .= "s";
    }
    if ($resume) {
        $update_sql .= ", resume=?";
        $params[] = $resume;
        $types .= "s";
    }

    $update_sql .= " WHERE email=?";
    $params[] = $email;
    $types .= "s";

    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='candidate_dashboard.php';</script>";
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}

// ✅ Load current candidate details
$sql = "SELECT * FROM candidates WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$candidate = $result->fetch_assoc();

if (!$candidate) {
    echo "Candidate not found!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <!-- ✅ Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Edit Profile</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($candidate['full_name']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Contact</label>
                    <input type="text" name="contact" class="form-control" value="<?php echo htmlspecialchars($candidate['contact']); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="Male" <?php if ($candidate['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                        <option value="Female" <?php if ($candidate['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                        <option value="Other" <?php if ($candidate['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Qualification</label>
                    <input type="text" name="qualification" class="form-control" value="<?php echo htmlspecialchars($candidate['qualification']); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="dob" class="form-control" value="<?php echo htmlspecialchars($candidate['dob']); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Experience Status</label>
                    <select name="experience_status" class="form-select">
                        <option value="Yes" <?php if ($candidate['experience_status'] == 'Yes') echo 'selected'; ?>>Yes</option>
                        <option value="No" <?php if ($candidate['experience_status'] == 'No') echo 'selected'; ?>>No</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Experience Details</label>
                    <textarea name="experience_details" class="form-control" rows="3"><?php echo htmlspecialchars($candidate['experience_details']); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Current Photo:</label><br>
                    <?php if ($candidate['photo']): ?>
                        <img src="<?php echo htmlspecialchars($candidate['photo']); ?>" alt="Photo" width="120"><br>
                    <?php else: ?>
                        <em>No photo uploaded.</em><br>
                    <?php endif; ?>
                    <input type="file" name="photo" class="form-control mt-2">
                </div>

                <div class="mb-3">
                    <label class="form-label">Current Resume:</label><br>
                    <?php if ($candidate['resume']): ?>
                        <a href="<?php echo htmlspecialchars($candidate['resume']); ?>" target="_blank">View Resume</a><br>
                    <?php else: ?>
                        <em>No resume uploaded.</em><br>
                    <?php endif; ?>
                    <input type="file" name="resume" class="form-control mt-2">
                </div>

                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>
</div>

<!-- ✅ Bootstrap JS Bundle (optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
