<?php
session_start();

$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure employee is logged in
if (!isset($_SESSION['employee_email'])) {
    header("Location: login1.php");
    exit();
}
$user_email = $conn->real_escape_string($_SESSION['employee_email']);

// Get employee id
$stmt = $conn->prepare("SELECT id FROM employees WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    echo "<div class='alert alert-danger'>Employee not found.</div>";
    exit();
}
$employee = $res->fetch_assoc();
$employee_id = (int)$employee['id'];
$stmt->close();

// Check whether 'deadline' column exists in projects
$colRes = $conn->query("SHOW COLUMNS FROM projects LIKE 'deadline'");
$hasDeadline = ($colRes && $colRes->num_rows > 0);

// Build query depending on whether deadline exists
if ($hasDeadline) {
    $sql = "SELECT project_name, description, deadline, progress, status, updated_at
            FROM projects
            WHERE employee_id = ?
            ORDER BY CASE WHEN deadline IS NULL THEN 1 ELSE 0 END, deadline ASC, updated_at DESC";
} else {
    // fallback: use updated_at only
    $sql = "SELECT project_name, description, updated_at, progress, status
            FROM projects
            WHERE employee_id = ?
            ORDER BY updated_at DESC";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$projects = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>My Project Notifications</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0">📌 Project Notifications</h3>
            <span class="badge bg-light text-dark">Employee Dashboard</span>
        </div>
        <div class="card-body">
            <?php if ($projects && $projects->num_rows > 0): ?>
                <div class="row g-4">
                    <?php while ($row = $projects->fetch_assoc()): ?>
                        <?php
                            $progress = (int)($row['progress'] ?? 0);
                            // If deadline exists in DB use it, otherwise compute from updated_at
                            if ($hasDeadline) {
                                $deadline_raw = $row['deadline'];
                                $deadline_ts = $deadline_raw ? strtotime($deadline_raw) : null;
                                $today = strtotime(date("Y-m-d"));
                                $days_left = $deadline_ts ? ceil(($deadline_ts - $today) / 86400) : null;
                                $badge_class = ($days_left !== null) ? ($days_left <= 3 ? 'danger' : ($days_left <= 7 ? 'warning' : 'success')) : 'secondary';
                                $deadline_display = $deadline_raw ? date('d M Y', $deadline_ts) : 'Not set';
                            } else {
                                $updated_ts = strtotime($row['updated_at']);
                                $deadline_ts = null;
                                $days_left = null;
                                $badge_class = 'secondary';
                                $deadline_display = $updated_ts ? date('d M Y', $updated_ts) : '—';
                            }
                        ?>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="card-title text-primary mb-2"><?= htmlspecialchars($row['project_name']) ?></h5>

                                    <?php if (!empty($row['description'])): ?>
                                        <p class="card-text text-muted mb-2"><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                                    <?php endif; ?>

                                    <p class="card-text mb-1">
                                        <strong>Status:</strong>
                                        <span class="badge bg-info text-dark"><?= htmlspecialchars($row['status'] ?? 'In Progress') ?></span>
                                    </p>

                                    <p class="card-text mb-2">
                                        <strong>Progress:</strong> <?= $progress ?>%
                                        <div class="progress mt-1" style="height:8px;">
                                            <div class="progress-bar" role="progressbar" style="width: <?= $progress ?>%;" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </p>

                                    <p class="card-text mt-2">
                                        <strong><?= $hasDeadline ? 'Deadline:' : 'Last updated:' ?></strong>
                                        <span class="badge bg-<?= $badge_class ?>">
                                            <?= htmlspecialchars($deadline_display) ?>
                                        </span>

                                        <?php if ($hasDeadline): ?>
                                            <?php if ($days_left === null): ?>
                                                <small class="text-muted"> (No deadline set)</small>
                                            <?php elseif ($days_left > 0): ?>
                                                <small class="text-muted"> (<?= $days_left ?> days left)</small>
                                            <?php elseif ($days_left === 0): ?>
                                                <small class="text-danger fw-bold"> (Due today)</small>
                                            <?php else: ?>
                                                <small class="text-danger fw-bold"> (Overdue)</small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <!-- show nothing extra for updated_at -->
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center">No projects assigned yet.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
