<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    echo "<div class='alert alert-danger'>❌ Session expired. Please login again.</div>";
    exit();
}

$employee_id = $_SESSION['employee_id'];
$employee_name = "";

// 🗄️ Database connection
$conn = new mysqli('localhost', 'root', '', 'company_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// 🔹 Fetch employee name
$stmt = $conn->prepare("SELECT full_name FROM employees WHERE id=?");
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$stmt->bind_result($employee_name);
$stmt->fetch();
$stmt->close();

// 🔹 Constants
$max_leaves_per_month = 8;

// 🔹 Calculate approved leave days for current month
$stmt = $conn->prepare("
    SELECT SUM(
        DATEDIFF(
            LEAST(to_date, LAST_DAY(NOW())),
            GREATEST(from_date, DATE_FORMAT(NOW(), '%Y-%m-01'))
        ) + 1
    ) AS total_days
    FROM leave_requests
    WHERE employee_id = ?
      AND status = 'Approved'
      AND (
          (from_date <= LAST_DAY(NOW())) 
          AND (to_date >= DATE_FORMAT(NOW(), '%Y-%m-01'))
      )
");
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$stmt->bind_result($used_leaves);
$stmt->fetch();
$stmt->close();

// 🔹 Default values
$used_leaves = $used_leaves ?? 0;
if ($used_leaves < 0) $used_leaves = 0;
$remaining_leaves = max($max_leaves_per_month - $used_leaves, 0);

// 🔹 Handle leave submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_leave'])) {
    // ❌ If employee already used all 8 leaves
    if ($remaining_leaves <= 0) {
        echo "<div class='alert alert-danger'>🚫 You’ve already used your $max_leaves_per_month leaves for this month. You cannot apply for more. Please contact HR.</div>";
        exit();
    }

    $from_date  = $_POST['from_date'] ?? '';
    $to_date    = $_POST['to_date'] ?? '';
    $reason     = $_POST['leave_reason'] ?? '';
    $leave_type = $_POST['leave_type'] ?? 'General';

    $today = date('Y-m-d');
    if ($from_date < $today || $to_date < $from_date) {
        echo "<div class='alert alert-danger'>❌ Invalid dates selected.</div>";
        exit();
    }

    // 🧮 Calculate requested days
    $diff_days = (strtotime($to_date) - strtotime($from_date)) / (60 * 60 * 24) + 1;

    if ($diff_days > $remaining_leaves) {
        echo "<div class='alert alert-warning'>⚠️ You only have $remaining_leaves leave(s) remaining this month. You cannot apply for $diff_days days.</div>";
        exit();
    }

    // 🔍 Check overlapping pending leaves
    $stmt_check = $conn->prepare("
        SELECT COUNT(*) FROM leave_requests 
        WHERE employee_id=? 
          AND status='Pending'
          AND (
              (from_date BETWEEN ? AND ?) 
              OR (to_date BETWEEN ? AND ?)
          )
    ");
    $stmt_check->bind_param("issss", $employee_id, $from_date, $to_date, $from_date, $to_date);
    $stmt_check->execute();
    $stmt_check->bind_result($count);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($count > 0) {
        echo "<div class='alert alert-warning'>❌ You already have a pending leave overlapping these dates.</div>";
        exit();
    }

    // 📝 Insert leave request
    $stmt = $conn->prepare("
        INSERT INTO leave_requests (employee_id, from_date, to_date, leave_reason, leave_type, status)
        VALUES (?, ?, ?, ?, ?, 'Pending')
    ");
    $stmt->bind_param("issss", $employee_id, $from_date, $to_date, $reason, $leave_type);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>✅ Leave ($leave_type) requested from $from_date to $to_date. Request submitted successfully, $employee_name!</div>";
    } else {
        echo "<div class='alert alert-danger'>❌ Failed to apply leave. Try again later.</div>";
    }
    $stmt->close();
    exit();
}
?>

<!-- 🌿 Leave Application UI -->
<div class="card p-3 shadow-lg">
    <h4 class="text-center mb-3">Apply for Leave</h4>

    <div class="row text-center mb-3">
        <div class="col-md-4">
            <div class="card p-2"><b>Total Leaves Per Month:</b> <?= $max_leaves_per_month ?></div>
        </div>
        <div class="col-md-4">
            <div class="card p-2"><b>Used Leaves:</b> <?= $used_leaves ?></div>
        </div>
        <div class="col-md-4">
            <div class="card p-2"><b>Remaining:</b> <?= $remaining_leaves ?></div>
        </div>
    </div>

    <form id="leaveForm" method="post">
        <div class="mb-3">
            <label for="leave_type" class="form-label">Leave Type</label>
            <select id="leave_type" name="leave_type" class="form-select" required>
                <option value="">-- Select Leave Type --</option>
                <option value="Sick Leave">Sick Leave</option>
                <option value="Casual Leave">Casual Leave</option>
                <option value="Paid Leave">Paid Leave</option>
                <option value="Unpaid Leave">Unpaid Leave</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="from_date" class="form-label">From Date</label>
            <input type="date" id="from_date" name="from_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="to_date" class="form-label">To Date</label>
            <input type="date" id="to_date" name="to_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="leave_reason" class="form-label">Reason</label>
            <input type="text" id="leave_reason" name="leave_reason" class="form-control" placeholder="Reason for leave" required>
        </div>

        <button type="submit" name="apply_leave" class="btn btn-success w-100">Apply Leave</button>
    </form>
</div>

<script>
// 🗓️ Date validation
const today = new Date().toISOString().split('T')[0];
document.getElementById('from_date').setAttribute('min', today);

document.getElementById('from_date').addEventListener('change', function() {
    const from = new Date(this.value);
    from.setDate(from.getDate() + 1);
    document.getElementById('to_date').setAttribute('min', from.toISOString().split('T')[0]);
});
</script>
