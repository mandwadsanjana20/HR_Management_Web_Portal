<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "company_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Total working days (adjust as per logic)
$total_days = 30;

$query = "
SELECT 
    e.id AS employee_id,
    e.full_name,
    e.email,

    COUNT(DISTINCT a.date) AS days_present,

    SUM(CASE WHEN p.status = 'Completed' THEN 1 ELSE 0 END) AS completed_projects,
    COUNT(p.id) AS total_projects

FROM employees e
LEFT JOIN attendance a ON e.id = a.employee_id
LEFT JOIN projects p ON e.id = p.employee_id
LEFT JOIN payroll pr ON e.id = pr.employee_id
GROUP BY e.id
";

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Employee Performance Dashboard</title>
  <style>
    body {
      font-family: "Poppins", sans-serif;
      background: #eef1f6;
      margin: 0;
      padding: 0;
    }
    header {
      background: #eef1f6;
      color: black;
      text-align: center;
      padding: 20px 0;
      box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    .container {
      width: 90%;
      max-width: 1200px;
      margin: 40px auto;
      background: white;
      border-radius: 12px;
      box-shadow: 0 6px 15px rgba(0,0,0,0.1);
      padding: 20px 30px 40px;
    }
    h1 {
      margin: 0;
      font-size: 26px;
      letter-spacing: 1px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 25px;
      font-size: 15px;
    }
    th, td {
      padding: 12px 10px;
      text-align: center;
      border-bottom: 1px solid #ddd;
    }
    th {
      background: #3498db;
      color: white;
      text-transform: uppercase;
      font-size: 13px;
      letter-spacing: 0.5px;
    }
    tr:hover {
      background: #f9f9f9;
    }
    .performance-bar {
      height: 10px;
      border-radius: 5px;
      background: #ddd;
      position: relative;
      overflow: hidden;
    }
    .performance-bar span {
      position: absolute;
      height: 100%;
      background: linear-gradient(90deg, #27ae60, #2ecc71);
      border-radius: 5px;
    }
    .score {
      font-weight: bold;
      color: #2c3e50;
    }
    .footer {
      text-align: center;
      margin-top: 40px;
      color: #666;
      font-size: 14px;
    }
     .table-responsive {
    /* Remove max-height and overflow */
    max-height: none;
    overflow-y: visible;
}
   .table thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: #212529;
            color: #fff;
        }
  </style>
</head>
<body>
  <header>
    <h1>Employee Performance Dashboard</h1>
  </header>

  <div class="container">
     <table class="table table-bordered table-striped table-hover">
      <thead class="thead-dark">
      <tr>
        <th>ID</th>
        <th>Employee Name</th>
        <th>Email</th>
        <th>Attendance %</th>
        <th>Projects Completed</th>
        <th>Total Projects</th>
        <th>Performance</th>
      </tr>

      <?php
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $attendance_score = ($row['days_present'] / $total_days) * 40;
          $project_score = ($row['total_projects'] > 0)
              ? (($row['completed_projects'] / $row['total_projects']) * 60)
              : 0;
          $total_performance = round($attendance_score + $project_score, 2);
          $performance_percent = min(100, $total_performance); // Cap at 100%

          echo "<tr>
            <td>{$row['employee_id']}</td>
            <td>{$row['full_name']}</td>
            <td>{$row['email']}</td>
            <td>" . round(($row['days_present'] / $total_days) * 100, 2) . "%</td>
            <td>{$row['completed_projects']}</td>
            <td>{$row['total_projects']}</td>
            <td>
              <div class='performance-bar'><span style='width: {$performance_percent}%;'></span></div>
              <div class='score'>{$total_performance}/100</div>
            </td>
          </tr>";
        }
      } else {
        echo "<tr><td colspan='7'>No performance data found</td></tr>";
      }
      ?>
    </table>
  </div>

  <div class="footer">
    <p>© <?php echo date('Y'); ?> HR Management Portal | All Rights Reserved</p>
  </div>
</body>
</html>

<?php $conn->close(); ?>
