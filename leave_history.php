<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Allow only employees
if (!isset($_SESSION['employee_email'])) {
    header("Location: login1.php");
    exit();
}

$user_email = $conn->real_escape_string($_SESSION['employee_email']);

// ✅ Get employee ID and joining date
$result = $conn->query("SELECT id, created_at FROM employees WHERE email = '$user_email'");
if (!$result || $result->num_rows === 0) {
    echo "<div class='alert alert-danger text-center'>Employee not found in the system.</div>";
    exit();
}
$employee = $result->fetch_assoc();
$employee_id = $employee['id'];
$joining_date = $employee['created_at'] ?? date('Y-m-d'); // fallback if null

// ✅ Calculate min month = joining month
$min_month = date('Y-m', strtotime($joining_date));
$max_month = date('Y-m'); // Optional – restrict to current month

// ✅ Handle AJAX Request for Month Filtering
if (isset($_POST['ajax']) && $_POST['ajax'] == '1') {
    $selected_month = $_POST['month'];
    if (!preg_match('/^\d{4}-\d{2}$/', $selected_month)) {
        $selected_month = date('Y-m');
    }

    $month_start = date("Y-m-01", strtotime($selected_month));
    $month_end   = date("Y-m-t", strtotime($selected_month));

    $sql = "SELECT from_date, to_date, leave_reason, status, rejection_reason, decision_date 
            FROM leave_requests 
            WHERE employee_id = ?
              AND from_date BETWEEN ? AND ?
            ORDER BY from_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $employee_id, $month_start, $month_end);
    $stmt->execute();
    $leaves = $stmt->get_result();

    if ($leaves->num_rows > 0) {
        while ($row = $leaves->fetch_assoc()) {
            echo "<tr>
                <td>" . htmlspecialchars($row['from_date']) . "</td>
                <td>" . htmlspecialchars($row['to_date']) . "</td>
                <td>" . htmlspecialchars($row['leave_reason']) . "</td>
                <td>";
            if ($row['status'] === 'Approved') {
                echo "<span class='badge bg-success'>Approved</span>";
            } elseif ($row['status'] === 'Rejected') {
                echo "<span class='badge bg-danger'>Rejected</span>";
            } else {
                echo "<span class='badge bg-warning text-dark'>Pending</span>";
            }
            echo "</td>
                <td>" . ($row['decision_date'] ? htmlspecialchars($row['decision_date']) : '-') . "</td>
                <td>" . ($row['rejection_reason'] ? htmlspecialchars($row['rejection_reason']) : '-') . "</td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='6' class='text-center text-muted py-3'>
                No leave records found for " . date('F Y', strtotime($selected_month)) . ".
              </td></tr>";
    }
    exit;
}

// ✅ Default page load (current month)
$selected_month = date('Y-m');
$month_start = date("Y-m-01");
$month_end   = date("Y-m-t");
$sql = "SELECT from_date, to_date, leave_reason, status, rejection_reason, decision_date 
        FROM leave_requests 
        WHERE employee_id = ?
          AND from_date BETWEEN ? AND ?
        ORDER BY from_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $employee_id, $month_start, $month_end);
$stmt->execute();
$leaves = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Leave History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-plane-departure"></i> Leave History</h4>

            <div class="d-flex align-items-center">
                <label for="month" class="me-2 fw-bold">Select Month:</label>
                <!-- ✅ Added min and max -->
                <input type="month" id="month"
                       value="<?= htmlspecialchars($selected_month) ?>"
                       min="<?= htmlspecialchars($min_month) ?>"
                       max="<?= htmlspecialchars($max_month) ?>"
                       class="form-control me-2"
                       style="width:180px;">
                <button id="filterBtn" class="btn btn-light btn-sm">Filter</button>
                <button id="showAllBtn" class="btn btn-warning btn-sm ms-2">Show All</button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>From</th>
                            <th>To</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Decision Date</th>
                            <th>Rejection Reason</th>
                        </tr>
                    </thead>
                    <tbody id="leaveTableBody">
                        <?php if ($leaves->num_rows > 0): ?>
                            <?php while ($row = $leaves->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['from_date']) ?></td>
                                    <td><?= htmlspecialchars($row['to_date']) ?></td>
                                    <td><?= htmlspecialchars($row['leave_reason']) ?></td>
                                    <td>
                                        <?php if ($row['status'] === 'Approved'): ?>
                                            <span class="badge bg-success">Approved</span>
                                        <?php elseif ($row['status'] === 'Rejected'): ?>
                                            <span class="badge bg-danger">Rejected</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $row['decision_date'] ? htmlspecialchars($row['decision_date']) : '-' ?></td>
                                    <td><?= $row['rejection_reason'] ? htmlspecialchars($row['rejection_reason']) : '-' ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center text-muted py-3">No leave records found for this month.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap & FontAwesome -->
<script src="https://kit.fontawesome.com/a2d04d66d3.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- ✅ AJAX Script for Filter -->
<script>
document.getElementById('filterBtn').addEventListener('click', function () {
    const month = document.getElementById('month').value;
    const tbody = document.getElementById('leaveTableBody');

    if (!month) {
        alert("Please select a valid month.");
        return;
    }

    fetch('leave_history.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'ajax=1&month=' + encodeURIComponent(month)
    })
    .then(res => res.text())
    .then(data => tbody.innerHTML = data);
});

document.getElementById('showAllBtn').addEventListener('click', function () {
    document.getElementById('month').value = '<?= date('Y-m') ?>';
    document.getElementById('filterBtn').click();
});
</script>
</body>
</html>

<?php $conn->close(); ?>
