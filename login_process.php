<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emailOrId = trim($_POST['employee_id']);
    $password = $_POST['password'];
    $rememberMe = isset($_POST['remember_me']); // Check if "Remember Me" checked

    // 1️⃣ Check Admin login
    $adminStmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $adminStmt->bind_param("s", $emailOrId);
    $adminStmt->execute();
    $adminResult = $adminStmt->get_result();

    if ($adminResult->num_rows === 1) {
        $admin = $adminResult->fetch_assoc();

        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_logged_in'] = true;

            header("Location: admin_dashboard.php");
            exit();
        } else {
            header("Location: login1.php?error=Invalid admin password.");
            exit();
        }
    }

    // 2️⃣ Check Employee login
    $employeeStmt = $conn->prepare("SELECT * FROM employees WHERE employee_id = ?");
    $employeeStmt->bind_param("s", $emailOrId);
    $employeeStmt->execute();
    $employeeResult = $employeeStmt->get_result();

    if ($employeeResult->num_rows === 1) {
        $employee = $employeeResult->fetch_assoc();

        if (!empty($employee['password'])) {
            $_SESSION['employee_id'] = $employee['id'];
            $_SESSION['employee_name'] = $employee['full_name'];
            $_SESSION['employee_email'] = $employee['email'];

            header("Location: employee_dashboard.php");
            exit();
        } else {
            header("Location: login1.php?error=Invalid employee password.");
            exit();
        }
    }

    // 3️⃣ Check Candidate login with Remember Me
    $stmt = $conn->prepare("SELECT * FROM candidates WHERE email = ?");
    $stmt->bind_param("s", $emailOrId);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $row = $res->fetch_assoc();

        if ($row['approval_status'] === 'Rejected') {
            $reason = urlencode($row['rejection_reason']);
            header("Location: login1.php?error=Your application was rejected. Reason: $reason");
            exit();
        }

        if (!empty($row['password']) && password_verify($password, $row['password'])) {
            // Set session
            $_SESSION['candidate_id'] = $row['id'];
            $_SESSION['candidate_name'] = $row['full_name'];
            $_SESSION['candidate_email'] = $row['email'];

            // Remember Me functionality
            if ($rememberMe) {
                $token = bin2hex(random_bytes(32)); // Generate random token
                $token_hash = password_hash($token, PASSWORD_DEFAULT);
                $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));

                // Insert token hash in DB
                $insertStmt = $conn->prepare("INSERT INTO user_tokens (user_id, token_hash, expiry) VALUES (?, ?, ?)");
                $insertStmt->bind_param("iss", $row['id'], $token_hash, $expiry);
                $insertStmt->execute();

                // Set cookie for 30 days, HttpOnly & Secure flags recommended if using HTTPS
                setcookie('remember_me', $token, time() + (30 * 24 * 60 * 60), "/", "", true, true);
            }

            header("Location: candidate_dashboard.php");
            exit();
        } else {
            header("Location: login1.php?error=Invalid candidate password.");
            exit();
        }
    }

    // No account found
    header("Location: login1.php?error=Account not found.");
    exit();
}
