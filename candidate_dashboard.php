<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Only allow candidates here
if (!isset($_SESSION['candidate_email'])) {
    header("Location: login1.php");
    exit();
}

$user_email = $_SESSION['candidate_email'];

// Fetch candidate info securely
$stmt = $conn->prepare("SELECT * FROM candidates WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "User not found.";
    exit();
}

$row = $result->fetch_assoc();

// Fetch notifications & applications for candidate (for notification dot)
$applicationsSql = "
    SELECT ja.id, ja.status, ja.selection_status, ja.interview_date, ja.interview_time, ja.offer_letter, ja.rejection_reason,
           ja.interview_status
    FROM job_applications ja
    WHERE ja.candidate_id = ?
      AND ja.status IN ('Shortlisted', 'Selected', 'Rejected')
    ORDER BY ja.interview_date DESC
";
$appStmt = $conn->prepare($applicationsSql);
$appStmt->bind_param("i", $row['id']);
$appStmt->execute();
$apps = $appStmt->get_result();

$new_notifications = ($apps && $apps->num_rows > 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Candidate Dashboard</title>
    <!-- External CSS & JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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

.social-icons a {
    margin-right: 10px;
    color: #00d1b2;
    font-size: 18px;
}

        #notificationBlock {
            position: absolute;
            top: 60px;
            right: 20px;
            width: 400px; z-index: 1000;
            background: #fff; padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.2);
            display: none;
        }
        .job-cards-container {
            display: flex; flex-wrap: wrap; gap: 20px;
        }
        .job-cards-container .card {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 8px; overflow: hidden;
            width: 18rem;
        }
        .job-cards-container .card img {
            height: 180px; object-fit: cover;
        }
        .btn-outline-primary {
            padding: 10px 20px; font-weight: bold; float:right; bottom:40px;
        }
        .sidebar a i { margin-right: 8px; }
        /* Dropdown inside sidebar */
        .sidebar .dropdown {
            position: relative;
        }
        .sidebar .dropdown-toggle {
            cursor: pointer;
            display: block;
            padding: 10px 0;
            font-weight: bold;
            color: #000;
            text-decoration: none;
        }
        .sidebar .dropdown-menu {
            background: #fff;
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            margin-top: 0;
            position: static;
            box-sizing: border-box;
            padding-left: 10px;
        }
        .sidebar .dropdown-menu a {
            font-weight: normal;
            padding: 8px 0;
            display: block;
            color: #000;
        }
        .sidebar .dropdown-menu a:hover {
            background-color: #e2e3e5;
            color: red;
        }
    </style>
</head>
<body>

<?php include('navbar.php'); ?>

<!-- Notification block -->
<div id="notificationBlock">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-bell"></i> Notifications</h5>
            <button type="button" class="close text-white" onclick="hideNotifications()" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="card-body" id="notificationsContent" style="max-height: 400px; overflow-y: auto;">
            <!-- Notifications will load here dynamically -->
        </div>
    </div>
</div>

<script>
function loadNotifications() {
    fetch('fetch_notifications.php')
    .then(response => response.text())
    .then(data => {
        document.getElementById('notificationsContent').innerHTML = data;
    });
}

// Auto-refresh notifications every 5 seconds
setInterval(loadNotifications, 5000);

// Load notifications on page load
loadNotifications();

function showNotifications() {
    const block = document.getElementById("notificationBlock");
    block.style.display = (block.style.display === "block") ? "none" : "block";
}
function hideNotifications() {
    document.getElementById("notificationBlock").style.display = "none";
}

window.addEventListener('click', function(e) {
    const block = document.getElementById("notificationBlock");
    if (block && block.style.display === "block" && !block.contains(e.target) && !e.target.closest('.sidebar a')) {
        block.style.display = "none";
    }
});
</script>

