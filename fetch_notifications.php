<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check candidate session
if (!isset($_SESSION['candidate_email'])) {
    exit("Unauthorized");
}

$user_email = $_SESSION['candidate_email'];

// Get candidate ID
$stmt = $conn->prepare("SELECT id FROM candidates WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    exit("Candidate not found");
}

$candidate = $result->fetch_assoc();
$candidate_id = $candidate['id'];

// Fetch all applications for this candidate
$sql = "
    SELECT ja.id, ja.status, ja.selection_status, ja.interview_date, ja.interview_time,
           ja.offer_letter, ja.reason, ja.rejection_reason, ja.interview_status,
           j.title
    FROM job_applications ja
    JOIN jobs j ON ja.job_id = j.job_id
    WHERE ja.candidate_id = ?
    ORDER BY ja.applied_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $candidate_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($app = $result->fetch_assoc()) {
        $jobTitle = htmlspecialchars($app['title']);
        $status = $app['status'] ?? '';
        $selectionStatus = $app['selection_status'] ?? '';
        $interviewStatus = $app['interview_status'] ?? '';
        $offerLetter = $app['offer_letter'] ?? '';
        $interviewDate = $app['interview_date'] ?? '';
        $interviewTime = $app['interview_time'] ?? '';
        $applicationRejReason = $app['reason'] ?? '';            // Application rejection reason
        $selectionRejReason = $app['rejection_reason'] ?? '';    // Selection/interview rejection reason

        echo '<div class="alert alert-light border mb-3">';
        echo '<h6 class="mb-1">Job: <strong>' . $jobTitle . '</strong></h6>';

        $isRejected = false;

        // Application-level rejection
        if ($status === 'Rejected') {
            echo '<span class="badge bg-danger">Application Rejected</span>';
            $isRejected = true;
            if (!empty($applicationRejReason)) {
                echo '<div class="text-danger small mt-1">Application Rejection: <em>' . htmlspecialchars($applicationRejReason) . '</em></div>';
            }
        }

        // Interview/selection-level rejection
        if ($selectionStatus === 'Rejected' || $interviewStatus === 'Rejected') {
            echo '<span class="badge bg-danger ms-2">Rejected</span>';
            $isRejected = true;
            if (!empty($selectionRejReason)) {
                echo '<div class="text-danger small mt-1">Rejection Reason: <em>' . htmlspecialchars($selectionRejReason) . '</em></div>';
            }
        }

        // If NOT rejected, show other statuses
        if (!$isRejected) {
            if ($selectionStatus === 'Selected') {
    echo '<span class="badge bg-success">Selected</span><br>';
    echo '<a href="offer_letter_view.php?id=' . $app['id'] . '" target="_blank" class="btn btn-sm btn-success mt-2 me-2">View Offer Letter</a>';
    
  echo '<form method="POST" action="accept_offer_letter.php" style="display:inline;">
        <input type="hidden" name="application_id" value="' . $app['id'] . '">
        <button type="submit" class="btn btn-sm btn-outline-primary mt-2">Accept Offer</button>
      </form>';
             } elseif ($status === 'Shortlisted') {
                echo '🎉Congratulations You are <span class="badge bg-info">Shortlisted</span> For Interview...😊<br>';
                if (!empty($interviewDate)) {
                    echo '<a href="interview_letter.php?id=' . $app['id'] . '" target="_blank" class="btn btn-sm btn-primary mt-2">View Interview Letter</a>';
                }
            } elseif ($interviewStatus === 'Attended' && $selectionStatus === 'Pending') {
                echo '<span class="badge bg-warning text-dark">In Progress</span>';
            } else {
                echo '<span class="badge bg-secondary">In Progress</span>';
            }
        }

        // Show interview date/time only if not rejected or not pending selection after attended interview
       // Show interview date/time only if offer letter is NOT received and not rejected
$showDateTime = true;
if (
    $isRejected || 
    $selectionStatus === 'Selected' ||   // Hide when offer letter is received
    ($interviewStatus === 'Attended' && $selectionStatus === 'Pending')
) {
    $showDateTime = false;
}

        if (!empty($interviewDate) && $showDateTime) {
            echo '<div class="mt-2">';
            echo '🗓 <strong>' . date("F j, Y", strtotime($interviewDate)) . '</strong><br>';
            echo '⏰ <strong>' . ($interviewTime ? date("h:i A", strtotime($interviewTime)) : 'TBA') . '</strong>';
            echo '</div>';
        }

        echo '</div>';
    }
} else {
    echo '<p class="text-muted mb-0">No new notifications.</p>';
}

$stmt->close();
$conn->close();
?>
