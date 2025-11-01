<?php
session_start();

// Only admin access
if (!isset($_SESSION['admin_id'])) {
    header("Location: login1.php");
    exit();
}

// DB connection
$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Count queries
$candidateCount = $conn->query("SELECT COUNT(*) AS total FROM candidates")->fetch_assoc()['total'];
$employeeCount = $conn->query("SELECT COUNT(*) AS total FROM employees")->fetch_assoc()['total'];
$attendanceCount = $conn->query("SELECT COUNT(*) AS total FROM attendance")->fetch_assoc()['total'];
$jobsCount = $conn->query("SELECT COUNT(*) AS total FROM jobs")->fetch_assoc()['total'];
$applicationsCount = $conn->query("SELECT COUNT(*) AS total FROM job_applications")->fetch_assoc()['total'];
$projectssCount = $conn->query("SELECT COUNT(*) AS total FROM projects")->fetch_assoc()['total'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>

  <!-- Bootstrap 4 CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    body {
      background:#ffffff;
      color: #000;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      padding: 40px 20px;
    }

    h1 {
      text-align: center;
      margin-bottom: 40px;
      color: #000;
      text-shadow: 2px 2px 8px rgba(0,0,0,0.6);
    }

    .card-custom {
      color: #fff;
      border: none;
      border-radius: 15px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }

    .card-custom:hover {
      transform: translateY(-8px);
      box-shadow: 0 12px 25px rgba(0,0,0,0.6);
    }

    .card-body {
      text-align: center;
      padding: 30px 20px;
    }

    .card-body h2 {
      font-size: 3rem;
      margin-bottom: 10px;
    }

    .card-body p {
      font-size: 1.2rem;
      font-weight: 600;
      margin: 0;
    }

    .card-body i {
      font-size: 2.5rem;
      margin-bottom: 15px;
      display: block;
    }

    /* Gradient backgrounds for cards */
    .bg-gradient-blue { background: linear-gradient(45deg, #00c6ff, #0072ff); }
    .bg-gradient-green { background: linear-gradient(45deg, #00b09b, #96c93d); }
    .bg-gradient-purple { background: linear-gradient(45deg, #7f00ff, #e100ff); }
    .bg-gradient-orange { background: linear-gradient(45deg, #ff8008, #ffc837); }
    .bg-gradient-red { background: linear-gradient(45deg, #ff416c, #ff4b2b); }
    .bg-gradient-gray { background: linear-gradient(45deg, #485563, #29323c); }
  </style>
</head>
<body>

  <h1>HR Dashboard</h1>

  <div class="container">
    <div class="row justify-content-center">

      <div class="col-lg-4 col-md-6 mb-4">
        <div class="card card-custom bg-gradient-blue">
          <div class="card-body">
            <i class="fas fa-user-graduate"></i>
            <h2><?= $candidateCount; ?></h2>
            <p>Candidates</p>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-6 mb-4">
        <div class="card card-custom bg-gradient-green">
          <div class="card-body">
            <i class="fas fa-user-tie"></i>
            <h2><?= $employeeCount; ?></h2>
            <p>Employees</p>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-6 mb-4">
        <div class="card card-custom bg-gradient-purple">
          <div class="card-body">
            <i class="fas fa-briefcase"></i>
            <h2><?= $jobsCount; ?></h2>
            <p>Posted Jobs</p>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-6 mb-4">
        <div class="card card-custom bg-gradient-orange">
          <div class="card-body">
            <i class="fas fa-file-alt"></i>
            <h2><?= $applicationsCount; ?></h2>
            <p>Applied Jobs</p>
          </div>
        </div>
      </div>

     

      <div class="col-lg-4 col-md-6 mb-4">
        <div class="card card-custom bg-gradient-gray">
          <div class="card-body">
            <i class="fas fa-project-diagram"></i>
            <h2><?= $projectssCount; ?></h2>
            <p>Total Projects</p>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
