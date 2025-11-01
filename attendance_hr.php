<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// ✅ Get all employees with their job title
$employees = $conn->query("
    SELECT e.id,e.employee_id, e.full_name, j.title AS job_title
    FROM employees e
    LEFT JOIN jobs j ON e.job_id = j.job_id
    ORDER BY e.full_name ASC
");


// --- Selected week (default = current week)
$startOfWeek = isset($_GET['week']) ? $_GET['week'] : date('Y-m-d', strtotime('monday this week'));
$endOfWeek   = date('Y-m-d', strtotime($startOfWeek . ' +6 days'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Attendance Calendar View</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .status-badge { padding: 6px 10px; border-radius: 10px; font-size: 0.85rem; display: inline-block; }
    .present { background: #d1f7d6; color: #0a7c1e; }
    .absent { background: #ffd6d6; color: #b20000; }
    .leave { background: #fce7b2; color: #a05d00; }
    .active { background: #d6e9ff; color: #0056b3; }
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
<div class="container my-4">
  <h3>Attendance Calendar (<?= date("d M", strtotime($startOfWeek)) ?> - <?= date("d M", strtotime($endOfWeek)) ?>)</h3>

  <!-- Week Filter -->
  <form method="GET" class="row g-3 mb-4">
    <div class="col-auto">
      <input type="date" name="week" class="form-control" value="<?= $startOfWeek ?>">
    </div>
    <div class="col-auto">
      <button type="submit" class="btn btn-primary">Show Week</button>
    </div>
  </form>

  <table class="table table-bordered text-center align-middle">
    <thead class="table-dark">
      <tr>
        <th>Employee</th>
        <?php for ($d=0; $d<7; $d++): 
            $day = date('Y-m-d', strtotime("$startOfWeek +$d days")); ?>
          <th><?= date("D<\b\\r>d M", strtotime($day)) ?></th>
        <?php endfor; ?>
      </tr>
    </thead>
    <tbody>
    <?php while ($emp = $employees->fetch_assoc()): ?>
      <tr>
        <td class="text-start">
          <strong><?= htmlspecialchars($emp['full_name']) ?></strong><br>
          <small><?= htmlspecialchars($emp['job_title']) ?></small>
        </td>
        <?php 
        for ($d=0; $d<7; $d++): 
          $day = date('Y-m-d', strtotime("$startOfWeek +$d days"));

          // --- Default = Absent
          $status = "<span class='status-badge absent'>Absent</span>";

          // --- ✅ Check Leave (from_date → to_date, Approved only)
          $leave = $conn->prepare("
              SELECT 1 FROM leave_requests 
              WHERE employee_id=? 
                AND status='Approved' 
                AND ? BETWEEN from_date AND to_date
          ");
          $leave->bind_param("is", $emp['id'], $day);
          $leave->execute();
          $leave_result = $leave->get_result();
          if ($leave_result->num_rows > 0) {
              $status = "<span class='status-badge leave'>Leave</span>";
          } else {
              // --- Check Attendance
              $att = $conn->prepare("SELECT check_in_time, check_out_time FROM attendance WHERE employee_id=? AND date=?");
              $att->bind_param("is", $emp['id'], $day);
              $att->execute();
              $att_result = $att->get_result();
              if ($row = $att_result->fetch_assoc()) {
                  if (!empty($row['check_in_time'])) {
                      $hours = "-";
                      if (!empty($row['check_out_time'])) {
                          $hours = round((strtotime($row['check_out_time']) - strtotime($row['check_in_time']))/3600, 2) . " Hrs";
                      }
                      $status = "<span class='status-badge present'>✔ $hours</span>";
                  }
              }
          }

          // --- Current Day Special (Active)
          if ($day == date('Y-m-d') && !empty($row['check_in_time']) && empty($row['check_out_time'])) {
              $status = "<span class='status-badge active'>Active</span>";
          }

          echo "<td>$status</td>";
        endfor; ?>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>
<?php $conn->close(); ?>
