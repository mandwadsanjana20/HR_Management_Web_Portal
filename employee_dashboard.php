<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Only allow employees here
if (!isset($_SESSION['employee_email'])) {
    header("Location: login1.php");
    exit();
}

$user_email = $conn->real_escape_string($_SESSION['employee_email']);

// Get employee data
$result = $conn->query("SELECT * FROM employees WHERE email = '$user_email'");
if ($result->num_rows === 0) {
    echo "User not found.";
    exit();
}
$row = $result->fetch_assoc();
$employee_id = $row['id'];

$leave_sql = "SELECT id, from_date, to_date, status, rejection_reason 
              FROM leave_requests 
              WHERE employee_id=? AND seen=0
              ORDER BY decision_date DESC LIMIT 1";
$stmt = $conn->prepare($leave_sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$leave_result = $stmt->get_result();
$leave = $leave_result->fetch_assoc();
$stmt->close();

// After showing the alert
if ($leave) {
    $update_seen = $conn->prepare("UPDATE leave_requests SET seen=1 WHERE id=?");
    $update_seen->bind_param("i", $leave['id']);
    $update_seen->execute();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
       body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background: #ffffff;
            color: #f1f1f1;
        }

        .main-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #ffffff;
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.6);
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .sidebar a {
            display: block;
            padding: 10px 0;
            font-weight: bold;
            text-decoration: none;
            color: #000;
            position: relative;
            transition: all 0.2s ease-in-out;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: red;
            padding-left: 8px;
        }

        .notify-dot {
            height: 10px;
            width: 10px;
            background: #ff3860;
            border-radius: 50%;
            position: absolute;
            top: 10px;
            right: 5px;
        }

        .profile-section {
            flex: 1;
            padding: 40px 20px;
        }

        .profile-container {
            display: flex;
            max-width: 800px;
            background: #242424;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.5);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .profile-left {
            width: 35%;
            background: linear-gradient(to right, #af5fb5ff, #000000ff);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .profile-left img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid white;
            object-fit: cover;
            margin-bottom: 15px;
        }

        .bt1 {
            background: #8a021dff;
            color: white;
            border: none;
            border-radius: 20px;
            padding: 6px 10px;
            cursor: pointer;
        }

        .profile-right {
            width: 65%;
            padding: 25px 30px;
            background: #1e1e1e;
        }

        .profile-right h4 {
            font-size: 16px;
            margin-bottom: 15px;
            color: #000;
            border-bottom: 1px solid #333;
            padding-bottom: 5px;
        }

        .profile-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .profile-info div {
            width: 45%;
            font-size: 14px;
            color: #ccc;
        }

        .profile-info span {
            font-weight: bold;
            color: #fff;
        }

        /* Dynamic content area */
        #main-content {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<?php include('navbar.php'); ?>

<div class="main-wrapper">
    <div class="sidebar">
    <a href="employee_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="employee_update.php"><i class="fas fa-user-edit"></i> Edit Profile</a>
    <a href="#" id="openNotificationsModal"><i class="fas fa-bell"></i> Notifications <span class="notify-dot"></span></a>

    <!-- Attendance Dropdown -->
    <a class="d-flex justify-content-between align-items-center" 
       data-bs-toggle="collapse" href="#attendanceMenu" role="button" 
       aria-expanded="false" aria-controls="attendanceMenu">
        <span><i class="fas fa-calendar-check"></i> Attendance</span>
        <i class="fas fa-chevron-down small"></i>
    </a>
    <div class="collapse ps-3" id="attendanceMenu">
        <a href="#" id="openAttendanceModal" class="d-block py-1"><i class="fas fa-clock"></i> Mark Attendance</a>
        <a href="atten_history.php" class="d-block py-1 load-page"><i class="fas fa-history"></i> Attendance History</a>
    </div>

    <!-- Leave Dropdown -->
    <a class="d-flex justify-content-between align-items-center" 
       data-bs-toggle="collapse" href="#leaveMenu" role="button" 
       aria-expanded="false" aria-controls="leaveMenu">
        <span><i class="fas fa-plane-departure"></i> Leave</span>
        <i class="fas fa-chevron-down small"></i>
    </a>
    <div class="collapse ps-3" id="leaveMenu">
        <a href="#" id="openLeaveModal" class="d-block py-1"><i class="fas fa-plus-circle"></i> Apply Leave</a>
        <a href="leave_history.php" class="d-block py-1 load-page"><i class="fas fa-history"></i> Leave History</a>
    </div>
    <!--<a href="new.php" class="load-page">
   <i class="fas fa-chart-bar"></i> Performance Report
</a>-->

     <a href="emp_payroll.php" class="load-page"><i class="fas fa-file-invoice-dollar"></i> Payslips</a>
    <a href="emp_reports.php" class="load-page"><i class="fas fa-chart-line"></i> Reports</a>
     <a href="emp_projects.php" class="load-page"><i class="fas fa-tasks"></i> Projects</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

    <div class="profile-section">
        <?php

// Assume you already have these variables set earlier in your code:
$join_date = new DateTime($row['created_at']);
$today = new DateTime();
$days_diff = $today->diff($join_date)->days;

// Fetch bank details
$bank_sql = "SELECT account_number, ifsc_code FROM employee_bank_details WHERE employee_id = '{$row['id']}'";
$bank_res = $conn->query($bank_sql);
$bank = $bank_res->fetch_assoc();

// ✅ Final alert logic
if ($days_diff <= 7 && (empty($bank['account_number']) || empty($bank['ifsc_code']))) {
    // Joined within a week AND bank details missing
    $days_left = 7 - $days_diff;
    echo "
    <div class='alert alert-warning alert-dismissible fade show' role='alert'>
        <i class='fas fa-exclamation-circle'></i> 
        ⚠️ Please update your <strong>Bank Details</strong> within <strong>$days_left day(s)</strong> to receive your payslip.
        <a href='bank_details.php' class='btn btn-sm btn-primary ms-2'>Update Bank Details</a>
        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
    </div>";
} elseif (!empty($bank['account_number']) && !empty($bank['ifsc_code'])) {
    // Bank details are filled (no warning)
    echo "
    <div class='alert alert-success alert-dismissible fade show' role='alert'>
        <i class='fas fa-check-circle'></i> ✅ Your bank details are verified. You can receive payslips.
        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
    </div>";
} elseif ($days_diff > 7 && (empty($bank['account_number']) || empty($bank['ifsc_code']))) {
    // Joined more than a week ago and still no details — show final warning
    echo "
    <div class='alert alert-danger alert-dismissible fade show' role='alert'>
        ❌ You did not update your bank details within a week. Please contact HR to activate your payslip access.
        <a href='bank_details.php' class='btn btn-sm btn-danger ms-2'>Update Now</a>
        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
    </div>";
}
?>

        <!-- Dynamic content will be loaded here -->
        <div id="main-content">

        <!-- Leave Notification -->
        <?php if ($leave): ?>
            <?php if ($leave['status'] === 'Approved'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i>
                    ✅ Your leave from <strong><?= htmlspecialchars($leave['from_date']) ?></strong> 
                    to <strong><?= htmlspecialchars($leave['to_date']) ?></strong> has been 
                    <strong>Approved</strong>.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($leave['status'] === 'Rejected'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-times-circle"></i>
                    ❌ Your leave from <strong><?= htmlspecialchars($leave['from_date']) ?></strong> 
                    to <strong><?= htmlspecialchars($leave['to_date']) ?></strong> has been 
                    <strong>Rejected</strong>.<br>
                    <em>Reason:</em> <?= !empty($leave['rejection_reason']) 
                        ? nl2br(htmlspecialchars($leave['rejection_reason'])) 
                        : '<span class="text-muted">No reason provided.</span>' ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>
           <!-- Payslip Notification -->
<?php
$employee_id = $_SESSION['employee_id']; // ensure this session variable exists

// ✅ Step 1: Check if employee has valid bank details
$bank_check = $conn->prepare("
    SELECT account_number, ifsc_code 
    FROM employee_bank_details 
    WHERE employee_id = ?
");
$bank_check->bind_param("i", $employee_id);
$bank_check->execute();
$bank_data = $bank_check->get_result()->fetch_assoc();
$bank_check->close();

if (!empty($bank_data['account_number']) && !empty($bank_data['ifsc_code'])) {
    // ✅ Step 2: Check if HR actually generated a payslip
    $sql = "SELECT id, total, generated_at, message 
            FROM payroll 
            WHERE employee_id = ? 
              AND total IS NOT NULL 
              AND total != 0
              AND generated_at IS NOT NULL
            ORDER BY generated_at DESC 
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $payslip = $result->fetch_assoc();
    $stmt->close();

    // ✅ Step 3: Show alert only if payslip exists
    if (!empty($payslip)) {
        ?>
        <div class="alert alert-info alert-dismissible fade show mt-3 shadow-sm border border-primary" role="alert">
            <i class="fas fa-file-invoice-dollar"></i>
            <strong>Payslip Generated!</strong><br>
            <?= htmlspecialchars($payslip['message'] ?? 'Your salary has been processed.') ?><br>
            <b>Total Salary:</b> ₹<?= htmlspecialchars(number_format($payslip['total'], 2)) ?><br>
            <b>Date:</b> <?= date("d M Y, h:i A", strtotime($payslip['generated_at'])) ?><br>

            <a href="view_payslip.php?id=<?= htmlspecialchars($payslip['id']) ?>" 
               class="btn btn-success btn-sm mt-2">
               <i class="fas fa-eye"></i> View Payslip
            </a>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php
    }
}
?>



        <!-- Employee Profile -->
        <div class="profile-container card shadow-sm rounded">
            <div class="row g-0">
                <div class="profile-left col-md-4">
                    <img src="<?= htmlspecialchars($row['photo']) ?>" alt="Profile">
                    <h3><?= htmlspecialchars($row['full_name']) ?></h3>
                    <p>Employee</p>
                    <a href="employee_update.php" class="btn btn-danger rounded-pill px-4 mt-3">Edit Profile</a>
                </div>
                <div class="profile-right col-md-8 bg-light p-4 rounded-end">
                    <h4 class="mb-4 border-bottom pb-2">Information</h4>
                    <div class="row mb-3">
                        <div class="col-sm-6"><strong>Email:</strong><br><?= htmlspecialchars($row['email']) ?></div>
                        <div class="col-sm-6"><strong>Phone:</strong><br><?= htmlspecialchars($row['contact']) ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-6"><strong>Gender:</strong><br><?= htmlspecialchars($row['gender']) ?></div>
                        <div class="col-sm-6"><strong>Qualification:</strong><br><?= htmlspecialchars($row['qualification']) ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-6"><strong>DOB:</strong><br><?= htmlspecialchars($row['dob']) ?></div>
                    </div>
                </div>
            </div>
        </div>

        </div>
    </div>
</div>

<!-- Attendance Modal --> 
 <div class="modal fade" id="attendanceModal" tabindex="-1"> 
  <div class="modal-dialog modal-lg modal-dialog-scrollable"> 
    <div class="modal-content"> 
      <div class="modal-header">
        <h5 class="modal-title">Employee Attendance</h5></div>
         <div class="modal-body" id="attendanceModalBody"></div> 
        </div>
       </div> 
      </div>

 <!-- Leave Modal --> 
  <div class="modal fade" id="leaveModal" tabindex="-1"> 
    <div class="modal-dialog"> 
      <div class="modal-content"> 
        <div class="modal-header"> 
          <h5 class="modal-title">Apply Leave</h5> 
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
         </div> <div class="modal-body" id="leaveModalBody"> 
          <div class="text-center py-4"> <div class="spinner-border"></div> 
        </div> </div> </div> </div> </div>

  <!-- Scripts --> 
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

   <script> 
   $(document).ready(function() {
  $('#openNotificationsModal').click(function(e) {
    e.preventDefault();
    var modal = new bootstrap.Modal(document.getElementById('notificationsModal'));
    modal.show();
    $('#notificationsModalBody').load('notification.php'); // load content dynamically
  });

  // Dynamic content loading for sidebar links
  $("a.load-page").click(function(e){
      e.preventDefault();
      let page = $(this).attr("href");
      $("#main-content").html('<div class="text-center py-4"><div class="spinner-border text-primary"></div><p class="mt-2">Loading...</p></div>');
      $("#main-content").load(page);
  });
});
   </script>

   <script> $(document).ready(function() { // Open leave modal and load form 
   $('#openLeaveModal').click(function(e) { e.preventDefault(); 
    const modalEl = document.getElementById('leaveModal');
     const leaveModal = new bootstrap.Modal(modalEl); 
     leaveModal.show(); $('#leaveModalBody').load('leaves.php'); 
    });

    // AJAX submit for dynamically loaded form
    $(document).on('submit', '#leaveForm', function(e) { e.preventDefault(); 
    const formData = $(this).serialize() + '&apply_leave=1'; $.post('leaves.php', formData,
     function(response) { $('#leaveModalBody').html(response); 

     }); 
     }); 
     }); 
  </script>

  <script>
function showAttendanceAlert(message, type = "success") {
  const alertBox = document.createElement("div");
  alertBox.innerHTML = `
    <div class="alert alert-${type} alert-dismissible fade show mt-2" role="alert">
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
  document.getElementById("attendanceModalBody").prepend(alertBox);
}

function bindAttendanceButtons() {
  const container = document.getElementById("attendanceModalBody");
  if (!container) return;

  const checkinBtn = container.querySelector("#checkInBtn");
  if (checkinBtn) {
    checkinBtn.onclick = () => {
      fetch("attendance.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "action=checkin"
      })
      .then(res => res.json())
      .then(data => {
        showAttendanceAlert(data.message, data.status === "success" ? "success" : "danger");
        if (data.status === "success") refreshAttendance();
      })
      .catch(() => showAttendanceAlert("❌ Error during check-in.", "danger"));
    };
  }

  const checkoutBtn = container.querySelector("#checkOutBtn");
  if (checkoutBtn) {
    checkoutBtn.onclick = () => {
      fetch("attendance.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "action=checkout"
      })
      .then(res => res.json())
      .then(data => {
        showAttendanceAlert(data.message, data.status === "success" ? "success" : "danger");
        if (data.status === "success") refreshAttendance();
      })
      .catch(() => showAttendanceAlert("❌ Error during check-out.", "danger"));
    };
  }
}

function refreshAttendance() {
  $("#attendanceModalBody").load("attendance.php", bindAttendanceButtons);
}

$("#openAttendanceModal").on("click", function (e) {
  e.preventDefault();
  const attendanceModal = new bootstrap.Modal(document.getElementById("attendanceModal"));
  attendanceModal.show();
  refreshAttendance();
});
</script>


<!-- Notifications Modal -->
<div class="modal fade" id="notificationsModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fas fa-bell"></i> Notifications</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="notificationsModalBody">
        <div class="text-center py-4">
          <div class="spinner-border text-primary"></div>
          <p class="mt-2">Loading notifications...</p>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
<?php $conn->close(); ?>
