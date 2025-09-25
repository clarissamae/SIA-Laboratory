
<?php
session_start();
include '../PHP/db.php'; // your DB connection file

// Make sure student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: student_portal_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student info from DB
$stmt = $conn->prepare("SELECT firstname, surname, email, student_id, course, year_level, semester FROM students WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $student = $result->fetch_assoc();
} else {
    header("Location: student_portal_login.php");
    exit();
}

// Fetch enrollment status
$statusStmt = $conn->prepare("SELECT status FROM registrations WHERE student_id = ?");
$statusStmt->bind_param("s", $student_id);
$statusStmt->execute();
$statusResult = $statusStmt->get_result();
$enrollmentStatus = ($statusResult->num_rows === 1) ? $statusResult->fetch_assoc()['status'] : 'pending';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../CSS/student_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="nav-left">
        <img src="../IMAGES/NCST-logo.png" alt="NCST Logo" class="logo">
        <span class="school-name">NCST Student Portal</span>
    </div>
    <div class="nav-right">
        <a href="student_profile.php" class="profile-text">Profile</a>
        <a href="../PHP/logout.php" class="btn-logout">Logout</a>
    </div>
</nav>

<!-- Welcome Text outside navbar -->
<div class="welcome-container">
    <h2>Welcome, <?= htmlspecialchars($student['firstname'] . ' ' . $student['surname']) ?>!</h2>
</div>

<!-- Main Grid Container -->
<div class="grid-container">
    <!-- Top Row: View Section and View Registration -->
    <div class="grid-row">
        <!-- View Section Card -->
        <div class="grid-card">
            <h2>Section Selection</h2>
            <p>Click below to view available sections for your course, year, and semester.</p>
            <form action="select_section.php" method="GET">
                <input type="hidden" name="course" value="<?= htmlspecialchars($student['course']) ?>">
                <input type="hidden" name="year_level" value="<?= htmlspecialchars($student['year_level']) ?>">
                <input type="hidden" name="semester" value="<?= htmlspecialchars($student['semester']) ?>">
                <button 
                    type="submit" 
                    class="btn-action view-btn"
                    <?= ($enrollmentStatus === 'approved') ? 'disabled title="Your enrollment is already approved."' : '' ?>
                >
                    <i class="fas fa-eye"></i> View Sections
                </button>
            </form>
            
            <?php if($enrollmentStatus === 'approved'): ?>
                <div class="notification success">
                    Your registration has been <strong>approved</strong>. Section selection is now locked.
                    <span class="close-notif">&times;</span>
                </div>
            <?php endif; ?>
        </div>

        <!-- View Registration Card -->
        <div class="grid-card">
            <h2>Registration Viewing</h2>
            <p>View your submitted registration information:</p>
            <form action="view_registration.php" method="GET" style="display:inline;">
                <button 
                    type="submit" 
                    class="btn-action view-btn"
                >
                    <i class="fas fa-eye"></i> View Registration
                </button>
            </form>
        </div>
    </div>

    <!-- Bottom Row: View Receipt (centered) -->
    <div class="grid-row">
        <div class="grid-card center-card">
            <h2>Receipt Viewing</h2>
            <p>View your payment/enrollment receipt:</p>
            <form action="view_receipt.php" method="GET" style="display:inline;">
                <button 
                    type="submit" 
                    class="btn-action view-btn"
                >
                    <i class="fas fa-receipt"></i> View Receipt
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.close-notif').forEach(btn => {
    btn.addEventListener('click', () => {
        btn.parentElement.style.display = 'none';
    });
});
</script>

</body>
</html>