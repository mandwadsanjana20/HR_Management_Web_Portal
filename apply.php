<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Candidate must be logged in
if (!isset($_SESSION['candidate_email'])) {
    echo "<script>alert('Please login or register first.'); window.location.href='index.php';</script>";
    exit;
}

$candidate_email = $_SESSION['candidate_email'];
$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;

// Get candidate details
$email_escaped = $conn->real_escape_string($candidate_email);
$sql = "SELECT * FROM candidates WHERE email = '$email_escaped'";
$result = $conn->query($sql);
$candidate = $result->fetch_assoc();

// Get job details
$job = null;
if ($job_id > 0) {
    $job_result = $conn->query("SELECT * FROM jobs WHERE job_id = $job_id");
    if ($job_result && $job_result->num_rows > 0) {
        $job = $job_result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Apply for Job</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
 <style>
    /* Reset and base */
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 40px;
      background: #f4f7fc;
      color: #333;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      min-height: 100vh;
      padding: 20px;
    }

    form {
      background: #fff;
      max-width: 500px;
      width: 100%;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      transition: box-shadow 0.3s ease;
    }
    form:hover {
      box-shadow: 0 12px 30px rgba(0,0,0,0.15);
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      font-weight: 700;
      color: #222;
      letter-spacing: 1.2px;
      text-transform: uppercase;
    }

    label {
      display: block;
      font-weight: 600;
      margin-bottom: 8px;
      color: #555;
      text-transform: uppercase;
      font-size: 0.9rem;
      letter-spacing: 0.05em;
    }

    input[type="text"],
    input[type="email"],
    input[type="file"],
    textarea {
      width: 100%;
      padding: 12px 14px;
      margin-bottom: 20px;
      border: 1.8px solid #ddd;
      border-radius: 8px;
      font-size: 1rem;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
      font-family: inherit;
      resize: vertical;
    }

    input[readonly] {
      background: #f9f9f9;
      color: #888;
      cursor: not-allowed;
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    textarea:focus,
    input[type="file"]:focus {
      border-color: #007bff;
      box-shadow: 0 0 8px rgba(0,123,255,0.3);
      outline: none;
    }

    textarea {
      min-height: 120px;
    }

    button {
      display: block;
      width: 100%;
      padding: 14px 0;
      background: linear-gradient(45deg, #0066ff, #0044cc);
      border: none;
      border-radius: 10px;
      color: #fff;
      font-size: 1.15rem;
      font-weight: 700;
      cursor: pointer;
      box-shadow: 0 5px 12px rgba(0,102,255,0.6);
      transition: background 0.4s ease, box-shadow 0.4s ease;
      user-select: none;
    }
    button:hover {
      background: linear-gradient(45deg, #0044cc, #002a80);
      box-shadow: 0 8px 20px rgba(0,68,204,0.8);
    }
    button:active {
      background: linear-gradient(45deg, #003399, #001f66);
      box-shadow: 0 4px 8px rgba(0,51,153,0.8);
      transform: translateY(2px);
    }

    /* Responsive tweaks */
    @media (max-width: 600px) {
      body {
        margin: 20px 10px;
      }
      form {
        padding: 25px 20px;
      }
    }
  </style>
</head>
<body>
<?php if ($job && $candidate): ?>
  <form action="submit_application.php" method="POST" enctype="multipart/form-data">
    <!-- Back Button with Icon -->
<a href="javascript:history.back()" style="
  display: inline-block;
  margin-bottom: 20px;
  text-decoration: none;
  color: #007bff;
  font-weight: 600;
  font-size: 0.95rem;
">
  <i class="fas fa-arrow-left"></i> Go Back
</a>

    <h2>Applying for: <?php echo htmlspecialchars($job['title']); ?></h2>
    <label>Name:</label><br>
    <input type="text" name="name" value="<?php echo htmlspecialchars($candidate['full_name']); ?>" readonly><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?php echo htmlspecialchars($candidate['email']); ?>" readonly><br>

    <label>Phone:</label><br>
    <input type="text" name="phone" value="<?php echo htmlspecialchars($candidate['contact']); ?>" readonly><br>

    <label>Upload New Resume (PDF, max 2MB, optional):</label><br>
    <input type="file" name="resume" accept=".pdf"><br>

    <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">

    <button type="submit">Submit Application</button>
  </form>

  <script>
    document.querySelector('form').addEventListener('submit', function(e) {
      const fileInput = document.querySelector('input[name="resume"]');
      if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        if (file.type !== "application/pdf") {
          alert("Please upload a PDF file.");
          e.preventDefault();
          return false;
        }
        if (file.size > 2 * 1024 * 1024) {
          alert("File size must be under 2MB.");
          e.preventDefault();
          return false;
        }
      }
    });
  </script>
<?php else: ?>
  <p>Invalid job or candidate information not found.</p>
<?php endif; ?>
</body>
</html>
