<?php
session_start();

$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$id = intval($_GET['id']);
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];

    // Validate dates: from_date and to_date should be valid and from_date <= to_date
    if (!$from_date || !$to_date) {
        $errorMessage = "Both From Date and To Date are required.";
    } elseif ($from_date > $to_date) {
        $errorMessage = "From Date cannot be later than To Date.";
    } else {
        // Validate image if uploaded
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $fileType = mime_content_type($_FILES['image']['tmp_name']);

            if (!in_array($fileType, $allowedTypes)) {
                $errorMessage = "Only JPG, JPEG, PNG, and GIF image formats are allowed.";
            }
        }

        if (empty($errorMessage)) {
            // Update text fields first
            $sql = "UPDATE jobs SET title='$title', description='$description', from_date='$from_date', to_date='$to_date' WHERE job_id=$id";

            if ($conn->query($sql) === TRUE) {
                // Handle image upload if no error and file uploaded
                if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                    $target_dir = "uploads/";
                    $image_name = basename($_FILES["image"]["name"]);
                    $target_file = $target_dir . $image_name;

                    // Delete old image if exists
                    $getOld = $conn->query("SELECT image FROM jobs WHERE job_id = $id");
                    if ($getOld && $getOld->num_rows > 0) {
                        $old = $getOld->fetch_assoc();
                        if (!empty($old['image']) && file_exists($target_dir . $old['image'])) {
                            unlink($target_dir . $old['image']);
                        }
                    }

                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        $conn->query("UPDATE jobs SET image='$image_name' WHERE job_id=$id");
                    } else {
                        $errorMessage = "Failed to upload the image.";
                    }
                }

                if (empty($errorMessage)) {
                    // Redirect on success
                    header("Location: manage_jobs.php");
                    exit();
                }
            } else {
                $errorMessage = "Error updating job: " . $conn->error;
            }
        }
    }
}

$result = $conn->query("SELECT * FROM jobs WHERE job_id = $id");
$job = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Job</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 40px; }
    form { background: #fff; padding: 20px; max-width: 500px; margin: auto; border-radius: 8px; }
    label { display: block; margin: 12px 0 4px; }
    input[type="text"], textarea, input[type="date"], input[type="file"] { width: 100%; padding: 8px; }
    button { background: #4CAF50; color: #fff; border: none; padding: 10px 20px; margin-top: 15px; cursor: pointer; }
    .success { color: green; margin-top: 10px; }
    .error { color: red; margin-top: 10px; }
    img.preview { max-width: 150px; margin-top: 10px; display: block; border: 1px solid #ccc; border-radius: 4px; }
  </style>
</head>
<body>

<h2 style="text-align:center;">Edit Job Post</h2>

<form method="POST" enctype="multipart/form-data" novalidate>
  <label>Title:</label>
  <input type="text" name="title" value="<?= htmlspecialchars($job['title']) ?>" required>

  <label>Description:</label>
  <textarea name="description" rows="5" required><?= htmlspecialchars($job['description']) ?></textarea>

  <label>From Date:</label>
  <input type="date" name="from_date" value="<?= htmlspecialchars($job['from_date']) ?>" required>

  <label>To Date:</label>
  <input type="date" name="to_date" value="<?= htmlspecialchars($job['to_date']) ?>" required>

  <label>Job Image:</label>
  <?php if (!empty($job['image'])): ?>
    <img src="uploads/<?= htmlspecialchars($job['image']) ?>" alt="Job Image" class="preview">
  <?php endif; ?>
  <input type="file" name="image" accept="image/*">

  <button type="submit">Update Job</button>

  <?php if ($errorMessage): ?>
    <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
  <?php endif; ?>
</form>

</body>
</html>
