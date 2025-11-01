<?php
session_start();

// DB connect
$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Get total jobs count for dropdown
$countRes = $conn->query("SELECT COUNT(*) AS total FROM jobs");
$countRow = $countRes->fetch_assoc();
$totalCount = $countRow['total'];

// Handle deletion
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $getImage = $conn->query("SELECT image FROM jobs WHERE job_id = $id");
    if ($getImage && $getImage->num_rows > 0) {
        $imgRow = $getImage->fetch_assoc();
        if (!empty($imgRow['image']) && file_exists("uploads/" . $imgRow['image'])) {
            unlink("uploads/" . $imgRow['image']);
        }
    }
    $conn->query("DELETE FROM jobs WHERE job_id = $id");
    header("Location: manage_jobs.php");
    exit();
}

// ✅ Get limit from URL
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;
if ($limit < 0) $limit = 0;

// ✅ Build query
$sql = "SELECT * FROM jobs ORDER BY posted_date ASC";
if ($limit > 0) {
    $sql .= " LIMIT $limit";
}

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manage Jobs</title>
  <!-- ✅ Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- ✅ Font Awesome for icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      padding: 40px;
      background: #f9f9f9;
    }
    h2 {
      text-align: center;
      margin-bottom: 30px;
    }
    .limit-form {
      text-align: right;
      margin-bottom: 20px;
    }
    .limit-form form {
      display: inline-flex;
      align-items: center;
      gap: 10px;
    }
    .thumb {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 4px;
    }
    table th {
      background: #000000ff;
      color: #fff;
    }
    a.icon-btn {
      color: #fff;
      padding: 6px 10px;
      border-radius: 4px;
      margin-right: 5px;
    }
    a.icon-btn.edit {
      background: #28a745;
    }
    a.icon-btn.delete {
      background: #dc3545;
    }
    a.icon-btn:hover {
      opacity: 0.85;
      text-decoration: none;
    }

    /* ✅ Sticky table header */
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
  <h2>Manage Job Posts</h2>

  <div class="limit-form">
    <form method="GET" action="">
      <label for="limit" class="form-label mb-0"><strong>Show records:</strong></label>
      <select name="limit" id="limit" class="form-select d-inline w-auto">
        <option value="2" <?php if ($limit == 2) echo 'selected'; ?>>2</option>
        <option value="5" <?php if ($limit == 5) echo 'selected'; ?>>5</option>
        <option value="10" <?php if ($limit == 10) echo 'selected'; ?>>10</option>
        <option value="0" <?php if ($limit == 0) echo 'selected'; ?>>All (<?= $totalCount ?>)</option>
      </select>
      <button type="submit" class="btn btn-dark">Apply</button>
    </form>
  </div>

  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Posted Date</th>
        <th>From Date</th>
        <th>To Date</th>
        <th>Image</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['job_id']) ?></td>
        <td><?= htmlspecialchars($row['title']) ?></td>
        <td><?= htmlspecialchars($row['posted_date']) ?></td>
        <td><?= htmlspecialchars($row['from_date']) ?></td>
        <td><?= htmlspecialchars($row['to_date']) ?></td>
        <td>
          <?php if (!empty($row['image'])): ?>
            <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="Job Image" class="thumb">
          <?php else: ?>
            <span class="text-muted">No Image</span>
          <?php endif; ?>
        </td>
        <td>
          <a href="edit_jobs.php?id=<?= $row['job_id'] ?>" class="icon-btn edit" title="Edit">
            <i class="fas fa-edit"></i>
          </a>
          <a href="manage_jobs.php?delete_id=<?= $row['job_id'] ?>" class="icon-btn delete"
             onclick="return confirm('Are you sure you want to delete this job?')" title="Delete">
            <i class="fas fa-trash-alt"></i>
          </a>
        </td>
      </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr>
        <td colspan="7" class="text-center">No jobs found.</td>
      </tr>
    <?php endif; ?>
    </tbody>
  </table>
  </div>


<!-- ✅ Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php $conn->close(); ?>
