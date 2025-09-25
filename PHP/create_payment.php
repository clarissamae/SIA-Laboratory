<?php
session_start();
include '../PHP/db.php';

if(!isset($_SESSION['student_id'])){
    header("Location: student_login.php");
    exit();
}

// PayMongo Secret Key
$secretKey = "xx_xxxx_xxxxxxxxxxxxxxx"; 
$authHeader = "Basic " . base64_encode($secretKey . ":");

// Get form input
$student_id = $_SESSION['student_id'];
$amount     = floatval($_POST['amount'] ?? 0);
$method     = $_POST['method'] ?? '';
$name       = $_POST['name'] ?? '';
$email      = $_POST['email'] ?? '';
$phone      = $_POST['phone'] ?? '';

if(!$amount || !$method){
    die("Payment amount or method missing.");
}

// normalize method to match DB enum values
// if your UI uses 'cash' change it to 'full'
if (strtolower($method) === 'cash') {
    $method = 'full';
} else {
    $method = strtolower($method); // 'installment' or 'full'
}

// Fetch latest registration
$stmt = $conn->prepare("SELECT * FROM registrations WHERE student_id=? ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$registration = $result ? $result->fetch_assoc() : null;
$stmt->close();

if(!$registration || $registration['status'] !== 'approved'){
    die("No approved registration found.");
}

// Convert to centavos for PayMongo requirement
$amountCents = intval(round($amount * 100, 0));

// Build redirect URLs (include student & registration info in success URL)
// IMPORTANT: urlencode to avoid issues with special characters
$baseUrl = "http://localhost/enrollment/PHP";
$successUrl = $baseUrl . "/payment_success.php?"
    . "sid=" . urlencode($student_id)
    . "&rid=" . urlencode($registration['id'])
    . "&method=" . urlencode($method)
    . "&amount=" . urlencode($amount)
    . "&session_id={CHECKOUT_SESSION_ID}";

$cancelUrl  = $baseUrl . "/view_registration.php";

// Prepare checkout session payload for PayMongo (checkout_sessions)
$payload = [
    "data" => [
        "attributes" => [
            "line_items" => [[
                "currency" => "PHP",
                "amount"   => $amountCents,
                "name"     => "Enrollment Payment ({$method})",
                "quantity" => 1
            ]],
            "payment_method_types" => ["card","gcash","paymaya"],
            "customer" => [
                "name"  => $name,
                "email" => $email,
                "phone" => $phone
            ],
            "success_url" => $successUrl,
            "cancel_url"  => $cancelUrl
        ]
    ]
];

// Create checkout session
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.paymongo.com/v1/checkout_sessions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: $authHeader",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$checkout = json_decode($response, true);

// Redirect to PayMongo checkout
if(isset($checkout['data']['attributes']['checkout_url'])){
    header("Location: " . $checkout['data']['attributes']['checkout_url']);
    exit;
} else {
    // helpful debug output when creation fails
    header('Content-Type: text/plain; charset=utf-8');
    echo "Error creating checkout session (HTTP $httpCode)\n\n";
    echo "Response from PayMongo:\n";
    echo $response;
    exit;
}
