<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "company_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

session_start();

// ------------------------------------------------
// ✅ Handle AJAX: Fetch employees by job title
// ------------------------------------------------
if (isset($_GET['ajax']) && $_GET['ajax'] == '1' && isset($_GET['job_id'])) {
    $job_id = intval($_GET['job_id']);

    $stmt = $conn->prepare("SELECT id AS employee_id, full_name FROM employees WHERE job_id = ? ORDER BY full_name ASC");
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<option value="">-- Choose Employee --</option>';
    while ($emp = $result->fetch_assoc()) {
        echo '<option value="' . htmlspecialchars($emp['employee_id']) . '">' . htmlspecialchars($emp['full_name']) . '</option>';
    }
    $stmt->close();
    exit;
}

// ------------------------------------------------
// ✅ Handle form submission: Assign new project
// ------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = intval($_POST['employee_id']);
    $project_name = trim($_POST['project_name']);
    $description = trim($_POST['description']);

    if ($employee_id > 0 && !empty($project_name)) {

        // ✅ Check if employee already has a project "In Progress"
        $check_stmt = $conn->prepare("SELECT id FROM projects WHERE employee_id = ? AND status = 'In Progress' LIMIT 1");
        $check_stmt->bind_param("i", $employee_id);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            echo "<script>alert('⚠️ This employee already has a project in progress! Cannot assign a new one until completed.'); window.location='assign_project.php';</script>";
            $check_stmt->close();
            exit;
        }
        $check_stmt->close();

        // ✅ If no ongoing project, assign new one
        $stmt = $conn->prepare("INSERT INTO projects (employee_id, project_name, description, progress, status) VALUES (?, ?, ?, 0, 'In Progress')");
        $stmt->bind_param("iss", $employee_id, $project_name, $description);
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('✅ Project assigned successfully!'); window.location='assign_project.php';</script>";
        exit;
    } else {
        echo "<script>alert('⚠️ Please select a valid employee and enter project details.');</script>";
    }
}

// ------------------------------------------------
// ✅ Fetch job titles for dropdown
// ------------------------------------------------
$title_query = "SELECT job_id, title FROM jobs ORDER BY title ASC";
$title_result = $conn->query($title_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Project - HR Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f6fa;
            padding: 30px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            background: white;
            max-width: 550px;
            margin: 20px auto;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        label {
            display: block;
            font-weight: bold;
            margin-top: 10px;
        }
        input[type="text"], textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            background: #007BFF;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            margin-top: 15px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        button:hover {
            background: #0056b3;
        }
    </style>

    <script>
        function fetchEmployeesByJob(jobId) {
            if (jobId === "") {
                document.getElementById("employee_id").innerHTML = '<option value="">-- Choose Employee --</option>';
                return;
            }
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "assign_project.php?ajax=1&job_id=" + encodeURIComponent(jobId), true);
            xhr.onload = function() {
                if (this.status === 200) {
                    document.getElementById("employee_id").innerHTML = this.responseText;
                }
            };
            xhr.send();
        }
    </script>
</head>
<body>

<h2>Assign New Project</h2>

<form method="POST">
    <label for="job_id">Select Job Title:</label>
    <select name="job_id" id="job_id" onchange="fetchEmployeesByJob(this.value)" required>
        <option value="">-- Choose Job Title --</option>
        <?php while ($row = $title_result->fetch_assoc()): ?>
            <option value="<?php echo htmlspecialchars($row['job_id']); ?>">
                <?php echo htmlspecialchars($row['title']); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label for="employee_id">Select Employee:</label>
    <select name="employee_id" id="employee_id" required>
        <option value="">-- Choose Employee --</option>
    </select>

    <label for="project_name">Project Name:</label>
    <input type="text" name="project_name" required>

    <label for="description">Project Description:</label>
    <textarea name="description" rows="4" required></textarea>

    <button type="submit">Assign Project</button>
</form>

</body>
</html>
