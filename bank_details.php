<?php
session_start();
$conn = new mysqli("localhost", "root", "", "company_db");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$employee_id = $_SESSION['employee_id']; // ensure this is set after login

// Fetch existing details
$sql = "SELECT * FROM employee_bank_details WHERE employee_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$bank = $result->fetch_assoc();

// Update details
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $bank_name = trim($_POST['bank_name']);
  $account_number = trim($_POST['account_number']);
  $ifsc_code = strtoupper(trim($_POST['ifsc_code']));
  $branch_name = trim($_POST['branch_name']);

  // 🔍 Server-side validations
  $errors = [];
  if (!preg_match("/^[0-9]{10,18}$/", $account_number)) {
    $errors[] = "Account number must be 10 to 18 digits long.";
  }
  if (!preg_match("/^[A-Z]{4}0[A-Z0-9]{6}$/", $ifsc_code)) {
    $errors[] = "Invalid IFSC code format. Example: SBIN0001234.";
  }

  if (empty($errors)) {
    if ($bank) {
      // Update existing record
      $update = $conn->prepare("UPDATE employee_bank_details SET bank_name=?, account_number=?, ifsc_code=?, branch_name=? WHERE employee_id=?");
      $update->bind_param("ssssi", $bank_name, $account_number, $ifsc_code, $branch_name, $employee_id);
      $update->execute();
      $msg = "✅ Bank details updated successfully!";
    } else {
      // Insert new record
      $insert = $conn->prepare("INSERT INTO employee_bank_details (employee_id, bank_name, account_number, ifsc_code, branch_name) VALUES (?, ?, ?, ?, ?)");
      $insert->bind_param("issss", $employee_id, $bank_name, $account_number, $ifsc_code, $branch_name);
      $insert->execute();
      $msg = "✅ Bank details added successfully!";
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Update Bank Details</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f8f9fa;
      margin: 0;
      padding: 0;
    }
    .container {
      width: 40%;
      margin: 60px auto;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 20px;
    }
    label {
      font-weight: bold;
      display: block;
      margin-top: 10px;
      color: #333;
    }
    input {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }
    button {
      width: 100%;
      padding: 10px;
      background: #28a745;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      margin-top: 20px;
      cursor: pointer;
    }
    button:hover {
      background: #218838;
    }
    .msg {
      text-align: center;
      font-weight: bold;
      margin-bottom: 15px;
      color: green;
    }
    .error {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Update Bank Details</h2>

    <?php 
    if (!empty($errors)) {
      foreach ($errors as $e) {
        echo "<div class='error'>⚠️ $e</div>";
      }
    }
    if (isset($msg)) echo "<p class='msg'>$msg</p>"; 
    ?>

    <form method="POST" onsubmit="return validateForm()">
      <label>Bank Name:</label>
      <input type="text" name="bank_name" id="bank_name" value="<?= htmlspecialchars($bank['bank_name'] ?? '') ?>" required>

      <label>Account Number:</label>
      <input type="text" name="account_number" id="account_number" value="<?= htmlspecialchars($bank['account_number'] ?? '') ?>" required>

      <label>IFSC Code:</label>
      <input type="text" name="ifsc_code" id="ifsc_code" value="<?= htmlspecialchars($bank['ifsc_code'] ?? '') ?>" required placeholder="Example: SBIN0001234">

      <label>Branch Name:</label>
      <input type="text" name="branch_name" id="branch_name" value="<?= htmlspecialchars($bank['branch_name'] ?? '') ?>" required>

      <button type="submit">Save Bank Details</button>
    </form>
  </div>

  <script>
    function validateForm() {
      const accountNumber = document.getElementById('account_number').value.trim();
      const ifsc = document.getElementById('ifsc_code').value.trim().toUpperCase();

      const accRegex = /^[0-9]{10,18}$/;
      const ifscRegex = /^[A-Z]{4}0[A-Z0-9]{6}$/;

      if (!accRegex.test(accountNumber)) {
        alert("❌ Account number must be 10 to 18 digits long.");
        return false;
      }

      if (!ifscRegex.test(ifsc)) {
        alert("❌ Invalid IFSC code format. Example: SBIN0001234");
        return false;
      }

      return true;
    }
  </script>
</body>
</html>
