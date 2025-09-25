<?php
session_start();

// DB connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "EnrollmentSystem";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // use password_hash in production

    $sql = "SELECT * FROM admin_users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $_SESSION['admin'] = $username;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background-color: #ffffff; /* white main background */
    font-family: 'Segoe UI', sans-serif;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.login-card {
    background: #f9f9f9; /* light gray card for contrast */
    border-radius: 12px;
    box-shadow: 0px 6px 18px rgba(0,0,0,0.15);
    width: 400px;
    overflow: hidden;
}

.login-header {
    background-color: #1E3A8A; /* dark blue header */
    color: #FFD60A; /* yellow text */
    padding: 20px;
    text-align: center;
}

.login-header img {
    width: 80px;
    margin-bottom: 10px;
}

.login-header h4 {
    margin: 0;
    font-weight: 700;
}

.card-body {
    padding: 30px 25px;
}

.form-label {
    font-weight: 600;
    color: #1E3A8A;
}

.btn-login {
    background-color: #2563EB; /* primary blue button */
    border: none;
    font-weight: 600;
    color: white;
    transition: 0.3s;
}

.btn-login:hover {
    background-color: #1E40AF; /* darker blue on hover */
}
.alert-danger {
    font-size: 14px;
    padding: 8px 12px;
    margin-bottom: 15px;
}
</style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <img src="../IMAGES/NCST-logo.png" alt="NCST Logo">
        <h4>Admin Login</h4>
    </div>
    <div class="card-body">
        <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Enter username" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter password" required>
            </div>
            <button type="submit" class="btn btn-login w-100">Login</button>
        </form>
    </div>
</div>

</body>
</html>
