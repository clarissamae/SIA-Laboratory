<?php
require __DIR__ . '/../vendor/autoload.php'; // path to TCPDF autoload

use TCPDF;

session_start();

// DB connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "EnrollmentSystem";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get student ID from GET parameter
$student_id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM OnlineApplication WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    die("Student not found!");
}

// Create PDF
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

// Add school logo
$pdf->Image('../IMAGES/NCST-logo.png', 15, 10, 30, '', '', '', '', false, 300);

// Add title
$pdf->Ln(20);
$pdf->Cell(0, 10, "NCST Online Application", 0, 1, 'C');
$pdf->Ln(5);

// Add student info
$pdf->Cell(0, 10, "Name: " . $student['firstname'] . " " . $student['lastname'], 0, 1);
$pdf->Cell(0, 10, "Student Type: " . $student['student_type'], 0, 1);
$pdf->Cell(0, 10, "Course: " . $student['course'], 0, 1);
$pdf->Cell(0, 10, "Year Level: " . $student['year_level'], 0, 1);
$pdf->Cell(0, 10, "Semester: " . $student['semester'], 0, 1);
$pdf->Cell(0, 10, "Email: " . $student['email'], 0, 1);
$pdf->Cell(0, 10, "Mobile: " . $student['mobile_number'], 0, 1);
$pdf->Cell(0, 10, "Address: " . $student['place'], 0, 1);

// You can loop through more fields to add everything

$pdf->Output('application_'.$student['id'].'.pdf', 'I'); // 'I' = inline view
