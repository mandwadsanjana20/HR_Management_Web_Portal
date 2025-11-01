<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// ✅ Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_id = intval($_POST['application_id']);
    $meet_link = $conn->real_escape_string($_POST['meet_link']);
    $attendance_status = $conn->real_escape_string($_POST['interview_status']);
    $selection_status = isset($_POST['selection_status']) ? $conn->real_escape_string($_POST['selection_status']) : 'Pending';
    $rejection_reason = ($selection_status === 'Rejected' && isset($_POST['rejection_reason'])) ? $conn->real_escape_string($_POST['rejection_reason']) : '';

    $update_sql = "
        UPDATE job_applications
        SET 
            meet_link='$meet_link',
            interview_status='$attendance_status',
            selection_status='$selection_status',
            rejection_reason='$rejection_reason'
        WHERE id=$application_id
    ";

    if (!$conn->query($update_sql)) {
        $message = "Error: " . $conn->error;
    } else {
        if ($attendance_status === 'Attended' && $selection_status === 'Selected') {
            // ✅ Only update status – candidate will see Offer Letter in dashboard
            $message = "Candidate marked as Selected. Offer Letter will appear in Candidate Dashboard.";
        } elseif ($selection_status === 'Rejected') {
            $message = "Candidate rejected with reason saved.";
        } else {
            $message = "Interview status updated.";
        }
    }
}

// ✅ Fetch Interview Data
$sql = "
    SELECT ja.*, c.full_name, c.email, c.resume, j.title AS job_title
    FROM job_applications ja
    JOIN candidates c ON ja.candidate_id = c.id
    JOIN jobs j ON ja.job_id = j.job_id
    WHERE ja.status='Shortlisted'
    ORDER BY ja.interview_date ASC
";
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Interview Schedule & Selection</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body { padding: 30px; }
    .message { font-weight: bold; color: green; text-align: center; }
    .table-responsive { max-height: 600px; overflow-y: auto; }
    .table thead th {
      position: sticky;
      top: 0;
      z-index: 2;
      background: #212529;
      color: #fff;
    }
    .meet-link-input { width: 250px; min-width: 200px; }
    .rejection-reason {
      width: 250px;
      min-height: 50px;
      font-size: 14px;
      resize: vertical;
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

<h2>Interview Schedule & Selection</h2>
<?php if ($message): ?>
  <p class="message"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<table class="table table-bordered">
  <thead class="table-dark">
    <tr>
      <th>Candidate</th>
      <th>Email</th>
      <th>Job</th>
      <th>Date</th>
      <th>Time</th>
      <th>Meet Link</th>
      <th>Resume</th>
      <th>Attendance</th>
      <th>Selection</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
  <?php if ($result->num_rows > 0): while ($row = $result->fetch_assoc()): ?>
    <?php
      $isSelected = $row['selection_status'] === 'Selected';
      $isRejected = $row['selection_status'] === 'Rejected';
      $disableButton = ($isSelected || $isRejected);
    ?>
    <tr>
      <form method="POST" class="status-form">
        <td><?= htmlspecialchars($row['full_name']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= htmlspecialchars($row['job_title']) ?></td>
       <td><?= htmlspecialchars($row['interview_date'] ?? '') ?></td>
       <td><?= htmlspecialchars($row['interview_time'] ?? '') ?></td>
<td>
  <input name="meet_link" value="<?= htmlspecialchars($row['meet_link'] ?? '') ?>" class="form-control meet-link-input" />
</td>
        <td>
          <?php if ($row['resume']): ?>
            <a href="preview_resume.php?id=<?= $row['candidate_id'] ?>" target="_blank">View</a>
          <?php else: ?> No Resume <?php endif; ?>
        </td>
        <td>
          <select name="interview_status" class="form-select attendance-select">
            <option value="Pending" <?= $row['interview_status']=='Pending'?'selected':'' ?>>Pending</option>
            <option value="Attended" <?= $row['interview_status']=='Attended'?'selected':'' ?>>Attended</option>
            <option value="Not Attended" <?= $row['interview_status']=='Not Attended'?'selected':'' ?>>Not Attended</option>
          </select>
        </td>
        <td>
          <select name="selection_status" class="form-select selection-status" <?= $row['interview_status'] !== 'Attended' ? 'disabled' : '' ?>>
            <option value="Pending" <?= $row['selection_status']=='Pending'?'selected':'' ?>>Pending</option>
            <option value="Selected" <?= $row['selection_status']=='Selected'?'selected':'' ?>>Selected</option>
            <option value="Rejected" <?= $row['selection_status']=='Rejected'?'selected':'' ?>>Rejected</option>
          </select>
          <textarea name="rejection_reason" class="form-control rejection-reason mt-1" placeholder="Reason" style="display:<?= $row['selection_status']=='Rejected' ? 'block' : 'none' ?>"><?= htmlspecialchars($row['rejection_reason']) ?></textarea>
        </td>
        <td>
          <input type="hidden" name="application_id" value="<?= $row['id'] ?>">
          <button type="submit" class="btn btn-primary" <?= $disableButton ? 'disabled' : '' ?>>Update</button>
        </td>
      </form>
    </tr>
  <?php endwhile; else: ?>
    <tr><td colspan="10" class="text-center">No interviews found.</td></tr>
  <?php endif; ?>
  </tbody>
</table>

<script>
document.querySelectorAll('.attendance-select').forEach(select => {
  select.addEventListener('change', e => {
    const form = e.target.closest('form');
    const selection = form.querySelector('.selection-status');
    if (e.target.value === 'Attended') {
      selection.disabled = false;
    } else {
      selection.disabled = true;
      form.querySelector('.rejection-reason').style.display = 'none';
      selection.value = 'Pending';
    }
  });
});

document.querySelectorAll('.selection-status').forEach(select => {
  select.addEventListener('change', e => {
    const form = e.target.closest('form');
    const reason = form.querySelector('.rejection-reason');
    reason.style.display = e.target.value === 'Rejected' ? 'block' : 'none';
  });
});

document.querySelectorAll('.status-form').forEach(form => {
  form.addEventListener('submit', e => {
    const sel = form.querySelector('.selection-status').value;
    const reason = form.querySelector('.rejection-reason').value.trim();
    if (sel === 'Rejected' && reason === '') {
      alert('Please provide rejection reason.');
      e.preventDefault();
    }
  });
});
</script>

</body>
</html>
