<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['id'])) {
    die("Application ID missing.");
}

$application_id = intval($_GET['id']);

$sql = "
    SELECT 
        c.full_name, c.email, c.contact,
        j.title AS job_title,
        ja.selection_status,
        ja.offer_letter  -- optional if you store letter as file/path
    FROM job_applications ja
    JOIN candidates c ON ja.candidate_id = c.id
    JOIN jobs j ON ja.job_id = j.job_id
    WHERE ja.id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $application_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Application not found.");
}

$row = $result->fetch_assoc();

if ($row['selection_status'] !== 'Selected') {
    echo "<script>
        alert('Your Interview Selection is in Process...');
        window.location.href = 'profile.php';
    </script>";
    exit();
}
function safeOut($str) {
    return htmlspecialchars($str);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Offer Letter - <?= safeOut($row['full_name']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background: #f5f5f5;
        }
        .letter {
            background: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 700px;
            margin: auto;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #0066cc;
        }
        p {
            font-size: 16px;
            line-height: 1.5;
        }
        .signature {
            margin-top: 50px;
            font-weight: bold;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="letter">
    <!-- ✅ Company Logo + Details -->
    <div style="text-align: center;">
        <img src="tech.jpg" alt="Company Logo" style="height: 80px; margin-bottom: 10px;">
        <h2 style="margin: 5px 0;">Your Company Name Pvt. Ltd.</h2>
        <p style="margin: 0;">
            123 Business Street, Industrial Area,<br>
            Solapur, Maharashtra, India - 413001<br>
            Contact: +91-9876543210 | Email: info@yourcompany.com
        </p>
    </div>

    <!-- ✅ Horizontal Line -->
    <hr style="margin: 20px 0; border-top: 2px solid #333;">

    <!-- Existing Offer Letter Content -->
    <h1>Offer Letter</h1>
    <p><strong>Ref No:</strong> HR/<?= date('Y') ?>/<?= str_pad($application_id, 4, '0', STR_PAD_LEFT) ?></p>
    <p><strong>Date:</strong> <?= date('F j, Y') ?></p>

    <p>To,</p>
    <p><strong><?= safeOut($row['full_name']) ?></strong><br>
    Email: <?= safeOut($row['email']) ?><br>
    Contact: <?= safeOut($row['contact']) ?></p>

    <p>Subject: <strong>Appointment for the post of <?= safeOut($row['job_title']) ?></strong></p>

    <p>Dear <strong><?= safeOut($row['full_name']) ?></strong>,</p>

    <p>We are delighted to offer you the position of <strong><?= safeOut($row['job_title']) ?></strong> at our company.</p>

    <p>Please find below the terms and conditions of your employment. This offer is valid subject to the completion of required formalities and your acceptance within the stipulated time.</p>

    <p>Kindly sign and return a copy of this letter as a token of your acceptance.</p>

    <p>We welcome you to our team and look forward to a mutually beneficial association.</p>

    <p>For any further clarification, please feel free to reach out to our HR Department at hr@company.com.</p>

    <div class="signature">
        <p>Yours sincerely,<br><br>
        HR Manager<br>
        Your Company Name Pvt. Ltd.</p>
    </div>

    <button id="downloadBtn" onclick="downloadPDF()" style="padding:10px 20px; background:#007BFF; color:#fff; border:none; border-radius:4px;">
        Download PDF
    </button>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<script>
function downloadPDF() {
  const button = document.getElementById('downloadBtn');
  const letter = document.querySelector('.letter');
  if (button) button.style.display = 'none'; // hide button before generating

  const opt = {
    margin: 10,
    filename: 'Offer_Letter.pdf',
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: { scale: 2, useCORS: true },
    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
    pagebreak: { mode: ['css', 'legacy'] }
  };

  html2pdf().set(opt).from(letter).save().then(() => {
    if (button) button.style.display = 'inline-block'; // show again if needed
  });
}
</script>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
