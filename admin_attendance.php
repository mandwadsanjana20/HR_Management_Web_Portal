<?php
session_start();

// ✅ Connect DB
$conn = new mysqli('localhost', 'root', '', 'company_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get filter values from GET request or default to current month/year
$filter_year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$filter_month = isset($_GET['month']) ? intval($_GET['month']) : date('m');

// Prepare start and end date for filtering
$start_date = "$filter_year-" . str_pad($filter_month, 2, "0", STR_PAD_LEFT) . "-01";
$end_date = date("Y-m-t", strtotime($start_date)); // last day of month

// Prepare SQL with date filtering
$sql = "SELECT 
            a.*,
            e.full_name AS employee_name,
            e.email AS employee_email
        FROM attendance a
        JOIN employees e ON a.employee_id = e.id
        WHERE a.date BETWEEN ? AND ?
        ORDER BY a.date DESC, a.employee_id ASC" ;

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin - Attendance Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
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
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0">HR - Employee Attendance</h3>
            <form method="get" class="d-flex align-items-center" style="gap:10px;">
                <label for="month" class="mb-0 text-white">Month:</label>
                <select name="month" id="month" class="form-select">
                    <?php
                    for ($m = 1; $m <= 12; $m++) {
                        $selected = ($m == $filter_month) ? 'selected' : '';
                        echo "<option value='$m' $selected>" . date('F', mktime(0, 0, 0, $m, 1)) . "</option>";
                    }
                    ?>
                </select>
                <label for="year" class="mb-0 text-white">Year:</label>
                <select name="year" id="year" class="form-select">
                    <?php
                    $current_year = date('Y');
                    for ($y = $current_year; $y >= $current_year - 5; $y--) {
                        $selected = ($y == $filter_year) ? 'selected' : '';
                        echo "<option value='$y' $selected>$y</option>";
                    }
                    ?>
                </select>
                <button type="submit" class="btn btn-light">Filter</button>
            </form>
        </div>
        <div class="card-body">
            <?php if ($result->num_rows > 0): ?>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Employee Email</th>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Check-In</th>
                            <th>Check-Out</th>
                            <th>Total Worked</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id'] ?? '') ?></td>
                                <td><?= htmlspecialchars($row['employee_email'] ?? '') ?></td>
                                <td><?= htmlspecialchars($row['employee_name'] ?? '') ?></td>
                                <td><?= htmlspecialchars($row['date'] ?? '') ?></td>
                                <td><?= htmlspecialchars($row['check_in_time'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['check_out_time'] ?? '-') ?></td>
                                <td>
                                    <?php
                                        if ($row['check_in_time'] && $row['check_out_time']) {
                                            $in = new DateTime($row['check_in_time']);
                                            $out = new DateTime($row['check_out_time']);
                                            $diff = $in->diff($out);
                                            echo $diff->format('%h hrs %i min');
                                        } else {
                                            echo '-';
                                        }
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-warning">No attendance records found for <?= htmlspecialchars(date('F Y', strtotime($start_date))) ?>.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
