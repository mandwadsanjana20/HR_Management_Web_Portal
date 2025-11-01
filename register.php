<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $full_name = $first_name . ' ' . $middle_name . ' ' . $last_name;

    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $contact = $_POST['contact'];
    $gender = $_POST['gender'];
    $qualification = $_POST['qualification'];
    $dob = $_POST['dob'];
    $experience = $_POST['experience'];
    $experience_details = $experience === "Yes" ? $_POST['experience_details'] : "";

    $photo = $_FILES['photo']['name'];
    $photo_tmp = $_FILES['photo']['tmp_name'];
    $photo_type = mime_content_type($photo_tmp);

    $resume = $_FILES['resume']['name'];
    $resume_tmp = $_FILES['resume']['tmp_name'];

    // Validate photo format
    $allowed_types = ['image/jpeg', 'image/png'];
    if (!in_array($photo_type, $allowed_types)) {
        echo "<script>alert('Only JPG, JPEG, and PNG formats are allowed for photo.'); window.history.back();</script>";
        exit();
    }

    // Validate image dimensions
    list($width, $height) = getimagesize($photo_tmp);
    if ($width != 350 || $height != 450) {
        echo "<script>alert('Photo must be exactly 350px by 450px.'); window.history.back();</script>";
        exit();
    }

    // Prepare upload directories
    if (!is_dir("uploads/photos")) mkdir("uploads/photos", 0777, true);
    if (!is_dir("uploads/resumes")) mkdir("uploads/resumes", 0777, true);

    // Set file destinations
    $target_photo = "uploads/photos/" . basename($photo);
    $target_resume = "uploads/resumes/" . basename($resume);

    // Move uploaded files
    move_uploaded_file($photo_tmp, $target_photo);
    move_uploaded_file($resume_tmp, $target_resume);

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO candidates 
        (full_name, email, password, contact, gender, qualification, dob, photo, resume, experience_status, experience_details)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssss", $full_name, $email, $password, $contact, $gender, $qualification, $dob, $photo, $resume, $experience, $experience_details);

   if ($stmt->execute()) {
    // Auto login after registration
    $_SESSION['candidate_id'] = $conn->insert_id;
    $_SESSION['candidate_name'] = $full_name;
    $_SESSION['candidate_email'] = $email;

    header("Location: job_post.php"); // 👈 Redirect to candidate profile page directly
    exit();
} else {
    echo "Error: " . $conn->error;
}

    $job_id=isset($_GET['job_id'])? intval($_GET['job_id']):0;
    //get job details
    $sql = "SELECT * FROM jobs WHERE id = $job_id";
    $result = $conn->query($sql);
    $job=$result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Candidate Registration</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #e0e0e0 0%, #f9f9f9 100%);
            margin: 0;
            padding: 20px;
            animation: fadeInBody 1s ease-in;
        }

        .container {
            background: #fff;
            max-width: 750px;
            margin: auto;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            animation: slideIn 1s ease;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #00394f;
            text-shadow: 1px 1px #ccc;
            animation: fadeDown 0.8s ease;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        .form-row {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .form-group {
            flex: 1;
            min-width: 220px;
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-bottom: 6px;
            color: #444;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="date"],
        select,
        textarea {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 15px;
            transition: 0.3s ease;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #008891;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 136, 145, 0.3);
        }

        input[type="file"] {
            padding: 8px;
        }

        .radio-group {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 5px;
        }

        .experience-box {
            margin-top: 10px;
            display: none;
        }

        button[type="submit"] {
            background-color: #000;
            color: white;
            border: none;
            margin-top: 30px;
            cursor: pointer;
            padding: 14px;
            font-size: 16px;
            border-radius: 8px;
            transition: 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: red;
        }

        .navbar-center img {
            display: block;
            margin: auto;
            margin-bottom: 20px;
            height: 50px;
            animation: zoomIn 1.2s ease;
        }

        @keyframes fadeInBody {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(40px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes fadeDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes zoomIn {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
            }
        }

        .required-star {
            color: red;
            font-weight: 400;
            margin-right: 5px;
        }

    </style>

    <script>
        function toggleExperienceBox(value) {
            const expBox = document.getElementById("experience-box");
            const expDetails = document.getElementById("experience_details");

            if (value === "Yes") {
                expBox.style.display = "block";
                expDetails.setAttribute("required", "required");
            } else {
                expBox.style.display = "none";
                expDetails.removeAttribute("required");
                expDetails.value = "";
            }
        }

        document.addEventListener("DOMContentLoaded", function () {
            // Check and set visibility on reload
            const checkedRadio = document.querySelector('input[name="experience"]:checked');
            if (checkedRadio) toggleExperienceBox(checkedRadio.value);

            const dobInput = document.getElementById("dob");
            const dobMessage = document.getElementById("dob-message");

            dobInput.addEventListener("change", function () {
                const dobValue = dobInput.value;
                if (!dobValue) {
                    dobMessage.textContent = "";
                    return;
                }

                const dob = new Date(dobValue);
                const today = new Date();
                let age = today.getFullYear() - dob.getFullYear();
                const m = today.getMonth() - dob.getMonth();

                if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                    age--;
                }

                if (age < 18) {
                    dobMessage.textContent = "Your age is not applicable";
                } else {
                    dobMessage.textContent = "";
                }
            });
        });
    </script>

</head>
<body>
    <div class="container">
        <div class="navbar-center">
            <img src="tech.jpg" alt="Logo">
        </div>
        <h2>Candidate Registration</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label>Last Name:<span class="required-star">*</span></label>
                    <input type="text" name="last_name" pattern="[A-Za-z]+" placeholder="Enter Last Name" required>
                </div>
                <div class="form-group">
                    <label>First Name:<span class="required-star">*</span></label>
                    <input type="text" name="first_name" pattern="[A-Za-z]+" placeholder="Enter First Name" required>
                </div>
                <div class="form-group">
                    <label>Middle Name:<span class="required-star">*</span></label>
                    <input type="text" name="middle_name" pattern="[A-Za-z]+" placeholder="Enter Middle Name" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Email:<span class="required-star">*</span></label>
                    <input type="email" name="email" placeholder="Enter Email" required>
                </div>
                <div class="form-group">
                    <label>Password:<span class="required-star">*</span></label>
                    <input type="password" name="password" placeholder="Enter Password" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Contact:<span class="required-star">*</span></label>
                    <input type="text" name="contact" pattern="[0-9]{10}" maxlength="10" placeholder="Enter 10 digit number" required>
                </div>
                <div class="form-group">
                    <label>Gender:</label>
                    <div class="radio-group">
                        <label><input type="radio" name="gender" value="Male" required> Male</label>
                        <label><input type="radio" name="gender" value="Female"> Female</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Qualification:<span class="required-star">*</span></label>
                <select name="qualification" required>
                    <option value="">--Select--</option>
                    <option value="12th">12th</option>
                    <option value="Diploma">Diploma</option>
                    <option value="Graduation">Graduation</option>
                    <option value="Post Graduation">Post Graduation</option>
                    <option value="PhD">PhD</option>
                </select>
            </div>

           <div class="form-group">
                <label>Date of Birth:<span class="required-star">*</span></label>
                <input type="date" name="dob" id="dob" required>
                <div id="dob-message" style="color:red; font-weight:400; margin-top:5px;"></div>
           </div>

            <div class="form-group">
                <label>Upload Photo:<span class="required-star">*</span></label>
                <input type="file" name="photo" accept=".jpg,.jpeg,.png" required>
            </div>

            <div class="form-group">
                <label>Upload Resume:<span class="required-star">*</span></label>
                <input type="file" name="resume" accept=".pdf,.doc,.docx" required>
            </div>

            <div class="form-group">
                <label>Have any Experience?</label>
                <div class="radio-group">
                    <label><input type="radio" name="experience" value="Yes" onclick="toggleExperienceBox(this.value)" required> Yes</label>
                    <label><input type="radio" name="experience" value="No" onclick="toggleExperienceBox(this.value)" required> No</label>
                </div>
            </div>

            <div id="experience-box" class="experience-box">
                <label>Experience Details:<span class="required-star">*</span></label>
                <textarea name="experience_details" id="experience_details" rows="4" cols="50"></textarea>
            </div>

            <button type="submit">Register</button>
            <p style="text-align:right;">Already have an account? <a href="login1.php">Login Here</a></p>
            <button style="background:none; border:none;margin-top:20px;font-weight:bold;" 
            onclick="window.location.href='index.php'" type="button">Back to Home</button>

        </form>
    </div>
</body>
</html>