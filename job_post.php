<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Connect DB
$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$jobs = [];
$today = date('Y-m-d');
$status = isset($_GET['status']) ? $_GET['status'] : '';
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

// Get candidate applied jobs if logged in 
$appliedJobs = [];
if (isset($_SESSION['candidate_id'])) {
    $cid = intval($_SESSION['candidate_id']);
    $resultApplied = $conn->query("SELECT job_id FROM job_applications WHERE candidate_id = $cid");
    if ($resultApplied && $resultApplied->num_rows > 0) {
        while ($r = $resultApplied->fetch_assoc()) {
            $appliedJobs[] = $r['job_id'];
        }
    }
}

// Build SQL based on status and query
if (!empty($query)) {
    $queryEscaped = $conn->real_escape_string($query);

    if ($status == 'closed') {
        // Only closed jobs with search
        $sql = "SELECT * FROM jobs WHERE (title LIKE '%$queryEscaped%' OR description LIKE '%$queryEscaped%') AND to_date < '$today' ORDER BY posted_date DESC";
    } else {
        // ongoing or default: show ongoing + upcoming with search
        // We'll filter upcoming in PHP below
        $sql = "SELECT * FROM jobs WHERE (title LIKE '%$queryEscaped%' OR description LIKE '%$queryEscaped%') ORDER BY posted_date DESC";
    }
} else {
    if ($status == 'closed') {
        $sql = "SELECT * FROM jobs WHERE to_date < '$today' ORDER BY posted_date DESC";
    } else {
        // ongoing or default: show all jobs to handle upcoming/ongoing in PHP
        $sql = "SELECT * FROM jobs ORDER BY posted_date DESC";
    }
}

