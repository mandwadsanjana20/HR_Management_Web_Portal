<?php
session_start();

// Connect DB
$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$successMessage = '';
$errorMessage = '';

// Date limits calculation
$today = new DateTime();
$tomorrow = $today->modify('+1 day')->format('Y-m-d');

$today = new DateTime();
$to_date_max_2m = $today->modify('+2 months')->format('Y-m-d');

if (isset($_POST['submit'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];

    // Server side date validation
    $current_date = new DateTime();
    $min_from_date = (clone $current_date)->modify('+1 day');
    $max_to_date = (clone $current_date)->modify('+2 months');

    if (new DateTime($from_date) < $min_from_date) {
        $errorMessage = "From Date must be from tomorrow onwards.";
    } elseif (new DateTime($to_date) > $max_to_date) {
        $errorMessage = "To Date cannot be more than 2 months from today.";
    } elseif (new DateTime($to_date) < new DateTime($from_date)) {
        $errorMessage = "To Date cannot be earlier than From Date.";
    } else {
        $image_name = '';

        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $tmp_name = $_FILES['image']['tmp_name'];
            $original_name = basename($_FILES['image']['name']);
            $unique_name = uniqid() . '_' . preg_replace("/[^A-Za-z0-9.]/", '', $original_name);
            $target = $upload_dir . $unique_name;

            // Server-side MIME type check (more strict)
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $tmp_name);
            finfo_close($finfo);

            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

            if (!in_array($mime_type, $allowed_types)) {
                $errorMessage = "Only JPG, PNG, WEBP, and GIF image files are allowed.";
            } elseif (!getimagesize($tmp_name)) {
                // Extra check to confirm file is an image
                $errorMessage = "Uploaded file is not a valid image.";
            } elseif (move_uploaded_file($tmp_name, $target)) {
                $image_name = $unique_name;
            } else {
                $errorMessage = "Failed to upload image.";
            }
        }

        if (empty($errorMessage)) {
            $sql = "INSERT INTO jobs (title, description, image, posted_date, from_date, to_date) 
                    VALUES ('$title', '$description', '$image_name', NOW(), '$from_date', '$to_date')";

            if ($conn->query($sql) === TRUE) {
                $successMessage = "✅ New job added successfully!";
            } else {
                $errorMessage = "Error: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Job Posting</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">

        <div class="card shadow-lg">
          <div class="card-body p-4">
            <h2 class="card-title text-center mb-4">Add New Job Posting</h2>

            <form method="POST" action="" enctype="multipart/form-data" novalidate id="jobForm">

              <div class="mb-3">
                <label for="title" class="form-label">Job Title</label>
                <input type="text" name="title" id="title" class="form-control" required>
              </div>

              <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" rows="5" class="form-control" required></textarea>
              </div>

              <div class="mb-3">
                <label for="from_date" class="form-label">From Date</label>
                <input type="date" name="from_date" id="from_date" class="form-control" required
                       min="<?= $tomorrow ?>" />
              </div>

              <div class="mb-3">
                <label for="to_date" class="form-label">To Date</label>
                <input type="date" name="to_date" id="to_date" class="form-control" required
                       min="<?= $tomorrow ?>" max="<?= $to_date_max_2m ?>" />
              </div>

              <div class="mb-3">
                <label for="image" class="form-label">Job Image (optional)</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*">
                <div id="imageError" class="text-danger mt-1" style="display:none;">Only image files (jpg, png, gif, webp) are allowed.</div>
              </div>

              <button type="submit" name="submit" class="btn btn-danger w-100">Add Job</button>

            </form>

            <?php if ($successMessage): ?>
              <div class="alert alert-success mt-3 text-center">
                <?= $successMessage ?>
              </div>
            <?php endif; ?>

            <?php if ($errorMessage): ?>
              <div class="alert alert-danger mt-3 text-center">
                <?= $errorMessage ?>
              </div>
            <?php endif; ?>

          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
  document.addEventListener('DOMContentLoaded', function () {
      const fromDateInput = document.getElementById('from_date');
      const toDateInput = document.getElementById('to_date');
      const imageInput = document.getElementById('image');
      const imageError = document.getElementById('imageError');
      const form = document.getElementById('jobForm');

      // Date input logic
      fromDateInput.addEventListener('change', function () {
          let fromDate = new Date(this.value);
          let tomorrow = new Date();
          tomorrow.setDate(tomorrow.getDate() + 1);

          if (fromDate < tomorrow) {
              fromDate = tomorrow;
              this.value = fromDate.toISOString().split('T')[0];
          }

          toDateInput.min = this.value;

          if (toDateInput.value < toDateInput.min) {
              toDateInput.value = toDateInput.min;
          }
      });

      // Client-side image validation
      imageInput.addEventListener('change', function () {
          imageError.style.display = 'none';
          const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
          if (this.files.length > 0) {
              const fileType = this.files[0].type;
              if (!allowedTypes.includes(fileType)) {
                  imageError.style.display = 'block';
                  this.value = ''; // Clear invalid file
              }
          }
      });

      // Final form submit validation for image
      form.addEventListener('submit', function (e) {
          imageError.style.display = 'none';
          if (imageInput.files.length > 0) {
              const fileType = imageInput.files[0].type;
              const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
              if (!allowedTypes.includes(fileType)) {
                  imageError.style.display = 'block';
                  e.preventDefault();
              }
          }
      });
  });
  </script>

</body>
</html>
