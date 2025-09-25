<?php
session_start();
include '../PHP/db.php'; // database connection

// Make sure student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: student_portal_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Check if subjects were selected
if (!isset($_POST['subjects']) || empty($_POST['subjects'])) {
    $_SESSION['error'] = "No subjects selected!";
    header("Location: select_section.php");
    exit();
}

$selected_subjects = $_POST['subjects']; // array of "sectionId|subject|day|start|end"

// Insert each selected subject into a table, e.g., `registrations`
foreach ($selected_subjects as $subj) {
    list($section_id, $subject_name, $day, $start, $end) = explode('|', $subj);

    // Insert into registration table (assuming you have table `registrations`)
    $stmt = $conn->prepare("INSERT INTO enrolled_subjects (student_id, section_id, subject_name, day, start_time, end_time, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $status = "Pending";
    $stmt->bind_param("iisssss", $student_id, $section_id, $subject_name, $day, $start, $end, $status);
    $stmt->execute();
}

// After inserting, redirect to registration_form.php to display the registration
header("Location: registration_form.php");
exit();
?>