<div class="main-wrapper">
    <div class="sidebar">
        <a href="candidate_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="edit_profile.php"><i class="fas fa-user-edit"></i> Edit Profile</a>
        <a href="#" onclick="showNotifications(); return false;">
            <i class="fas fa-bell"></i> Notifications <?php if ($new_notifications): ?><span class="notify-dot"></span><?php endif; ?>
        </a>
        <a href="?view=applied_jobs"><i class="fas fa-briefcase"></i> Applied Jobs</a>

        <div class="dropdown">
            <a class="dropdown-toggle" id="documentsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#">
                <i class="fas fa-file-alt"></i> Documents
            </a>
            <div class="dropdown-menu" aria-labelledby="documentsDropdown">
                <a class="dropdown-item" href="?view=view_interview_letters">View Interview Letter</a>
                <a class="dropdown-item" href="?view=view_offer_letters">View Offer Letter</a>
            </div>
        </div>
        <a href="change_password.php"><i class="fas fa-key"></i> Change Password</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="profile-section">
         <?php
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($_SESSION['message']);
        echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
        echo '<span aria-hidden="true">&times;</span>';
        echo '</button></div>';
        unset($_SESSION['message']);
    }
    ?>
    <?php if (isset($_GET['view'])): ?>
        <?php
        $candidate_id = $row['id'];

        if ($_GET['view'] === 'applied_jobs'):
            $appliedJobsSql = "
                SELECT ja.id, ja.status, ja.selection_status, ja.interview_date, ja.interview_time, ja.offer_letter, ja.rejection_reason, ja.interview_status,
                       j.title, j.description, j.posted_date, j.image
                FROM job_applications ja
                JOIN jobs j ON ja.job_id = j.job_id
                WHERE ja.candidate_id = ?
                ORDER BY ja.interview_date DESC
            ";
            $appliedJobsStmt = $conn->prepare($appliedJobsSql);
            $appliedJobsStmt->bind_param("i", $candidate_id);
            $appliedJobsStmt->execute();
            $appliedJobs = $appliedJobsStmt->get_result();

            if ($appliedJobs && $appliedJobs->num_rows > 0) {
                echo '<div class="job-cards-container">';
                while ($job = $appliedJobs->fetch_assoc()) {
                    $shortDesc = strlen($job['description']) > 100 ? substr($job['description'], 0, 100) . '...' : $job['description'];
                    $image = !empty($job['image']) ? htmlspecialchars($job['image']) : 'default-job.png';

                    $selectionStatus = $job['selection_status'];
                    $applicationStatus = $job['status'];
                    $interviewStatus = $job['interview_status'] ?? '';

                    echo '<div class="card">';
                    echo '<img src="uploads/' . $image . '" class="card-img-top" alt="Job Image">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($job['title']) . '</h5>';
                    echo '<p class="card-text">' . htmlspecialchars($shortDesc) . '</p>';

                    // Status & Buttons logic
if ($selectionStatus === 'Selected') {
    echo '<span class="badge bg-success">Selected</span><br>';
    echo '<a href="offer_letter_view.php?id=' . $job['id'] . '" target="_blank" class="btn btn-sm btn-success mt-2">View Offer Letter</a>';
} elseif ($selectionStatus === 'Rejected' || $interviewStatus === 'Rejected') {
    echo '<span class="badge bg-danger">You are Rejected as employee</span>';
    if (!empty($job['rejection_reason'])) {
        echo '<div class="text-danger small mt-1">Interview Rejection Reason: <em>' . htmlspecialchars($job['rejection_reason']) . '</em></div>';
    }
} elseif ($applicationStatus === 'Rejected') {
    echo '<span class="badge bg-danger">Application Rejected</span>';
    if (!empty($job['reason'])) {
        echo '<div class="text-danger small mt-1">Application Rejection Reason: <em>' . htmlspecialchars($job['reason']) . '</em></div>';
    }
} elseif ($interviewStatus === 'Attended' && $selectionStatus === 'Pending') {
    echo '<span class="badge bg-warning text-dark">In Progress</span>';
} elseif ($applicationStatus === 'Shortlisted') {
    echo '<span class="badge bg-info">Shortlisted</span><br>';
    if (!empty($job['interview_date'])) {
        echo '<a href="interview_letter.php?id=' . $job['id'] . '" target="_blank" class="btn btn-sm btn-primary mt-2">View Interview Letter</a>';
    }
} else {
    echo '<span class="badge bg-secondary">In Progress</span>';
}


                    // Show date/time only if not rejected and not attended+pending
                    $showDateTime = true;
                    if (
                        ($interviewStatus === 'Attended' && $selectionStatus === 'Pending') ||
                        $selectionStatus === 'Rejected'
                    ) {
                        $showDateTime = false;
                    }

                    if (!empty($job['interview_date']) && $showDateTime) {
                        echo '<div class="mt-2">';
                        echo '🗓 <strong>' . date("F j, Y", strtotime($job['interview_date'])) . '</strong><br>';
                        echo '⏰ <strong>' . ($job['interview_time'] ? date("h:i A", strtotime($job['interview_time'])) : 'TBA') . '</strong>';
                        echo '</div>';
                    }

                    echo '</div></div>';
                }
                echo '</div>';
            } else {
                echo "<p>You have not applied for any jobs yet.</p>";
            }

        elseif ($_GET['view'] === 'view_interview_letters'):
            $lettersSql = "
                SELECT ja.id, j.title
                FROM job_applications ja
                JOIN jobs j ON ja.job_id = j.job_id
                WHERE ja.candidate_id = ? AND ja.status = 'Shortlisted'
                ORDER BY ja.interview_date DESC
            ";
            $lettersStmt = $conn->prepare($lettersSql);
            $lettersStmt->bind_param("i", $candidate_id);
            $lettersStmt->execute();
            $letters = $lettersStmt->get_result();

            if ($letters && $letters->num_rows > 0) {
                echo '<ul class="list-group">';
                while ($letter = $letters->fetch_assoc()) {
                    $title = htmlspecialchars($letter['title']);
                    echo '<li class="list-group-item">';
                    echo "<strong>$title</strong> - <a href=\"interview_letter.php?id={$letter['id']}\" target=\"_blank\">View Interview Letter</a>";
                    echo '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p>No interview letters available.</p>';
            }

     elseif ($_GET['view'] === 'view_offer_letters'):
    $offerSql = "
        SELECT ja.id, j.title, ja.selection_status
        FROM job_applications ja
        JOIN jobs j ON ja.job_id = j.job_id
        WHERE ja.candidate_id = ? AND ja.selection_status = 'Selected'
        ORDER BY ja.interview_date DESC
    ";
    $offerStmt = $conn->prepare($offerSql);
    $offerStmt->bind_param("i", $candidate_id);
    $offerStmt->execute();
    $offerLetters = $offerStmt->get_result();

    echo '<h4 class="mb-4">My Offer Letters</h4>';

    if ($offerLetters && $offerLetters->num_rows > 0) {
        echo '<ul class="list-group">';
        while ($letter = $offerLetters->fetch_assoc()) {
            $title = htmlspecialchars($letter['title']);
            $id = $letter['id'];
            echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
            echo "<div><strong>$title</strong></div>";
            echo "<a href='offer_letter_view.php?id=$id' target='_blank' class='btn btn-success btn-sm'>View Offer Letter</a>";
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No offer letters available yet.</p>';
    }
endif;


        ?>
    <?php else: ?>
        <!-- Default candidate profile view -->
        <div class="profile-container card shadow-sm rounded">
            <div class="row g-0">
                <div class="profile-left col-md-4 bg-dark text-white d-flex flex-column align-items-center justify-content-center p-4 rounded-start">
                    <img src="<?= htmlspecialchars($row['photo']) ?>" alt="Profile" class="rounded-circle mb-3" style="width:120px; height:120px; object-fit:cover; border:3px solid white;">
                    <h3><?= htmlspecialchars($row['full_name']) ?></h3>
                    <p>Candidate</p>
                    <a href="edit_profile.php" class="btn btn-danger rounded-pill px-4 mt-3">Edit Profile</a>
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
                    <div>
                        <a href="#" class="me-3 text-secondary"><i class="fab fa-facebook-f fs-4"></i></a>
                        <a href="#" class="me-3 text-secondary"><i class="fab fa-twitter fs-4"></i></a>
                        <a href="#" class="text-secondary"><i class="fab fa-instagram fs-4"></i></a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    </div>
</div>

<?php
$stmt->close();
$appStmt->close();
$conn->close();
?>

</body>
</html>
