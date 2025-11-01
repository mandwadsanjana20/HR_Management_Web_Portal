<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle date range filtering
$fromDate = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$toDate = isset($_GET['to_date']) ? $_GET['to_date'] : '';

// Build SQL query
$sql = "SELECT ja.*, j.title 
        FROM job_applications ja
        JOIN jobs j ON ja.job_id = j.job_id";

$where = [];

if (!empty($fromDate)) {
    $where[] = "DATE(ja.applied_on) >= '" . $conn->real_escape_string($fromDate) . "'";
}

if (!empty($toDate)) {
    $where[] = "DATE(ja.applied_on) <= '" . $conn->real_escape_string($toDate) . "'";
}

if (!empty($where)) {
    $sql .= " WHERE " . implode(' AND ', $where);
}

$sql .= " ORDER BY ja.applied_on DESC";

$result = $conn->query($sql);
$showingRecords = $result->num_rows;
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manage Job Applications</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>
    .modal-custom {
      display: none;
      position: fixed;
      z-index: 1050;
      inset: 0;
      overflow-y: auto;
      background: rgba(0,0,0,0.5);
      padding: 1rem;
    }
    .modal-custom .modal-content-custom {
      background: #fff;
      border-radius: 0.5rem;
      max-width: 400px;
      margin: 5% auto;
      padding: 1.5rem;
      box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
      text-align: center;
    }
    .modal-custom textarea {
      resize: none;
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
<body class="bg-light">

<div class="container py-5">
  <h1 class="text-center mb-4">Manage Job Applications</h1>
<!-- ✅ Filter form -->
  <div class="mb-4">
    <form method="GET" class="row g-2 justify-content-end align-items-end">
      <div class="col-auto">
        <label for="from_date" class="form-label fw-semibold">From:</label>
        <input type="date" name="from_date" id="from_date" class="form-control" value="<?= htmlspecialchars($fromDate) ?>" />
      </div>
      <div class="col-auto">
        <label for="to_date" class="form-label fw-semibold">To:</label>
        <input type="date" name="to_date" id="to_date" class="form-control" value="<?= htmlspecialchars($toDate) ?>" />
      </div>
      <div class="col-auto">
        <button type="submit" class="btn btn-dark">Filter</button>
        <a href="manage_applications.php" class="btn btn-secondary">Reset</a>
      </div>
    </form>
  </div>


  <table class="table table-bordered table-hover align-middle text-center bg-white shadow-sm">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Candidate Name</th>
        <th>Job Name</th>
        <th>Contact</th>
        <th>Applied on</th>
        <th>Current Status</th>
        <th style="min-width: 180px;">Action</th>
      </tr>
    </thead>
    <tbody>
     <?php if ($showingRecords > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr id="row-<?= $row['id'] ?>">
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= date("Y-m-d", strtotime($row['applied_on'])) ?></td>

            <td class="status-cell">
              <?php if ($row['status'] === 'Rejected'): ?>
                <span class="badge bg-danger"><?= htmlspecialchars($row['status']) ?></span>
              <?php elseif ($row['status'] === 'Shortlisted'): ?>
                <span class="badge bg-success"><?= htmlspecialchars($row['status']) ?></span>
              <?php else: ?>
                <span class="badge bg-secondary"><?= htmlspecialchars($row['status']) ?></span>
              <?php endif; ?>
            </td>
            <td class="action-cell">
              <?php if ($row['status'] === 'Shortlisted' || $row['status'] === 'Rejected'): ?>
                <span class="text-muted fst-italic">Action Completed</span>
              <?php else: ?>
                <form class="d-flex gap-2 justify-content-center align-items-center" onsubmit="return handleStatusChange(event, this, <?= $row['id'] ?>);">
                  <input type="hidden" name="application_id" value="<?= $row['id'] ?>" />
                  <input type="hidden" name="reason" value="" />
                  <select name="status" class="form-select form-select-sm" required>
                    <option value="">--Select--</option>
                    <option value="Rejected">Reject</option>
                    <option value="Shortlisted">Shortlist</option>
                  </select>
                  <button type="submit" class="btn btn-sm btn-success">Update</button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="6" class="text-center text-muted fst-italic">No job applications found for selected date.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
  </div>

<!-- Modal -->
<div id="reasonModal" class="modal-custom">
  <div class="modal-content-custom">
    <button type="button" class="btn-close float-end" onclick="closeModal()"></button>
    <h5 class="mb-3">Rejection Reason</h5>
    <textarea id="reasonText" class="form-control mb-3" rows="4" placeholder="Enter reason..." maxlength="300"></textarea>
    <div>
      <button class="btn btn-danger me-2" onclick="submitReason(true)">Submit</button>
      <button class="btn btn-secondary" onclick="submitReason(false)">Cancel</button>
    </div>
  </div>
</div>

<script>
let activeSelect = null;
let activeForm = null;
let activeRowId = null;

function handleStatusChange(e, form, rowId) {
  e.preventDefault();
  const select = form.querySelector('select[name="status"]');
  const status = select.value;

  if (status === 'Rejected') {
    activeSelect = select;
    activeForm = form;
    activeRowId = rowId;
    document.getElementById('reasonText').value = '';
    document.getElementById('reasonModal').style.display = 'block';
    return false;
  } else {
    if (confirm("Are you sure you want to shortlist this candidate?")) {
      updateStatusAJAX(rowId, form.querySelector('input[name="application_id"]').value, status, '');
    }
  }
}

function closeModal() {
  document.getElementById('reasonModal').style.display = 'none';
  if (activeSelect) {
    activeSelect.value = '';
  }
}

function submitReason(confirmSubmit) {
  if (confirmSubmit) {
    const reason = document.getElementById('reasonText').value.trim();
    if (!reason) {
      alert("Reason cannot be empty.");
      return;
    }
    if (confirm("Are you sure you want to reject this candidate?")) {
      const form = activeForm;
      const applicationId = form.querySelector('input[name="application_id"]').value;
      updateStatusAJAX(activeRowId, applicationId, 'Rejected', reason);
      closeModal();
    }
  } else {
    closeModal();
  }
}

function updateStatusAJAX(rowId, applicationId, status, reason) {
  fetch('update_candidate.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `application_id=${applicationId}&status=${encodeURIComponent(status)}&reason=${encodeURIComponent(reason)}`
  })
  .then(res => res.text())
  .then(response => {
    if (response.trim() === 'OK') {
      const row = document.getElementById('row-' + rowId);
      const statusCell = row.querySelector('.status-cell');
      const actionCell = row.querySelector('.action-cell');

      if (status === 'Rejected') {
        statusCell.innerHTML = `<span class="badge bg-danger">Rejected</span>`;
      } else {
        statusCell.innerHTML = `<span class="badge bg-success">Shortlisted</span>`;
      }

      actionCell.innerHTML = `<span class="text-muted fst-italic">Action Completed</span>`;
    } else {
      alert("Failed to update: " + response);
    }
  });
}

window.onclick = function(e) {
  const modal = document.getElementById('reasonModal');
  if (e.target === modal) {
    closeModal();
  }
};
</script>

</body>
</html>

<?php $conn->close(); ?>
