<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'company_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Default Filters ---
$selected_employee = isset($_GET['employee_id']) ? intval($_GET['employee_id']) : 0;
$selected_month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
if (!preg_match('/^\d{4}-\d{2}$/', $selected_month)) {
    $selected_month = date('Y-m');
}

$month_start = date("Y-m-01", strtotime($selected_month));
$month_end   = date("Y-m-t", strtotime($selected_month));

$leave_limit = 8; // monthly limit

// --- Fetch all employees for dropdown ---
$emp_result = $conn->query("SELECT id, full_name FROM employees ORDER BY full_name");

// --- Main Summary Query (INNER JOIN for only applied employees) ---
$sql = "
    SELECT 
        e.id AS employee_id,
        e.full_name AS employee_name,
        SUM(
            CASE 
                WHEN lr.status = 'Approved' THEN
                    DATEDIFF(
                        LEAST(lr.to_date, ?),
                        GREATEST(lr.from_date, ?)
                    ) + 1
                ELSE 0
            END
        ) AS total_used,
        COUNT(lr.id) AS total_applications
    FROM employees e
    INNER JOIN leave_requests lr 
        ON e.id = lr.employee_id
        AND lr.to_date >= ? 
        AND lr.from_date <= ?
    WHERE 1=1
";

$params = [$month_end, $month_start, $month_start, $month_end];
$types = "ssss";

if ($selected_employee > 0) {
    $sql .= " AND e.id = ?";
    $params[] = $selected_employee;
    $types .= "i";
}

$sql .= " GROUP BY e.id, e.full_name HAVING total_applications > 0 ORDER BY e.full_name";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin - Leave Summary</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { background-color: #f8f9fa; }
.card-header { display: flex; justify-content: space-between; align-items: center; }
.table thead th { position: sticky; top: 0; background: #212529; color: #fff; }
</style>
<script>
function applyFilter() {
    const emp = document.getElementById('employee_id').value;
    const month = document.getElementById('month').value;
    window.location.href = "?employee_id=" + encodeURIComponent(emp) + "&month=" + encodeURIComponent(month);
}
</script>
</head>
<body>
<div class="container py-5">
    <div class="card shadow-lg">
        <div class="card-header bg-dark text-white">
            <h3 class="mb-0">Admin Panel - Leave Summary</h3>
            <div class="d-flex align-items-center">
                <select id="employee_id" class="form-select form-select-sm me-2" style="width:200px;">
                    <option value="0">All Employees</option>
                    <?php
                    $emp_result->data_seek(0);
                    while ($emp = $emp_result->fetch_assoc()): ?>
                        <option value="<?= $emp['id'] ?>" <?= ($emp['id'] == $selected_employee) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($emp['full_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <input type="month" id="month" class="form-control form-control-sm me-2" style="width:150px;" value="<?= htmlspecialchars($selected_month) ?>">
                <button class="btn btn-warning btn-sm" onclick="applyFilter()"><i class="bi bi-funnel"></i> Filter</button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Employee Name</th>
                            <th>Used Leaves</th>
                            <th>Remaining Leaves</th>
                            <th>Total Applications</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): 
                            $used = (int)$row['total_used'];
                            $used_display = $used > $leave_limit ? "{$leave_limit} (Limit Exceeded)" : $used;
                            $remaining_display = max(0, $leave_limit - $used);
                        ?>
                        <tr>
                            <td><?= $row['employee_id'] ?></td>
                            <td><?= htmlspecialchars($row['employee_name']) ?></td>
                            <td><?= $used_display ?></td>
                            <td><?= $remaining_display ?></td>
                            <td><?= $row['total_applications'] ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center text-muted py-3">No leave applications found for <?= date('F Y', strtotime($selected_month)) ?>.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
