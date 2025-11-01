<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";           // update if needed
$dbname = "company_db";  // update with your DB name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from employees table
$sql = "SELECT * FROM employees ORDER BY created_at ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Panel - Employees</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            margin: 0;
            padding: 20px;
            background: #f7f7f7;
            font-family: Arial, sans-serif;
        }
        .photo-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
        }
         table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px 15px;
            text-align: left;
        }

        th {
            background: #222;
            color: #fff;
        }

        tr:hover {
            background: #e2e2e2;
        }
        
  /* ✅ Sticky table header */
  .table-responsive {
    max-height: 600px;
    overflow-y: auto;
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
<div class="container mt-5">
    <h2 class="mb-4">Employees List</h2>
    <?php if ($result->num_rows > 0): ?>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Employee ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Gender</th>
                <th>Qualification</th>
                <th>DOB</th>
                <th>Photo</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['id']); ?></td>
                <td><?= htmlspecialchars($row['employee_id']); ?></td>
                <td><?= htmlspecialchars($row['full_name']); ?></td>
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td><?= htmlspecialchars($row['contact']); ?></td>
                <td><?= htmlspecialchars($row['gender']); ?></td>
                <td><?= htmlspecialchars($row['qualification']); ?></td>
                <td><?= htmlspecialchars($row['dob']); ?></td>
                <td>
                    <?php if (!empty($row['photo'])): ?>
                        <img src="<?= htmlspecialchars($row['photo']); ?>" alt="Photo" class="photo-img" />
                    <?php else: ?>
                        <small>No Photo</small>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['created_at']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>No employee records found.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
