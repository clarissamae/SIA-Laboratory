<?php
session_start();

// You can optionally pass some info via session or GET
$firstname = $_SESSION['firstname'] ?? 'Student';
$lastname = $_SESSION['lastname'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>NCST Application Final Step</title>
<link rel="stylesheet" href="../CSS/OnlineApplication.css"> <!-- reuse your CSS -->
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f9f9f9;
        margin: 0;
        padding: 0;
    }

    .final-step {
        text-align: center;
        margin-top: 50px;
        padding: 20px;
    }

    .school-header img {
        width: 80px;
        height: auto;
    }

    .school-header h2 {
        margin: 10px 0 5px;
        font-size: 26px;
        color: #222;
    }

    .school-header p {
        font-size: 15px;
        color: #555;
        margin-bottom: 20px;
    }

    /* Green check animation */
    .checkmark {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: inline-block;
        stroke-width: 2;
        stroke: #fff;
        stroke-miterlimit: 10;
        margin: 20px auto;
        box-shadow: inset 0px 0px 0px #4CAF50;
        animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
        position: relative;
    }

    .checkmark__circle {
        stroke-dasharray: 166;
        stroke-dashoffset: 166;
        stroke-width: 2;
        stroke-miterlimit: 10;
        stroke: #4CAF50;
        fill: none;
        animation: stroke 0.6s cubic-bezier(.65, 0, .45, 1) forwards;
    }

    .checkmark__check {
        transform-origin: 50% 50%;
        stroke-dasharray: 48;
        stroke-dashoffset: 48;
        stroke: #fff;
        animation: stroke 0.3s cubic-bezier(.65, 0, .45, 1) .8s forwards;
    }

    @keyframes stroke {
        100% { stroke-dashoffset: 0; }
    }
    @keyframes scale {
        0%, 100% { transform: none; }
        50% { transform: scale3d(1.1, 1.1, 1); }
    }
    @keyframes fill {
        100% { box-shadow: inset 0px 0px 0px 80px #4CAF50; }
    }

    .final-step h1 {
        color: #0056b3;
        margin-bottom: 20px;
    }

    .final-step ul {
        text-align: left;
        display: inline-block;
        margin-bottom: 30px;
    }

    .final-step .btn-home {
        background: #0056b3;
        color: #fff;
        padding: 12px 28px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 18px;
    }

    .final-step .btn-home:hover {
        background: #003f7f;
    }
</style>
</head>
<body>

<div class="final-step">
    <div class="school-header">
        <img src="../IMAGES/NCST-logo.png" alt="NCST Logo">
        <h2>National College of Science and Technology</h2>
        <p>Please confirm your application details below.</p>
    </div>

    <!-- Animated checkmark -->
    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
        <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
        <path class="checkmark__check" fill="none" d="M14 27l7 7 16-16"/>
    </svg>

    <h1>Thank you, <?= htmlspecialchars($firstname . ' ' . $lastname) ?>!</h1>
    <p>Your online application has been submitted successfully.</p>
    <p>Please make sure to bring the following requirements when you visit the admission office:</p>

    <ul>
        <li>Original Transcript of Records (TOR)</li>
        <li>High School Diploma / Certificate of Graduation</li>
        <li>Birth Certificate (NSO/PSA)</li>
        <li>Valid ID (Parent/Guardian and Student)</li>
        <li>Recent 2x2 ID Photo</li>
        <li>Any other relevant documents</li>
    </ul>

    <p>Please wait for our confirmation email. Our admission staff will contact you for your scheduled appointment.</p>

    <a href="index.php" class="btn-home">Back to Home</a>
</div>

</body>
</html>
