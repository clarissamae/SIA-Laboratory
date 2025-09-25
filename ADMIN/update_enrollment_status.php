<?php
session_start();
include '../PHP/db.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Get parameters
$student_id = $_GET['student_id'] ?? '';
$action = $_GET['action'] ?? '';

if (!$student_id || !in_array($action, ['approve', 'reject'])) {
    die("Invalid request.");
}

// Map action to status
$status = $action === 'approve' ? 'approved' : 'rejected';

// Update all subjects of the student
$stmt = $conn->prepare("UPDATE registrations SET status=? WHERE student_id=? AND status='pending'");
$stmt->bind_param("ss", $status, $student_id);
if ($stmt->execute()) {
    // Redirect back to enrollment requests page with success
    header("Location: enrollment_request.php?success=1");
    exit();
} else {
    die("Failed to update enrollment requests.");
}
