<?php
$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$project_msg = "";

// ---------------------------------------------
// Handle Add Project form submission
// ---------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_project'])) {
    $job_id = intval($_POST['job_id']);
    $project_name = trim($_POST['project_name']);
    $description = trim($_POST['description']);
    $deadline = $_POST['deadline'];
    $status = 'In Progress';
    $progress = 0;

    if ($job_id && $project_name && $deadline) {
        $stmt = $conn->prepare("INSERT INTO projects (employee_id, project_name, progress, description, status, updated_at) VALUES (?, ?, ?, ?, ?, NOW())");

        // No specific employee now, set employee_id = 0 or NULL
        $employee_id = 0;

        $stmt->bind_param("isiss", $employee_id, $project_name, $progress, $description, $status);

        if ($stmt->execute()) {
            $project_msg = "<div class='alert alert-success'>✅ Project added successfully.</div>";
        } else {
            $project_msg = "<div class='alert alert-danger'>❌ Error adding project: {$conn->error}</div>";
        }
    } else {
        $project_msg = "<div class='alert alert-warning'>⚠ Please fill all required fields.</div>";
    }
}

// ---------------------------------------------
// Fetch job titles for dropdown
// ---------------------------------------------
$jobs = $conn->query("SELECT job_id, title FROM jobs ORDER BY title ASC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Add Project - HR Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body class="bg-light">
  <div class="container my-5">
    <h2 class="mb-4 text-center">HR Panel - Add New Project</h2>

    <div class="card shadow">
      <div class="card-header bg-primary text-white fw-bold">Add Project</div>
      <div class="card-body">
        <?= $project_msg ?>

        <form method="post">
          <div class="mb-3">
            <label for="job_id" class="form-label">Select Job Title <span class="text-danger">*</span></label>
            <select id="job_id" name="job_id" class="form-select" required>
              <option value="">-- Choose Job Title --</option>
              <?php foreach ($jobs as $job): ?>
                <option value="<?= $job['job_id'] ?>"><?= htmlspecialchars($job['title']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label for="project_name" class="form-label">Project Name <span class="text-danger">*</span></label>
            <input type="text" id="project_name" name="project_name" class="form-control" required>
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" rows="3"></textarea>
          </div>

          <div class="mb-3">
            <label for="deadline" class="form-label">Deadline Date <span class="text-danger">*</span></label>
            <input type="date" id="deadline" name="deadline" class="form-control" required>
          </div>

          <button type="submit" name="add_project" class="btn btn-success w-100">Add Project</button>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Disable past dates in deadline calendar
    document.addEventListener("DOMContentLoaded", function() {
      const today = new Date().toISOString().split("T")[0];
      document.getElementById("deadline").setAttribute("min", today);
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
