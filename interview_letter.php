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
      ja.interview_date, ja.interview_time, ja.meet_link,
      c.full_name, c.email, c.contact, c.photo,
      j.title AS job_title
    FROM job_applications ja
    JOIN candidates c ON ja.candidate_id = c.id
    JOIN jobs j ON ja.job_id = j.job_id
    WHERE ja.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $application_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("Application not found.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Interview Letter</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header img { width: 80px; float: left; }
        .content { display: flex; justify-content: space-between; }
        .text { width: 70%; }
        .photo img { width: 120px; height: 150px; object-fit: cover; border: 1px solid #000; }
        .instructions { margin-top: 20px; font-weight: bold; }
        .meet-link { margin-top: 10px; }
        .meet-link a { color: #007BFF; font-weight: bold; text-decoration: none;}
        @media print {
    button { display: none; }
    body { margin: 0; }

 }
    </style>
</head>
<body>
    <div id="interview-letter">
<div class="header">
    <img src="tech.jpg" alt="Logo">
    <h2>Company Name</h2>
    <p>Address | Contact: 1234567890 | Email: company@example.com</p>
</div>

<h3 style="text-align:center;">Call for Interview</h3>

<div class="content">
    <div class="text">
        <p>Dear <strong><?= htmlspecialchars($row['full_name']); ?></strong>,</p>
        <p>We are pleased to inform you that you have been shortlisted for the position of <strong><?= htmlspecialchars($row['job_title']); ?></strong>.</p>

        <p><strong>Interview Details:</strong></p>
        <p>Date: <?= htmlspecialchars($row['interview_date']); ?><br>
        Time: <?= htmlspecialchars($row['interview_time']); ?><br>
        Mode: Online (Google Meet)</p>

        <div class="meet-link">
            <p><strong>Join Interview:</strong> 
                <a href="<?= htmlspecialchars($row['meet_link']); ?>" target="_blank">
                    <?= htmlspecialchars($row['meet_link']); ?>
                </a>
            </p>
        </div>
    </div>

</div>

<div class="instructions">
    <p><strong>Instructions:</strong></p>
    <ol>
        <li>Ensure you have a stable internet connection and a working camera & microphone.</li>
        <li>Join the Google Meet link at least 10 minutes before the scheduled time.</li>
        <li>Keep your resume and valid government ID proof ready for verification.</li>
        <li>Maintain a professional dress code during the interview.</li>
        <li>Sit in a quiet, well-lit place with no background disturbances.</li>
        <li>Use the same name as your application when joining the meeting.</li>
        <li>Test your audio and video before joining the call.</li>
        <li>If you face technical issues, inform us immediately via email or phone.</li>
    </ol>
</div>
</div>
    <button onclick="downloadPDF()" style="padding:10px 20px; background:#007BFF; color:#fff; border:none; border-radius:4px;">Download PDF</button>

<script>
function downloadPDF() {
  const element = document.getElementById('interview-letter');
  const opt = {
    margin:       [10, 10, 10, 10],
    filename:     'Interview_Letter.pdf',
    image:        { type: 'jpeg', quality: 0.98 },
    html2canvas:  { scale: 2 },
    jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
  };
  html2pdf().set(opt).from(element).save();
}
</script>



</body>
</html>
<?php $conn->close(); ?>
