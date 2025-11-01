<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    header("Location: login1.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Performance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="card shadow p-4">
    <h3 class="text-center mb-4"><i class="fas fa-chart-line"></i> Employee Performance Report</h3>

    <div class="row">
      <!-- Salary Trend -->
      <div class="col-md-6 mb-4">
        <h5 class="text-center">Salary Trend (Last 6 Months)</h5>
        <canvas id="salaryChart" height="200"></canvas>
      </div>

      <!-- Attendance vs Leaves -->
      <div class="col-md-6 mb-4">
        <h5 class="text-center">Attendance (This Month)</h5>
        <canvas id="attendanceChart" height="200"></canvas>
      </div>
    </div>
  </div>
</div>


<script>
$(document).ready(function(){
    $.getJSON("performance.php", function(data){
        if (data.error) {
            alert(data.error);
            return;
        }

        console.log("Data received:", data); // Debugging

        // Salary Trend Line Chart
        let salaryLabels = data.salary.map(s => s.month).reverse();
        let salaryValues = data.salary.map(s => s.net_salary).reverse();

        new Chart(document.getElementById("salaryChart"), {
            type: 'line',
            data: {
                labels: salaryLabels,
                datasets: [{
                    label: 'Net Salary (₹)',
                    data: salaryValues,
                    borderColor: 'blue',
                    backgroundColor: 'rgba(0,0,255,0.2)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: true } }
            }
        });

        // Attendance vs Absent vs Leaves Doughnut Chart
        new Chart(document.getElementById("attendanceChart"), {
            type: 'doughnut',
            data: {
                labels: ['Present Days', 'Absent Days', 'Leaves'],
                datasets: [{
                    data: [data.attendance, data.absent, data.leaves],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    });
});
</script>

<!-- FontAwesome for icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

</body>
</html>
