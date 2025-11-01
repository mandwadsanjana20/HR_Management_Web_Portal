<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Ensure employee is logged in
if (!isset($_SESSION['employee_id'])) {
    header("Location: login1.php");
    exit();
}

$employee_id = intval($_SESSION['employee_id']);

// ✅ Fetch projects assigned to this employee
$query = "SELECT * FROM projects WHERE employee_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>My Projects</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f8f9fa;
      padding: 0px;
    }
    h3 {
      text-align: center;
      color: #333;
      font-size: 22px;
      margin-bottom: 20px;
    }
    .card {
      border: 1px solid #ccc;
      border-radius: 10px;
      padding: 15px;
      margin: 15px auto;
      width: 420px;
      background: white;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .progress-bar {
      height: 10px;
      background: #eee;
      border-radius: 5px;
      margin-bottom: 10px;
      overflow: hidden;
    }
    .progress-bar-fill {
      height: 10px;
      background: #28a745;
      border-radius: 5px;
      transition: width 0.4s;
    }
    button {
      background: #007BFF;
      color: white;
      padding: 8px 15px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    button:hover {
      background: #0056b3;
    }
    button:disabled {
      background: gray;
      cursor: not-allowed;
    }
    textarea {
      width: 100%;
      height: 60px;
      resize: none;
    }
    input[type="number"], input[type="file"] {
      width: 100%;
      padding: 6px;
    }
    small {
      color: gray;
      font-size: 12px;
    }
    .completed {
      color: green;
      font-weight: bold;
      text-align: center;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <h3>My Assigned Projects</h3>

  <?php if ($result->num_rows === 0): ?>
      <p style="text-align:center;">⚠️ No projects assigned yet.</p>
  <?php else: ?>
      <?php while($project = $result->fetch_assoc()): ?>
      <?php $progress = intval($project['progress']); ?>
      <div class="card">
        <h4><?= htmlspecialchars($project['project_name'] ?? '') ?></h4>
        <p>Status: <strong><?= htmlspecialchars($project['status'] ?? '') ?></strong></p>
        <p>Progress: <?= $progress ?>%</p>
        <div class="progress-bar">
          <div class="progress-bar-fill" style="width:<?= $progress ?>%"></div>
        </div>

        <?php if ($progress < 100): ?>
        <!-- ✅ Allow updates only if progress < 100% -->
        <form action="update_progress.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="project_id" value="<?= htmlspecialchars($project['id'] ?? '') ?>">

          <label>Update Progress (%):</label><br>
          <input type="number" name="progress" min="<?= $progress ?>" max="100"
                 value="<?= $progress ?>" required class="progress-input"><br><br>

          <label>Remarks:</label><br>
          <textarea name="remarks" placeholder="Enter remarks..." required><?= htmlspecialchars($project['remarks'] ?? '') ?></textarea><br><br>
          
          <label>Upload Project File:</label><br>
          <input type="file" name="project_file"
                 accept="<?= ($progress >= 100) ? '.zip' : '.pdf,.doc,.docx,.jpg,.jpeg,.png' ?>"
                 class="file-input">
          <small class="file-note">
            <?= ($progress >= 100)
                ? 'Allowed: ZIP file only (final submission)'
                : 'Allowed: PDF, DOC, DOCX, JPG, PNG' ?>
          </small><br><br>

          <button type="submit">Update</button>
        </form>

        <?php else: ?>
        <!-- 🚫 Disable update form if completed -->
        <div class="completed">✅ Project Completed (100%)<br>No further updates allowed.</div>
        <?php endif; ?>
      </div>
      <?php endwhile; ?>
  <?php endif; ?>

  <script>
  // ✅ JS: Change allowed file type dynamically when progress changes
  document.querySelectorAll('.progress-input').forEach((input, index) => {
      input.addEventListener('input', function() {
          const progress = parseInt(this.value) || 0;
          const fileInput = document.querySelectorAll('.file-input')[index];
          const note = document.querySelectorAll('.file-note')[index];

          if (progress >= 100) {
              fileInput.setAttribute('accept', '.zip');
              note.textContent = 'Allowed: ZIP file only (final submission)';
          } else {
              fileInput.setAttribute('accept', '.pdf,.doc,.docx,.jpg,.jpeg,.png');
              note.textContent = 'Allowed: PDF, DOC, DOCX, JPG, PNG';
          }
      });
  });
  </script>
</body>
</html>
