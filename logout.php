<?php
session_start();

// Check if role parameter is set
if (isset($_GET['role'])) {
    $role = $_GET['role'];

    switch ($role) {
        case 'admin':
            unset($_SESSION['admin_id']);
            unset($_SESSION['admin_username']);
            break;

        case 'employee':
            unset($_SESSION['employee_id']);
            unset($_SESSION['employee_email']);
            unset($_SESSION['employee_name']);
            break;

        case 'candidate':
            unset($_SESSION['candidate_id']);
            unset($_SESSION['candidate_email']);
            unset($_SESSION['candidate_name']);
            break;
    }
} else {
    // If no role specified, destroy everything
    session_unset();
    session_destroy();
}

// If no sessions remain, destroy session completely
if (empty($_SESSION)) {
    session_destroy();
}

// Redirect to login or home page
header("Location: index.php");
exit();
?>
