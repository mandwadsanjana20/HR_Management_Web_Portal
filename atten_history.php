<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['employee_email'])) {
    header("Location: login1.php");
    exit();
}

$user_email = $conn->real_escape_string($_SESSION['employee_email']);

// Get employee details
$stmt = $conn->prepare("SELECT id, full_name, job_id FROM employees WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "Employee not found.";
    exit();
}
$employee = $result->fetch_assoc();
$employee_id = $employee['id'];

// Get job title
$jobTitle = "";
$jobRes = $conn->query("SELECT title FROM jobs WHERE job_id = ".$employee['job_id']);
if ($jobRes && $jobRes->num_rows > 0) {
    $jobTitle = $jobRes->fetch_assoc()['title'];
}

// --- Week Filter ---
$startOfWeek = isset($_GET['week']) ? $_GET['week'] : date('Y-m-d', strtotime('monday this week'));
$endOfWeek   = date('Y-m-d', strtotime($startOfWeek . ' +6 days'));

// Fetch attendance for this week
$sql = "SELECT date, check_in_time, check_out_time 
        FROM attendance 
        WHERE employee_id = ? 
        AND date BETWEEN ? AND ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $employee_id, $startOfWeek, $endOfWeek);
$stmt->execute();
$attendanceResult = $stmt->get_result();

$attendanceData = [];
while ($row = $attendanceResult->fetch_assoc()) {
    $attendanceData[$row['date']] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Weekly Attendance - <?= htmlspecialchars($employee['full_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .calendar-table th, .calendar-table td {
            text-align: center;
            vertical-align: middle;
            min-width: 110px;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        .status-present { background: #d1fae5; color: #065f46; }
        .status-absent { background: #fee2e2; color: #991b1b; }
        .status-leave { background: #e9f9d1ff; color: #2859cbff; }
        .status-active { background: #dbeafe; color: #1e3a8a; }
        .emp-info { display: flex; align-items: center; gap: 10px; }
        .emp-avatar {
            width: 40px; height: 40px; border-radius: 50%; background: #ccc;
        }
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
<div class="container my-5">
    <h3 class="mb-4">Attendance (<?= date("d M", strtotime($startOfWeek)) ?> - <?= date("d M", strtotime($endOfWeek)) ?>)</h3>

    <!-- Week Filter -->
    <form method="GET" class="row g-3 mb-4">
        <div class="col-auto">
            <input type="date" name="week" class="form-control" value="<?= htmlspecialchars($startOfWeek) ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Show Week</button>
        </div>
    </form>

        <table class="table table-bordered calendar-table">
            <thead class="table-dark">
                <tr>
                    <th>Employee</th>
                    <?php
                    for ($i = 0; $i < 7; $i++) {
                        $day = date('Y-m-d', strtotime("$startOfWeek +$i days"));
                        echo "<th>".date("D<br>d M", strtotime($day))."</th>";
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="emp-info">
                        <div class="emp-avatar"></div>
                        <div>
                            <strong><?= htmlspecialchars($employee['full_name']) ?></strong><br>
                            <small><?= htmlspecialchars($jobTitle) ?></small>
                        </div>
                    </td>
                    <?php
                    for ($i = 0; $i < 7; $i++) {
                        $day = date('Y-m-d', strtotime("$startOfWeek +$i days"));
                        $statusHtml = "<span class='status-badge status-absent'>Absent</span>";

                        // If attendance exists
                        if (isset($attendanceData[$day])) {
                            $row = $attendanceData[$day];
                            if (!empty($row['check_in_time'])) {
                                $workHours = "-";
                                if (!empty($row['check_out_time'])) {
                                    $in = strtotime($row['check_in_time']);
                                    $out = strtotime($row['check_out_time']);
                                    $diff = round(($out - $in) / 3600, 2);
                                    $workHours = $diff."h";
                                }
                                $statusHtml = "<span class='status-badge status-present'>✔ $workHours</span>";
                            }
                        }

                        // Check leave from leave_from to leave_to
                        $leave_sql = "SELECT * FROM leave_requests 
                                      WHERE employee_id = ? 
                                      AND status='approved' 
                                      AND ? BETWEEN from_date AND to_date";
                        $stmt2 = $conn->prepare($leave_sql);
                        $stmt2->bind_param("is", $employee_id, $day);
                        $stmt2->execute();
                        $leave_result = $stmt2->get_result();

                        if ($leave_result->num_rows > 0) {
                            $statusHtml = "<span class='status-badge status-leave'>🌴 Leave</span>";
                        }

                        // Highlight today if checked in but not yet checked out → Active
                        if ($day == date('Y-m-d') && isset($attendanceData[$day])) {
                            $row = $attendanceData[$day];
                            if (!empty($row['check_in_time']) && empty($row['check_out_time'])) {
                                $statusHtml = "<span class='status-badge status-active'>Active</span>";
                            }
                        }

                        echo "<td>$statusHtml</td>";
                    }
                    ?>
                </tr>
            </tbody>
        </table>
</div>
</body>
</html>
<?php $conn->close(); ?>
