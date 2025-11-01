<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get rejected applications
$sql = "
  SELECT ja.*, j.title AS job_title 
  FROM job_applications ja 
  JOIN jobs j ON ja.job_id = j.job_id 
  WHERE ja.status = 'Rejected'
  ORDER BY ja.applied_on DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Rejected Candidates</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body { padding: 40px; background: #f8f9fa; }
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
  <div class="container">
    <h1 class="text-center mb-4">Rejected Candidates</h1>

    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle bg-white shadow-sm">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Candidate Name</th>
            <th>Job Name</th>
            <th>Contact</th>
            <th>Applied On</th>
            <th>Rejection Reason</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['job_title']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= date('Y-m-d', strtotime($row['applied_on'])) ?></td>
                <td><?= nl2br(htmlspecialchars($row['reason'] ?? '')) ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center text-muted fst-italic">No rejected candidates found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
   </div>
</body>
</html>

<?php $conn->close(); ?>
