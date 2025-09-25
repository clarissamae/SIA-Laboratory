<?php
session_start();

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "EnrollmentSystem";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $surname    = $_POST['surname'];

    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ? AND surname = ?");
    $stmt->bind_param("ss", $student_id, $surname);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $_SESSION['student_id'] = $student_id;
        $_SESSION['surname']    = $surname;
        header("Location: student_dashboard.php");
        exit;
    } else {
        $error = "Invalid Credentials. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Portal Login - NCST</title>
  <link rel="stylesheet" href="../CSS/student_portal_login.css">
</head>
<body>
  <div class="login-wrapper">
    <div class="login-card">
      <img src="../IMAGES/NCST-logo.png" alt="NCST Logo" class="logo">

      <h1>Student Portal</h1>
      <p>Please enter your Student ID and Surname to login.</p>

      <?php if($error): ?>
        <p style="color:red; font-weight:bold;"><?= $error ?></p>
      <?php endif; ?>

      <form action="" method="POST" class="login-form">
        <label for="student_id">Student ID</label>
        <input type="text" id="student_id" name="student_id" placeholder="Enter your Student ID" required>

        <label for="surname">Surname</label>
        <input type="text" id="surname" name="surname" placeholder="Enter your Surname" required>

        <button type="submit">Login</button>
      </form>
    </div>
  </div>
</body>
</html>
