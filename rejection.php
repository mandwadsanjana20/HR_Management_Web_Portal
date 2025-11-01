<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$status_filter = 'Rejected';

// Use prepared statements to avoid SQL injection
$stmt = $conn->prepare("
    SELECT ja.id, ja.candidate_id, ja.job_id, ja.interview_date, ja.interview_time,
           ja.selection_status, ja.rejection_reason, c.full_name, c.email, j.title AS job_title
    FROM job_applications ja
    JOIN candidates c ON ja.candidate_id = c.id
    JOIN jobs j ON ja.job_id = j.job_id
    WHERE LOWER(ja.selection_status) = LOWER(?)
    ORDER BY ja.interview_date DESC
");
$stmt->bind_param("s", $status_filter);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Rejected Candidates</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body { padding: 30px; }
    h2 { text-align: center; margin-bottom: 20px; }
     /* ✅ Sticky table header */
  .table-responsive {
    max-height: 600px;
    overflow-y: auto;
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

<h2>Rejected Candidates</h2>

<div class="table-responsive">
<table class="table table-bordered">
  <thead class="table-dark">
    <tr>
      <th>Candidate</th>
      <th>Email</th>
      <th>Job</th>
      <th>Interview Date</th>
      <th>Interview Time</th>
      <th>Rejection Reason</th>
    </tr>
  </thead>
  <tbody>
  <?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
  <td><?= htmlspecialchars($row['full_name']) ?></td>
  <td><?= htmlspecialchars($row['email']) ?></td>
  <td><?= htmlspecialchars($row['job_title']) ?></td>
  <td><?= !empty($row['interview_date']) ? htmlspecialchars($row['interview_date']) : 'N/A' ?></td>
  <td><?= !empty($row['interview_time']) ? htmlspecialchars($row['interview_time']) : 'N/A' ?></td>
  <td><?= htmlspecialchars($row['rejection_reason'] ?? 'No reason provided') ?></td>
</tr>

    <?php endwhile; ?>
  <?php else: ?>
    <tr>
      <td colspan="6" class="text-center">No rejected candidates found.</td>
    </tr>
  <?php endif; ?>
  </tbody>
</table>
</div>

</body>
</html>
