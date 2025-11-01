<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Employee Reports</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: "Poppins", sans-serif;
      background: #f4f6f9;
      margin: 0;
      padding: 0;
    }
    h1 {
      text-align: center;
      color: #333;
      margin: 30px 0;
    }
    .container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 30px;
    }
    .card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      padding: 20px;
      width: 400px;
      text-align: center;
    }
    canvas {
      width: 100% !important;
      height: 280px !important;
    }
  </style>
</head>
<body>
  <h1>My Reports Dashboard</h1>

  <div class="container">
    <div class="card">
      <h3>Project Performance</h3>
      <canvas id="projectChart"></canvas>
    </div>

    <div class="card">
      <h3>Attendance Summary</h3>
      <canvas id="attendanceChart"></canvas>
    </div>
  </div>

  <script>
  fetch('get_data.php')
    .then(res => res.json())
    .then(data => {
      if (data.error) {
        alert(data.error);
        return;
      }

      // ✅ 1. Project Progress Bar Chart
      const labels = data.projects.map(p => p.project_name);
      const progress = data.projects.map(p => p.progress);

      new Chart(document.getElementById('projectChart'), {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Progress (%)',
            data: progress,
            backgroundColor: progress.map(v => v >= 100 ? '#4CAF50' : '#FFA726'),
            borderRadius: 10
          }]
        },
        options: {
          indexAxis: 'y',
          scales: {
            x: {
              beginAtZero: true,
              max: 100,
              ticks: { callback: v => v + '%' },
              title: { display: true, text: 'Progress (%)' }
            }
          },
          plugins: {
            legend: { display: false },
            title: { display: true, text: 'Project Progress Overview' }
          }
        }
      });

      // ✅ 2. Attendance Donut Chart
      const present = data.attendance.present;
      const absent = data.attendance.absent;

      new Chart(document.getElementById('attendanceChart'), {
        type: 'doughnut',
        data: {
          labels: ['Present', 'Absent'],
          datasets: [{
            data: [present, absent],
            backgroundColor: ['#4CAF50', '#FF5252'],
            hoverOffset: 10
          }]
        },
        options: {
          plugins: {
            title: { display: true, text: `Attendance for ${data.attendance.month}` },
            legend: { position: 'bottom' }
          }
        }
      });
    })
    .catch(err => console.error('Error loading reports:', err));
  </script>
</body>
</html>
