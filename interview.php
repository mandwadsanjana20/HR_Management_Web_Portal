<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get limit from GET param or default to show all
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;

// Calculate min and max date
$minDate = date('Y-m-d'); // Today
$maxDate = date('Y-m-d', strtotime('+1 month')); // One month ahead

// Time range (8 AM to 6 PM)
$minTime = "08:00";
$maxTime = "18:00";

// Base SQL
$sqlBase = "
SELECT 
  ja.id, ja.candidate_id, ja.job_id, ja.interview_date, ja.interview_time,
  c.full_name, c.email, c.contact,
  j.title AS job_title
FROM 
  job_applications ja
JOIN candidates c ON ja.candidate_id = c.id
JOIN jobs j ON ja.job_id = j.job_id
WHERE ja.status = 'Shortlisted'
ORDER BY ja.interview_date IS NULL ASC, ja.interview_date ASC
";

// Get total shortlisted records count
$countResult = $conn->query("
  SELECT COUNT(*) as total FROM job_applications WHERE status = 'Shortlisted'
");
$totalRecords = $countResult->fetch_assoc()['total'];

// Append LIMIT if required
if ($limit > 0) {
    $sql = $sqlBase . " LIMIT " . $limit;
} else {
    $sql = $sqlBase;
}

$result = $conn->query($sql);
$showingRecords = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Shortlisted Candidates</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background: #f8f9fa;
      padding: 2rem;
      font-family: Arial, sans-serif;
    }
    table.form-table input[type="date"],
    table.form-table input[type="time"] {
      max-width: 160px;
    }
    form.inline-form {
      margin: 0;
    }
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

<div class="container">
  <h2 class="text-center mb-4">Shortlisted Candidates for Interview</h2>

  <div class="d-flex justify-content-end mb-3">
    <form method="GET" class="d-flex align-items-center gap-2">
      <label for="limit" class="form-label mb-0 fw-semibold">Show records:</label>
      <select name="limit" id="limit" class="form-select form-select-sm" style="width: auto;">
        <option value="2" <?= ($limit == 2) ? 'selected' : '' ?>>2</option>
        <option value="5" <?= ($limit == 5) ? 'selected' : '' ?>>5</option>
        <option value="10" <?= ($limit == 10) ? 'selected' : '' ?>>10</option>
        <option value="0" <?= ($limit == 0) ? 'selected' : '' ?>>All</option>
      </select>
      <button type="submit" class="btn btn-primary btn-sm">Apply</button>
    </form>
  </div>

  <div class="table-responsive">
  <table class="table table-bordered table-hover form-table align-middle bg-white">
    <thead class="table-dark text-center">
      <tr>
        <th>Candidate Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Job Title</th>
        <th>Interview Date</th>
        <th>Interview Time</th>
        <th>Update</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result && $showingRecords > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <form method="POST" action="update_interview.php" class="inline-form d-flex gap-2 align-items-center">
              <td class="text-center align-middle"><?= htmlspecialchars($row['full_name']) ?></td>
              <td class="text-center align-middle"><?= htmlspecialchars($row['email']) ?></td>
              <td class="text-center align-middle"><?= htmlspecialchars($row['contact']) ?></td>
              <td class="text-center align-middle"><?= htmlspecialchars($row['job_title']) ?></td>
              <td class="text-center align-middle">
                <input 
                  type="date" 
                  name="interview_date" 
                  value="<?= htmlspecialchars($row['interview_date'] ?? '') ?>"
                  min="<?= $minDate ?>" 
                  max="<?= $maxDate ?>"
                  class="form-control form-control-sm"
                  required
                >
              </td>
              <td class="text-center align-middle">
                <input 
                  type="time" 
                  name="interview_time" 
                  value="<?= htmlspecialchars($row['interview_time'] ?? '') ?>"
                  min="<?= $minTime ?>" 
                  max="<?= $maxTime ?>"
                  class="form-control form-control-sm"
                  required
                >
              </td>
              <td class="text-center align-middle">
                <input type="hidden" name="application_id" value="<?= $row['id'] ?>">
                <button type="submit" class="btn btn-success btn-sm">Save</button>
              </td>
            </form>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="7" class="text-center text-muted fst-italic">No shortlisted candidates yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  </div>
</div>

<!-- Bootstrap JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>