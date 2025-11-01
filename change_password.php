<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Determine user type and ID from session
// Assuming session stores user type and ID like this:
// $_SESSION['user_type'] = 'admin' or 'candidate'
// $_SESSION['user_id'] = user ID
if (!isset($_SESSION['candidate_email'], $_SESSION['candidate_id'])) {
    header("Location: login1.php"); // Redirect if not logged in
    exit();
}

$userType = $_SESSION['admin_id']; // 'admin' or 'candidate'
$userId = intval($_SESSION['admin_id']);

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldPassword = $_POST['old_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Basic validations
    if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
        $errorMessage = "All fields are required.";
    } elseif ($newPassword !== $confirmPassword) {
        $errorMessage = "New password and confirm password do not match.";
    } elseif (strlen($newPassword) < 6) {
        $errorMessage = "New password should be at least 6 characters.";
    } else {
        // Table & column depend on user type
        $table = $userType === 'admin' ? 'admins' : 'candidates';
        $idColumn = $userType === 'admin' ? 'id' : 'id';

        // Fetch current hashed password from DB
        $stmt = $conn->prepare("SELECT password FROM $table WHERE $idColumn = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($hashedPassword);
        if ($stmt->fetch()) {
            // Verify old password
            if (password_verify($oldPassword, $hashedPassword)) {
                $stmt->close();

                // Hash new password
                $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Update new password
                $updateStmt = $conn->prepare("UPDATE $table SET password = ? WHERE $idColumn = ?");
                $updateStmt->bind_param("si", $newHashedPassword, $userId);
                if ($updateStmt->execute()) {
                    $successMessage = "Password changed successfully.";
                } else {
                    $errorMessage = "Failed to update password. Please try again.";
                }
                $updateStmt->close();
            } else {
                $errorMessage = "Old password is incorrect.";
            }
        } else {
            $errorMessage = "User not found.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 480px;">
    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="card-title mb-4 text-center">Change Password (<?= htmlspecialchars(ucfirst($userType)) ?>)</h3>

            <?php if ($successMessage): ?>
                <div class="alert alert-success"><?= $successMessage ?></div>
            <?php elseif ($errorMessage): ?>
                <div class="alert alert-danger"><?= $errorMessage ?></div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <div class="mb-3">
                    <label for="old_password" class="form-label">Old Password</label>
                    <input type="password" class="form-control" id="old_password" name="old_password" required>
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" minlength="6" required>
                    <div class="form-text">Minimum 6 characters</div>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Change Password</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Optional: Simple client-side password confirmation check
    const form = document.querySelector('form');
    form.addEventListener('submit', (e) => {
        const newPass = form.new_password.value;
        const confirmPass = form.confirm_password.value;
        if (newPass !== confirmPass) {
            e.preventDefault();
            alert('New password and confirm password do not match.');
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
