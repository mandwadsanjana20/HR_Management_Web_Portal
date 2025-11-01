<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'company_db');
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$message = "";

// Update leave status if admin clicks Approve/Reject
if (isset($_GET['id'], $_GET['action'])) {
    $id = intval($_GET['id']);
    $action = ($_GET['action'] === 'approve') ? 'Approved' : 'Rejected';
    $reason = isset($_GET['reason']) ? trim($_GET['reason']) : null;

    if ($action === 'Rejected' && empty($reason)) {
        $message = "❌ Rejection reason is required.";
    } else {
        if ($action === 'Rejected') {
            $sql = "UPDATE leave_requests SET status = ?, rejection_reason = ?, decision_date = NOW() WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $action, $reason, $id);
        } else {
            $sql = "UPDATE leave_requests SET status = ?, decision_date = NOW(), rejection_reason = NULL WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $action, $id);
        }

        if ($stmt->execute()) {
            $message = "✅ Leave ID $id has been $action.";
        } else {
            $message = "❌ Failed to update leave: " . $stmt->error;
        }
    }
}

// Fetch all leave requests with employee names
$sql = "SELECT lr.*, e.full_name AS employee_name 
        FROM leave_requests lr 
        JOIN employees e ON lr.employee_id = e.id
        ORDER BY from_date DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Leave Approvals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script>
        function confirmReject(id) {
            let reason = prompt("Enter the reason for rejection:");
            if (reason !== null && reason.trim() !== "") {
                window.location.href = "?id=" + id + "&action=reject&reason=" + encodeURIComponent(reason);
            }
            return false; // prevent default
        }
    </script>
    <style>
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
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow-lg">
        <div class="card-header bg-dark text-white">
            <h3 class="mb-0">Admin Panel - Leave Requests</h3>
        </div>
        <div class="card-body">

            <?php if ($message): ?>
                <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Employee</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Applied On</th>
                        <th>Decision Date</th>
                        <th>Rejection Reason</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                            switch ($row['status']) {
                                case 'Approved': $badge = 'success'; break;
                                case 'Rejected': $badge = 'danger'; break;
                                default: $badge = 'warning'; break;
                            }
                        ?>
                       <tr>
    <td><?= (int)$row['id'] ?></td>
    <td><?= htmlspecialchars($row['employee_name'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['from_date'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['to_date'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['leave_reason'] ?? '') ?></td>
    <td><span class="badge bg-<?= $badge ?>"><?= htmlspecialchars($row['status'] ?? '') ?></span></td>
    <td><?= htmlspecialchars($row['applied_on'] ?? '') ?></td>
    <td><?= !empty($row['decision_date']) ? htmlspecialchars($row['decision_date']) : '<span class="text-muted">--</span>' ?></td>
    <td><?= !empty($row['rejection_reason']) ? htmlspecialchars($row['rejection_reason']) : '<span class="text-muted">--</span>' ?></td>
                        <td>

                                <?php if ($row['status'] === 'Pending'): ?>
                                    <a href="?id=<?= (int)$row['id'] ?>&action=approve" 
                                       class="btn btn-outline-success btn-sm" title="Approve"
                                       onclick="return confirm('Approve this leave request?');">
                                        <i class="bi bi-check-circle"></i>
                                    </a>
                                    <a href="#" 
                                       class="btn btn-outline-danger btn-sm" title="Reject"
                                       onclick="return confirmReject(<?= (int)$row['id'] ?>);">
                                        <i class="bi bi-x-circle"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">No Action</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="10" class="text-center">No leave requests found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>
</body>
</html>
