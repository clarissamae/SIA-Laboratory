<?php
// payment_success.php
session_start();
include '../PHP/db.php'; // adjust path if needed

// Get GET parameters from PayMongo redirect
$student_id = $_GET['sid'] ?? null;
$method     = $_GET['method'] ?? 'full';
$amount_raw = $_GET['amount'] ?? 0;
$checkout_id = $_GET['session_id'] ?? uniqid("txn_");
$transaction_id = $_GET['checkout_id'] ?? uniqid("txn_", true);


// normalize amount
$amount = floatval($amount_raw);

// If registration_id missing, get latest registration for this student
$registration_id = null;
if ($student_id) {
    $stmt = $conn->prepare("SELECT * FROM registrations WHERE student_id=? ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $row = $res->fetch_assoc()) {
        // Use a unique field to identify registration (e.g., the primary key or student_id + created_at)
        $registration_id = $row['student_id']; // or another unique field
    }
}

// Validate required data
if (!$student_id || !$registration_id || $amount <= 0) {
    echo "<h3>❌ Missing or invalid payment details</h3>";
    echo "<pre>" . htmlspecialchars(print_r($_GET, true)) . "</pre>";
    exit;
}

// Insert payment into database
$payment_date = date('Y-m-d H:i:s');
$insert = $conn->prepare(
    "INSERT INTO payments (student_id, registration_id, method, amount, transaction_id, payment_date) 
     VALUES (?, ?, ?, ?, ?, ?)"
);
if (!$insert) {
    die("Prepare failed: " . $conn->error);
}
$insert->bind_param("sisdss", $student_id, $registration_id, $method, $amount, $checkout_id, $payment_date);

if ($insert->execute()) {
    echo "<h2>✅ Payment Successful!</h2>";
    echo "<p>Student ID: " . htmlspecialchars($student_id) . "</p>";
    echo "<p>Amount Paid: ₱" . number_format($amount, 2) . "</p>";
    echo "<p>Payment Method: " . htmlspecialchars($method) . "</p>";
    echo "<p>Transaction ID: " . htmlspecialchars($checkout_id) . "</p>";
} else {
    echo "<h2>❌ Failed to record payment</h2>";
    echo "<p>MySQL error: " . htmlspecialchars($insert->error) . "</p>";
    echo "<pre>" . htmlspecialchars(print_r($_GET, true)) . "</pre>";
}
// Check if transaction already exists
$stmtCheck = $conn->prepare("SELECT * FROM payments WHERE transaction_id=?");
$stmtCheck->bind_param("s", $transaction_id);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if($resultCheck->num_rows > 0){
    echo "<h3>❌ Payment already recorded.</h3>";
    exit();
}


// Redirect to view_registration after 3 seconds
header("refresh:3; url=view_registration.php");
exit;
