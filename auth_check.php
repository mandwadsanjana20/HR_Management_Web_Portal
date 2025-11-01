<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (!isset($_SESSION['candidate_id']) && isset($_COOKIE['remember_me'])) {
    $token = $_COOKIE['remember_me'];

    $stmt = $conn->prepare("SELECT user_id, token_hash, expiry FROM user_tokens WHERE expiry > NOW()");
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        if (password_verify($token, $row['token_hash'])) {
            $_SESSION['candidate_id'] = $row['user_id'];
            // Optionally renew cookie expiry
            break;
        }
    }
}

if (!isset($_SESSION['candidate_id'])) {
    header("Location: login.php");
    exit();
}
