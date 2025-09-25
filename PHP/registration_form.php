<?php
session_start();
include '../PHP/db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student info
$stmt = $conn->prepare("SELECT firstname, surname, course, year_level, semester FROM students WHERE student_id=?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$fullname = $student['firstname'] . ' ' . $student['surname'];

// Handle form submission (insert or update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subjects']) && count($_POST['subjects']) > 0) {
    $subjectsArr = [];
    foreach($_POST['subjects'] as $s) {
        list($section_name, $subject_name, $day, $start_time, $end_time, $units) = explode('|', $s);
        $subjectsArr[] = [
            'section_name' => $section_name,
            'subject_name' => $subject_name,
            'day' => $day,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'units' => (int)$units
        ];
    }

    $subjectsJson = json_encode($subjectsArr);

    // Check if student already has a registration
    $checkStmt = $conn->prepare("SELECT student_id FROM registrations WHERE student_id=?");
    $checkStmt->bind_param("s", $student_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // Update existing registration and set status to pending
        $updateStmt = $conn->prepare("UPDATE registrations 
            SET fullname=?, course=?, year_level=?, semester=?, subjects=?, status='pending', created_at=NOW()
            WHERE student_id=?");
        $updateStmt->bind_param("ssssss", $fullname, $student['course'], $student['year_level'], $student['semester'], $subjectsJson, $student_id);
        $updateStmt->execute();
    } else {
        // Insert new registration with status pending
        $insertStmt = $conn->prepare("INSERT INTO registrations 
            (student_id, fullname, course, year_level, semester, subjects, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
        $insertStmt->bind_param("ssssss", $student_id, $fullname, $student['course'], $student['year_level'], $student['semester'], $subjectsJson);
        $insertStmt->execute();
    }

    // Optional: notify student registration is pending
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        Swal.fire({
            icon: 'info',
            title: 'Registration Submitted',
            text: 'Your registration is pending admin approval.',
            timer: 3000,
            showConfirmButton: false
        }).then(() => { window.location='student_dashboard.php'; });
    </script>";
    exit();
}

// Fetch the latest registration
$regStmt = $conn->prepare("SELECT subjects, status FROM registrations WHERE student_id=? ORDER BY created_at DESC LIMIT 1");
$regStmt->bind_param("s", $student_id);
$regStmt->execute();
$regResult = $regStmt->get_result();

if ($regResult->num_rows === 0) {
    echo "<p>No registration record found. Please select subjects first.</p>";
    exit();
}

$row = $regResult->fetch_assoc();
$subjects = json_decode($row['subjects'], true);
$status = $row['status'];
$total_units = 0;
foreach($subjects as $s) {
    $total_units += (int)$s['units'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Form</title>
    <style>
        /* Your previous design styles here */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); padding: 20px; min-height: 100vh; margin: 0; }
        .container { max-width: 900px; margin: 20px auto; background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); position: relative; }
        h2 { text-align: center; color: #2c3e50; margin-bottom: 25px; font-size: 28px; font-weight: 600; position: relative; padding-bottom: 15px; }
        h2:after { content: ''; position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 100px; height: 4px; background: linear-gradient(to right, #3498db, #2ecc71); border-radius: 2px; }
        .student-info { margin-bottom: 30px; padding: 20px; background: linear-gradient(to right, #f8f9fa, #e9ecef); border-radius: 10px; border-left: 5px solid #3498db; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .student-info p { margin: 8px 0; font-size: 16px; color: #555; display: flex; }
        .student-info strong { min-width: 120px; color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-radius: 10px; overflow: hidden; }
        th, td { border: 1px solid #e0e0e0; padding: 12px 15px; text-align: center; }
        th { background: linear-gradient(to right, #3498db, #2980b9); color: #fff; font-weight: 600; font-size: 16px; }
        td { background-color: #fafafa; transition: background-color 0.3s; }
        tr:hover td { background-color: #f0f7ff; }
        tr:last-child td { font-weight: bold; background: linear-gradient(to right, #e3f2fd, #bbdefb); font-size: 17px; }
        .back-btn { display: inline-block; margin-top: 25px; padding: 10px 25px; background: linear-gradient(to right, #6c757d, #495057); color: white; text-decoration: none; border-radius: 30px; font-weight: 500; transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .back-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 8px rgba(0,0,0,0.15); background: linear-gradient(to right, #5a6268, #3d4348); }
        .status-badge { display: inline-block; padding: 5px 15px; background-color: #2ecc71; color: white; border-radius: 20px; font-size: 14px; font-weight: 500; margin-left: 10px; }
        .total-units { font-size: 18px; color: #2c3e50; }
    </style>
</head>
<body>
<div class="container">
    <div class="header-section">
        <div class="logo">EduReg</div>
        <div>
            <span>Registration Status:</span>
            <span class="status-badge"><?= ucfirst(htmlspecialchars($status)) ?></span>
        </div>
    </div>

    <h2>School Registration Form</h2>

    <div class="student-info">
        <p><strong>Full Name:</strong> <?= htmlspecialchars($fullname) ?></p>
        <p><strong>Student ID:</strong> <?= htmlspecialchars($student_id) ?></p>
        <p><strong>Course:</strong> <?= htmlspecialchars($student['course']) ?></p>
        <p><strong>Year Level:</strong> <?= htmlspecialchars($student['year_level']) ?></p>
        <p><strong>Semester:</strong> <?= htmlspecialchars($student['semester']) ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Section</th>
                <th>Subject</th>
                <th>Units</th>
                <th>Day</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($subjects as $subj): ?>
                <tr>
                    <td><?= htmlspecialchars($subj['section_name']) ?></td>
                    <td><?= htmlspecialchars($subj['subject_name']) ?></td>
                    <td><?= htmlspecialchars($subj['units']) ?></td>
                    <td><?= htmlspecialchars($subj['day']) ?></td>
                    <td><?= htmlspecialchars($subj['start_time'] . ' - ' . $subj['end_time']) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="2" style="text-align:right;"><strong>Total Units:</strong></td>
                <td class="total-units" colspan="3"><?= $total_units ?></td>
            </tr>
        </tbody>
    </table>

    <a href="javascript:history.back()" class="back-btn">← Back to Previous Page</a>
</div>
</body>
</html>
