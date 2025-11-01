<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "company_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch project + employee data
$query = "
  SELECT p.*, e.full_name AS employee_name 
  FROM projects p 
  JOIN employees e ON p.employee_id = e.id 
  ORDER BY p.updated_at DESC
";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>HR Dashboard - Project Reports</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f8f9fa; padding: 20px; }
    h2 { text-align:center; color:#333; margin-bottom:30px; }
    table { width:100%; border-collapse:collapse; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 0 10px rgba(0,0,0,0.1); }
    th, td { padding:12px; text-align:left; border-bottom:1px solid #ddd; vertical-align: middle; }
    th { background:#007BFF; color:#fff; }
    tr:hover { background:#f1f1f1; }
    .progress-bar { height:10px; background:#ddd; border-radius:5px; margin-top:5px; overflow:hidden; }
    .progress-fill { height:10px; border-radius:5px; }
    .in-progress { background:orange; color:#fff; padding:4px 8px; border-radius:5px; font-size:12px; display:inline-block; }
    .completed { background:green; color:#fff; padding:4px 8px; border-radius:5px; font-size:12px; display:inline-block; }
    .download-link { text-decoration:none; color:#007BFF; font-weight:bold; }
    .download-link:hover { text-decoration:underline; }
    .no-file { color:gray; font-style:italic; }
    .thumb { max-width:80px; max-height:60px; display:block; margin-top:6px; border-radius:4px; }
    td.center { text-align:center; }
  </style>
</head>
<body>

  <h2>HR Dashboard – All Employee Projects</h2>

  <table>
    <thead>
      <tr>
        <th>Employee</th>
        <th>Project Name</th>
        <th>Progress</th>
        <th>Status</th>
        <th>Remarks</th>
        <th>Last Updated</th>
        <th class="center">Latest File</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($result && $result->num_rows > 0):
          while ($row = $result->fetch_assoc()):
              $employee_name = htmlspecialchars($row['employee_name'] ?? '');
              $project_name  = htmlspecialchars($row['project_name'] ?? '');
              $progress      = (int)($row['progress'] ?? 0);
              $status_text   = htmlspecialchars($row['status'] ?? '');
              $remarks       = htmlspecialchars($row['remarks'] ?? '');
              $updated_at    = !empty($row['updated_at']) ? date('d M Y, H:i', strtotime($row['updated_at'])) : '-';

              // ✅ File Path Normalization
              $fileField = trim($row['file_path'] ?? $row['progress_file'] ?? '');
              $filePath = '';
              
              if ($fileField !== '') {
                  if (file_exists($fileField)) {
                      $filePath = $fileField; // already valid path
                  } elseif (file_exists("uploads/" . $fileField)) {
                      $filePath = "uploads/" . $fileField;
                  } elseif (file_exists("uploads/progress/" . $fileField)) {
                      $filePath = "uploads/progress/" . $fileField;
                  }
              }
      ?>
      <tr>
        <td><?= $employee_name ?></td>
        <td><?= $project_name ?></td>
        <td>
          <?= $progress ?>%
          <div class="progress-bar">
            <div class="progress-fill" style="width:<?= $progress ?>%; background:<?= ($progress >= 100 ? 'green' : 'orange') ?>;"></div>
          </div>
        </td>
        <td>
          <?php if ($progress >= 100): ?>
            <span class="completed">Completed</span>
          <?php else: ?>
            <span class="in-progress">In Progress</span>
          <?php endif; ?>
        </td>
        <td><?= $remarks ?></td>
        <td><?= $updated_at ?></td>
        <td class="center">
          <?php
          if ($filePath && file_exists($filePath)) {
              $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
              $label = in_array($ext, ['zip']) ? "Download ZIP" : "View File";
              echo "<a class='download-link' href='" . htmlspecialchars($filePath) . "' target='_blank'>{$label}</a>";

              // ✅ Image preview for image files
              if (in_array($ext, ['jpg','jpeg','png','gif'])) {
                  echo "<br><img src='" . htmlspecialchars($filePath) . "' class='thumb' alt='file preview'>";
              }
          } elseif ($fileField !== '') {
              echo "<span class='no-file'>File not found on server (" . htmlspecialchars($fileField) . ")</span>";
          } else {
              echo "<span class='no-file'>No file uploaded</span>";
          }
          ?>
        </td>
      </tr>
      <?php
          endwhile;
      else:
      ?>
        <tr><td colspan="7" style="text-align:center;">No project data found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

</body>
</html>

<?php
$conn->close();
?>
