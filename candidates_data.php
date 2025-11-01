<?php
$conn = new mysqli("localhost", "root", "", "company_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Select columns excluding skills, employee_id, employee_type, password
$sql = "SELECT id, full_name, email, contact, gender, qualification, dob, photo, resume, experience_status, experience_details FROM candidates";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Candidates List</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <style>
        body {
            background: #f8f9fa;
            margin: 0;
            padding: 0;
            height: 100vh;
        }
        img.candidate-photo {
            height: 60px;
            width: auto;
            border-radius: 5px;
            object-fit: cover;
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
<div class="container mt-4">
    <h2 class="mb-4">Candidates List</h2>
    <?php if ($result && $result->num_rows > 0): ?>
            <table class="table table-bordered table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Gender</th>
                        <th>Qualification</th>
                        <th>DOB</th>
                        <th>Photo</th>
                        <th>Resume</th>
                        <th>Experience Status</th>
                        <th>Experience Details</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['contact']) ?></td>
                        <td><?= htmlspecialchars($row['gender']) ?></td>
                        <td><?= htmlspecialchars($row['qualification']) ?></td>
                        <td><?= htmlspecialchars($row['dob']) ?></td>
                        <td>
                            <?php if (!empty($row['photo'])): ?>
                                <img src="uploads/photos/<?= htmlspecialchars($row['photo']) ?>" alt="Photo" class="candidate-photo" />
                            <?php else: ?>
                                <span>No Photo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($row['resume'])): ?>
                                <a href="uploads/resumes/<?= htmlspecialchars($row['resume']) ?>" target="_blank" class="btn btn-sm btn-primary">View Resume</a>
                            <?php else: ?>
                                <span>No Resume</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['experience_status']) ?></td>
                        <td><?= nl2br(htmlspecialchars($row['experience_details'])) ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
    <?php else: ?>
        <div class="alert alert-info">No candidate records found.</div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
