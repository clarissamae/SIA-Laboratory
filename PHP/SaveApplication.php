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

// Collect form data
$firstname     = $_POST['firstname'] ?? '';
$middlename    = $_POST['middlename'] ?? '';
$lastname      = $_POST['lastname'] ?? '';
$suffix        = $_POST['suffix'] ?? '';
$student_type  = $_POST['student_type'] ?? '';
$course        = $_POST['course'] ?? '';
$year_level    = $_POST['year_level'] ?? '';
$semester      = $_POST['semester'] ?? '';
$gender        = $_POST['gender'] ?? '';
$civil_status  = $_POST['civil_status'] ?? '';
$nationality   = $_POST['nationality'] ?? '';
$dob           = $_POST['dob'] ?? '';
$pob           = $_POST['pob'] ?? '';
$mobile_number = $_POST['mobile_number'] ?? '';
$email         = $_POST['email'] ?? '';
$religion      = $_POST['religion'] ?? '';
$place         = $_POST['place'] ?? '';

// Educational background
$education = json_encode([
    'primary_school'   => $_POST['primary_school'] ?? '',
    'primary_grad'     => $_POST['primary_grad'] ?? '',
    'secondary_school' => $_POST['secondary_school'] ?? '',
    'secondary_grad'   => $_POST['secondary_grad'] ?? '',
    'strand'           => $_POST['strand'] ?? '',
    'achievement'      => $_POST['achievement'] ?? ''
]);

// Father info
$father_info = json_encode([
    'lname'      => $_POST['father_lname'] ?? '',
    'fname'      => $_POST['father_fname'] ?? '',
    'mname'      => $_POST['father_mname'] ?? '',
    'address'    => $_POST['father_address'] ?? '',
    'mobile'     => $_POST['father_mobile'] ?? '',
    'occupation' => $_POST['father_occupation'] ?? ''
]);

// Mother info
$mother_info = json_encode([
    'lname'      => $_POST['mother_lname'] ?? '',
    'fname'      => $_POST['mother_fname'] ?? '',
    'mname'      => $_POST['mother_mname'] ?? '',
    'address'    => $_POST['mother_address'] ?? '',
    'mobile'     => $_POST['mother_mobile'] ?? '',
    'occupation' => $_POST['mother_occupation'] ?? ''
]);

// Guardian info
$guardian_info = json_encode([
    'lname'        => $_POST['guardian_lname'] ?? '',
    'fname'        => $_POST['guardian_fname'] ?? '',
    'mname'        => $_POST['guardian_mname'] ?? '',
    'address'      => $_POST['guardian_address'] ?? '',
    'mobile'       => $_POST['guardian_mobile'] ?? '',
    'occupation'   => $_POST['guardian_occupation'] ?? '',
    'relationship' => $_POST['guardian_relationship'] ?? ''
]);

// Insert
$stmt = $conn->prepare("INSERT INTO OnlineApplication (
    firstname, middlename, lastname, suffix, student_type,
    course, year_level, semester, gender, civil_status, nationality,
    dob, pob, mobile_number, email, religion, place,
    education, father_info, mother_info, guardian_info
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
    "sssssssssssssssssssss",
    $firstname, $middlename, $lastname, $suffix, $student_type,
    $course, $year_level, $semester, $gender, $civil_status, $nationality,
    $dob, $pob, $mobile_number, $email, $religion, $place,
    $education, $father_info, $mother_info, $guardian_info
);

if ($stmt->execute()) {
    header("Location: FinalStep.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}
?>
