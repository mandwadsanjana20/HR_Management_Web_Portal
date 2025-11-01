<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) {
    die("DB Connection Failed: " . $conn->connect_error);
}

if (!isset($_SESSION['employee_id'])) {
    echo "<div class='alert alert-danger'>Not logged in</div>";
    exit();
}

$employee_id = $_SESSION['employee_id'];
$date = date("Y-m-d");

// Check if employee is on leave today
$leaveStmt = $conn->prepare("SELECT * FROM leave_requests WHERE employee_id=? AND ? BETWEEN from_date AND to_date AND status='approved'");
$leaveStmt->bind_param("is", $employee_id, $date);
$leaveStmt->execute();
$leaveResult = $leaveStmt->get_result();
$onLeave = $leaveResult->num_rows > 0;

// Fetch today's attendance
$stmt = $conn->prepare("SELECT * FROM attendance WHERE employee_id=? AND date=?");
$stmt->bind_param("is", $employee_id, $date);
$stmt->execute();
$result = $stmt->get_result();
$attendance = $result->fetch_assoc();
?>

<div class="p-3">
    <div id="attendanceAlert"></div>
    <h5>Attendance - <?php echo date("d M Y"); ?></h5>

    <?php if ($onLeave): ?>
        <div class="alert alert-warning">
            ⚠️ You are on leave today. Attendance is disabled.
        </div>
    <?php else: ?>
        <?php if (!$attendance): ?>
            <button class="btn btn-success" id="checkInBtn">Check In</button>
        <?php else: ?>
            <p><strong>Check In:</strong> <?php echo $attendance['check_in_time']; ?></p>

            <?php
            $checkoutDisabled = false;
            $hoursPassed = 0;
            if (!$attendance['check_out_time']) {
                $checkInTimestamp = strtotime($attendance['check_in_time']);
                $nowTimestamp = time();
                $hoursPassed = ($nowTimestamp - $checkInTimestamp) / 3600;
                if ($hoursPassed < 5) {
                    $checkoutDisabled = true;
                }
            }
            ?>

            <?php if ($attendance['check_out_time']): ?>
                <p><strong>Check Out:</strong> <?php echo $attendance['check_out_time']; ?></p>
                <div class="alert alert-info">✅ You have completed today’s attendance.</div>
            <?php else: ?>
                <button class="btn btn-danger" id="checkOutBtn" <?php echo $checkoutDisabled ? "disabled" : ""; ?>>
                    Check Out
                </button>
                <?php if ($checkoutDisabled): ?>
                    <small class="text-muted d-block mt-2">
                        ⚠️ Checkout available after 5 hours. Time passed: <?= round($hoursPassed, 2) ?> hrs
                    </small>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
function showAttendanceAlert(message, type = "success") {
    const alertBox = document.getElementById("attendanceAlert");
    alertBox.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show mt-2" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
}

function bindAttendanceButtons() {
    const checkInBtn = document.getElementById("checkInBtn");
    const checkOutBtn = document.getElementById("checkOutBtn");

    if (checkInBtn) {
        checkInBtn.addEventListener("click", () => {
            fetch("atten_content.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "action=checkin"
            })
            .then(res => res.json())
            .then(data => {
                showAttendanceAlert(data.message, data.status === "success" ? "success" : "danger");
                if (data.status === "success") refreshAttendance();
            });
        });
    }

    if (checkOutBtn) {
        checkOutBtn.addEventListener("click", () => {
            fetch("atten_content.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "action=checkout"
            })
            .then(res => res.json())
            .then(data => {
                showAttendanceAlert(data.message, data.status === "success" ? "success" : "danger");
                if (data.status === "success") refreshAttendance();
            });
        });
    }
}

function refreshAttendance() {
    $("#attendanceModalBody").load("attendance.php", bindAttendanceButtons);
}

bindAttendanceButtons();
</script>
