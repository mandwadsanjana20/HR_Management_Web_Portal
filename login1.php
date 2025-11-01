<?php
session_start();
$msg = '';

if (isset($_GET['error'])) {
    $msg = $_GET['error'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Employee Login - HR Portal</title>
    <style>
      * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    height: 100vh;
  background: linear-gradient(135deg, #e0e0e0 0%, #f9f9f9 100%);
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: center;
    justify-content: center;
}

.container {
    width: 900px;
    height: 500px;
    display: flex;
    background: #fff;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
    animation: slideIn 1s ease forwards;
}

.form-box {
    flex: 1;
    padding: 40px 50px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    animation: fadeIn 2s ease forwards;
}

h2 {
    font-size: 2.4rem;
    color: #00394f;
    text-shadow: 1px 1px #ccc;
    margin-bottom: 20px;
    text-align: center;
    font-weight: bold;
}

.error-wrapper {
    min-height: 60px;
    margin-bottom: 10px;
}

.error-message {
    background-color: #ffe6e6;
    color: #b30000;
    border: 1px solid #ff4d4d;
    padding: 12px 15px;
    border-radius: 8px;
    font-weight: bold;
    animation: fadeSlide 0.5s ease-in-out;
    box-shadow: 0 2px 10px rgba(255, 0, 0, 0.1);
}

.input-group {
    margin-bottom: 20px;
}

.input-group label {
    display: block;
    margin-bottom: 6px;
    color: #333;
    font-weight: 500;
    font-size: 0.95rem;
}

.input-group input {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 1rem;
    background-color: #f9f9f9;
    transition: border 0.3s ease, box-shadow 0.3s ease;
}

.input-group input:focus {
    border-color: #008891;
    outline: none;
    background-color: #fff;
    box-shadow: 0 0 6px rgba(0, 136, 145, 0.3);
}

button {
    padding: 12px;
    width: 100%;
    background: #000;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    font-weight: 600;
    transition: background 0.3s ease;
    margin-top: 10px;
}

button:hover {
    background: #dc3545;
}

.signup-link {
    text-align: center;
    margin-top: 25px;
    font-size: 0.95rem;
}

.signup-link a {
    color: #00394f;
    text-decoration: none;
    font-weight: 600;
}

.signup-link a:hover {
    text-decoration: underline;
}

.btn12 {
    padding: 10px;
    margin-top: 10px;
    color: #00394f;
    font-weight: 500;
    background: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn12:hover {
    color: red;
    background:none;
}

@keyframes slideIn {
    from {
        transform: translateY(100px);
        opacity: 0;
    }
    to {
        transform: translateY(0px);
        opacity: 1;
    }
}

@keyframes fadeIn {
    0% {
        opacity: 0;
        transform: translateX(-30px);
    }
    100% {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fadeSlide {
    0% {
        opacity: 0;
        transform: translateY(-10px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Image box styling */
.image-box {
    flex: 1;
    background: rgb(103, 158, 160);
    display: flex;
    align-items: center;
    justify-content: center;
}

.image-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

    </style>
</head>
<body>
    <div class="container">
        <div class="form-box">
            <h2>Welcome Back</h2>

            <div class="error-wrapper">
                <?php if (!empty($msg)): ?>
                    <div class="error-message"><?= htmlspecialchars($msg); ?></div>
                <?php endif; ?>
            </div>

            <form action="login_process.php" method="POST">
                <div class="input-group">
                    <label>Email or Username :</label>
                    <input type="text" name="employee_id" placeholder="Enter Email" required>
                </div>
                <div class="input-group">
                    <label>Password :</label>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                
                <button type="submit">Login</button>

                <p class="signup-link">Don't have an account? <a href="register.php">Register Now</a></p>
            </form>

            <button class="btn12" onclick="window.location.href='index.php'">Back to Home</button>
        </div>

        <div class="image-box">
            <img src="lo_img.avif" alt="Login Illustration" />
        </div>
    </div>
</body>
</html>
