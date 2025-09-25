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

// 🔹 Function to generate unique Application ID
function generateApplicationId() {
    $part1 = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    $part2 = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
    return "APP-$part1-$part2";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate new application ID
    $application_id = generateApplicationId();

    // Get all POST inputs
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
    $mobile        = $_POST['mobile'] ?? '';
    $email         = $_POST['email'] ?? '';
    $religion      = $_POST['religion'] ?? '';
    $place         = ($_POST['address'] ?? '') . ', ' . ($_POST['barangay'] ?? '') . ', ' . ($_POST['city'] ?? '') . ', ' . ($_POST['province'] ?? '') . ', ' . ($_POST['region'] ?? '') . ', ' . ($_POST['zipcode'] ?? '');

    // Store JSON fields
    $education = json_encode([
        'primary_school'   => $_POST['primary_school'] ?? '',
        'primary_grad'     => $_POST['primary_grad'] ?? '',
        'secondary_school' => $_POST['secondary_school'] ?? '',
        'secondary_grad'   => $_POST['secondary_grad'] ?? '',
        'strand'           => $_POST['strand'] ?? '',
        'achievement'      => $_POST['achievement'] ?? ''
    ]);

    $father_info = json_encode([
        'lname'      => $_POST['father_lname'] ?? '',
        'fname'      => $_POST['father_fname'] ?? '',
        'mname'      => $_POST['father_mname'] ?? '',
        'address'    => $_POST['father_address'] ?? '',
        'mobile'     => $_POST['father_mobile'] ?? '',
        'occupation' => $_POST['father_occupation'] ?? ''
    ]);

    $mother_info = json_encode([
        'lname'      => $_POST['mother_lname'] ?? '',
        'fname'      => $_POST['mother_fname'] ?? '',
        'mname'      => $_POST['mother_mname'] ?? '',
        'address'    => $_POST['mother_address'] ?? '',
        'mobile'     => $_POST['mother_mobile'] ?? '',
        'occupation' => $_POST['mother_occupation'] ?? ''
    ]);

    $guardian_info = json_encode([
        'lname'        => $_POST['guardian_lname'] ?? '',
        'fname'        => $_POST['guardian_fname'] ?? '',
        'mname'        => $_POST['guardian_mname'] ?? '',
        'address'      => $_POST['guardian_address'] ?? '',
        'mobile'       => $_POST['guardian_mobile'] ?? '',
        'occupation'   => $_POST['guardian_occupation'] ?? '',
        'relationship' => $_POST['guardian_relationship'] ?? ''
    ]);

    // ✅ Define default status
    $status = "pending";

    $stmt = $conn->prepare("
        INSERT INTO OnlineApplication (
            application_id, firstname, middlename, lastname, suffix, student_type,
            course, year_level, semester, gender, civil_status, nationality,
            dob, pob, mobile_number, email, religion, place,
            education, father_info, mother_info, guardian_info, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "sssssssssssssssssssssss",
        $application_id, $firstname, $middlename, $lastname, $suffix, $student_type,
        $course, $year_level, $semester, $gender, $civil_status, $nationality,
        $dob, $pob, $mobile, $email, $religion, $place,
        $education, $father_info, $mother_info, $guardian_info, $status
    );

    if ($stmt->execute()) {
        // ✅ Redirect with application_id
        header("Location: FinalStep.php?id=$application_id");
        exit();
    } else {
        die("Error inserting application: " . $stmt->error);
    }
} else {
    die("Invalid request");
}
?>
