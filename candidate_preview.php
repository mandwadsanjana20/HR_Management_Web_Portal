<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");

if (!isset($_SESSION['registered_id'])) {
    header("Location: register.php");
    exit();
}

$id = $_SESSION['registered_id'];
$result = $conn->query("SELECT * FROM candidates WHERE id = $id");
$candidate = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration Preview</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #e0f7fa, #fff);
            padding: 40px;
        }

        .preview-box {
            max-width: 700px;
            background: white;
            padding: 30px;
            margin: auto;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            animation: fadeIn 0.7s ease-in-out;
        }

        h2 {
            text-align: center;
            color: #00796B;
            margin-bottom: 30px;
        }

        .info {
            margin-bottom: 20px;
        }

        .info label {
            font-weight: bold;
            color: #444;
        }

        .info span {
            display: inline-block;
            margin-left: 10px;
        }

        .preview-photo {
            text-align: center;
            margin-top: 20px;
        }

        .preview-photo img {
            height: 120px;
            border-radius: 8px;
            float:top;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity: 1; transform: translateY(0);}
        }

        .btn-home {
            display: block;
            text-align: center;
            margin-top: 30px;
        }

        .btn-home a {
            text-decoration: none;
            padding: 12px 24px;
            background: #00796B;
            color: white;
            border-radius: 8px;
            font-weight: bold;
        }

        .btn-home a:hover {
            background: #004d40;
        }
    </style>
</head>
<body>

<div class="preview-box">
    <h2>Candidate Registration Preview</h2>

    <div class="info"><label>Name:</label> <span><?= htmlspecialchars($candidate['full_name']) ?></span></div>
    <div class="info"><label>Email:</label> <span><?= htmlspecialchars($candidate['email']) ?></span></div>
    <div class="info"><label>Contact:</label> <span><?= htmlspecialchars($candidate['contact']) ?></span></div>
    <div class="info"><label>Gender:</label> <span><?= htmlspecialchars($candidate['gender']) ?></span></div>
    <div class="info"><label>Qualification:</label> <span><?= htmlspecialchars($candidate['qualification']) ?></span></div>
    <div class="info"><label>Date of Birth:</label> <span><?= htmlspecialchars($candidate['dob']) ?></span></div>
    <div class="info"><label>Experience:</label> <span><?= htmlspecialchars($candidate['experience_status']) ?></span></div>

    <?php if ($candidate['experience_status'] === "Yes"): ?>
        <div class="info"><label>Experience Details:</label> <span><?= nl2br(htmlspecialchars($candidate['experience_details'])) ?></span></div>
    <?php endif; ?>

    <div class="preview-photo">
        <label>Photo:</label><br>
        <img src="uploads/photos/<?= htmlspecialchars($candidate['photo']) ?>" alt="Candidate Photo">
    </div>

    <div class="btn-home">
        <a href="index.php">Go to Home</a>
    </div>
</div>

</body>
</html>
