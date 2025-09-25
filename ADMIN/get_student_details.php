<?php
require_once '../PHP/db.php';

$student_id = $_GET['id'] ?? '';

if (!$student_id) {
    echo json_encode(['error' => 'Student ID missing']);
    exit();
}

// Fetch all records for this student
$stmt = $conn->prepare("SELECT fullname, student_id, course, year_level, semester, subject_name, section 
                        FROM registrations WHERE student_id=?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'No records found']);
    exit();
}

$subjects = [];
$studentInfo = [];

while ($row = $result->fetch_assoc()) {
    if (empty($studentInfo)) {
        // Save student info from first row
        $studentInfo = [
            'fullname' => $row['fullname'],
            'student_id' => $row['student_id'],
            'course' => $row['course'],
            'year_level' => $row['year_level'],
            'semester' => $row['semester'],
            'subjects' => []
        ];
    }
    $studentInfo['subjects'][] = [
        'subject_name' => $row['subject_name'],
        'section' => $row['section']
    ];
}

echo json_encode($studentInfo);