// Execute query
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $jobs[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Job Postings</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #e0e0e0 0%, #f9f9f9 100%);
      color: #333;
        animation: pageFadeSlideIn 1.2s ease forwards;

    }
    @keyframes pageFadeSlideIn {
  0% {
    opacity: 0;
    transform: translateY(40px);
    filter: blur(15px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
    filter: blur(0);
  }
}
    .color-1 { background: #f0f9ff !important; }
    .color-2 { background: #fff7e6 !important; }
    .color-3 { background: #fef6fb !important; }
    .color-4 { background: #f5f5f5 !important; }
    .color-5 { background: #f9f0f0 !important; }
    .color-6 { background: #f4f9f2 !important; }

    .jobs { padding: 40px 15px; max-width: 1300px; margin: auto; text-align: center; }
    .jobs h5 { text-transform: uppercase; letter-spacing: 4px; color: #555; font-weight: 700; margin-bottom: 10px; }
    .jobs h1 { font-weight: 800; margin-bottom: 20px; color: #222; font-size: 2.5rem; }

    .filter-buttons .btn { font-weight: 600; border-radius: 50px; padding: 10px 20px; }
    .filter-buttons .active { box-shadow: 0 4px 10px rgba(0,0,0,0.2); }

    .job-card {
      border-radius: 14px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
      border: 1px solid #ddd;
      height: 340px;
      display: flex;
      flex-direction: column;
      transition: transform 0.25s ease, box-shadow 0.25s ease;
      overflow: hidden;
      text-align: left;
    }
    .job-card:hover { transform: translateY(-6px); box-shadow: 0 12px 30px rgba(0,0,0,0.15); border-color: #333; }
    .job-card img { height: 120px; object-fit: cover; border-radius: 8px 8px 0 0; width: 100%; }
    .card-body { flex: 1; display: flex; flex-direction: column; padding: 1rem; }
    .job-content h3 { font-size: 1.2rem; margin-bottom: 8px; color: #111; }
    .job-content p { flex: 1; font-size: 0.95rem; color: #555; margin-bottom: 8px; }
    .apply-btn { font-weight: 600; border-radius: 50px; padding: 10px 22px; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 8px; }
    .apply-btn i { font-size: 1rem; transition: transform 0.3s ease; }
    .apply-btn:hover i { transform: translateX(5px); }
    .apply-btn:disabled { background: #999 !important; cursor: not-allowed !important; color: #eee !important; }

    @media (max-width: 480px) {
      .jobs h1 { font-size: 1.8rem; }
      .job-card { height: auto; }
    }
  </style>
</head>
<body>

<?php include('navbar.php'); ?>

<section class="jobs fade-up">
  <div class="container">
    <h5>Featured Job Posts</h5>
    <h1>Search For Job Posts And Apply</h1>

    <!-- Filter Buttons -->
    <div class="filter-buttons d-flex justify-content-center mb-4">
      <a href="?status=ongoing<?php echo !empty($query) ? '&query='.urlencode($query) : ''; ?>" class="btn btn-success mx-2 <?php echo ($status=='ongoing' || $status=='')?'active':''; ?>">Ongoing Jobs</a>
      <a href="?status=closed<?php echo !empty($query) ? '&query='.urlencode($query) : ''; ?>" class="btn btn-danger mx-2 <?php echo ($status=='closed')?'active':''; ?>">Closed Jobs</a>
    </div>

    <div class="row">
      <?php
      if (!empty($jobs)) {
        $count = 1;
        foreach ($jobs as $row) {
          $colorClass = "color-" . $count;
          $from = $row['from_date'];
          $to = $row['to_date'];

          // Handle visibility & button logic
          if ($status === 'closed') {
            // Show only closed jobs (to_date < today)
            if ($to >= $today) continue; // skip non-closed
            $canApply = false; // closed => no apply
            $isUpcoming = false;
          } else {
            // Ongoing or default tab
            // Skip closed jobs
            if ($to < $today) continue;

            if ($from > $today) {
              // Upcoming job => show but disable apply
              $canApply = false;
              $isUpcoming = true;
            } else {
              // Started job => enable apply if within from_date and to_date
              $isUpcoming = false;
              $canApply = ($today >= $from && $today <= $to);
            }
          }

          $image = !empty($row['image']) ? $row['image'] : 'default.jpg';
      ?>
          <div class="col-sm-6 col-md-4 mb-4 d-flex align-items-stretch">
            <div class="card job-card <?php echo $colorClass; ?>">
              <img src="uploads/<?php echo htmlspecialchars($image); ?>" class="card-img-top" alt="Job Image">
              <div class="card-body job-content d-flex flex-column">
                <h3 class="d-flex justify-content-between align-items-center">
                  <span><?php echo htmlspecialchars($row['title']); ?></span>
                  <small class="text-muted" style="font-weight:400; font-size:0.85rem;">
                    Posted on: <?php echo htmlspecialchars($row['posted_date']); ?>
                  </small>
                </h3>
                <p><?php echo htmlspecialchars(substr($row['description'], 0, 100)) . '...'; ?></p>
                <small><strong>Starts From:</strong> <?php echo htmlspecialchars($from); ?></small><br>
                <small><strong>To Date:</strong> <?php echo htmlspecialchars($to); ?></small><br>

                <?php if (in_array($row['job_id'], $appliedJobs)): ?>
                  <button class="btn apply-btn mt-auto" disabled>Applied</button>
                <?php elseif ($isUpcoming): ?>
                  <button class="btn apply-btn mt-auto" disabled>Upcoming</button>
                <?php elseif ($canApply): ?>
                  <a href="apply.php?job_id=<?php echo urlencode($row['job_id']); ?>" class="btn btn-dark apply-btn mt-auto">Apply Now <i class="fas fa-arrow-right"></i></a>
                <?php else: ?>
                  <button class="btn apply-btn mt-auto" disabled>Closed</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
      <?php
          $count++;
          if ($count > 6) $count = 1;
        }
      } else {
        echo '<div class="col-12"><p class="text-center">No jobs found';
        if (!empty($query)) echo " for '<strong>" . htmlspecialchars($query) . "</strong>'";
        echo ".</p></div>";
      }
      ?>
    </div>
  </div>
</section>

<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
