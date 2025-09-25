<?php
session_start();
require_once '../PHP/db.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $application_id = $_POST['application_id'] ?? null;
    $status         = $_POST['status'] ?? null;

    if (empty($application_id) || empty($status)) {
        die("Invalid request: " . print_r($_POST, true));
    }

    // 1️⃣ Update OnlineApplication
    $stmt = $conn->prepare("UPDATE OnlineApplication SET status = ? WHERE application_id = ?");
    $stmt->bind_param("ss", $status, $application_id);
    $stmt->execute();
    $stmt->close();

    // 2️⃣ If accepted → insert into Students
    $student_id = null; // default in case rejected
    if ($status === "Accepted") {
        $res = $conn->prepare("SELECT firstname, lastname, course, year_level, email, semester, created_at 
                               FROM OnlineApplication WHERE application_id = ?");
        $res->bind_param("s", $application_id);
        $res->execute();
        $applicant = $res->get_result()->fetch_assoc();
        $res->close();

        if ($applicant) {
            // Generate Student ID: YYYY-00001
            $year = date("Y");
            $check = $conn->prepare("SELECT student_id FROM Students WHERE student_id LIKE ? ORDER BY student_id DESC LIMIT 1");
            $like = $year . "-%";
            $check->bind_param("s", $like);
            $check->execute();
            $result = $check->get_result();
            $lastId = $result->fetch_assoc()['student_id'] ?? null;
            $check->close();

            $newNum = $lastId ? str_pad(intval(substr($lastId, 5)) + 1, 5, "0", STR_PAD_LEFT) : "00001";
            $student_id = $year . "-" . $newNum;

            $insert = $conn->prepare("INSERT INTO Students 
                (student_id, surname, firstname, course, year_level, email, semester, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $insert->bind_param(
                "ssssssss",
                $student_id,
                $applicant['lastname'],
                $applicant['firstname'],
                $applicant['course'],
                $applicant['year_level'],
                $applicant['email'],
                $applicant['semester'],
                $applicant['created_at']
            );
            $insert->execute();
            $insert->close();
        }
    } else {
        // If rejected, just fetch email and name
        $res = $conn->prepare("SELECT firstname, lastname, email FROM OnlineApplication WHERE application_id = ?");
        $res->bind_param("s", $application_id);
        $res->execute();
        $applicant = $res->get_result()->fetch_assoc();
        $res->close();
    }

    // 3️⃣ Send Email via PHPMailer
    if ($applicant) {
        $mail = new PHPMailer(true);
        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';         // Replace with your SMTP host
            $mail->SMTPAuth   = true;
            $mail->Username   = 'clarissamae537@gmail.com';    // Your SMTP email
            $mail->Password   = 'rupy qoqm lzpu ukka';      // Your SMTP password or app password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('yourgmail@gmail.com', 'NCST Admissions');
            $mail->addAddress($applicant['email'], $applicant['firstname'] . ' ' . $applicant['lastname']);

            $mail->isHTML(true);
            $mail->Subject = 'Admission Application Status';

            if ($status === "Accepted") {
                $mail->Body = "
                    <h3>Congratulations!</h3>
                    <p>Dear {$applicant['firstname']} {$applicant['lastname']},</p>
                    <p>Your application has been <strong>Accepted</strong>.</p>
                    <p>Your Student ID is: <strong>{$student_id}</strong></p>
                    <p>Please login to the <a href='http://localhost/enrollment/PHP/student_portal_login.php'>Student Portal</a> to proceed</p>
                    <br>
                    <p>Thank you,<br>NCST Admissions</p>
                ";
            } else {
                $mail->Body = "
                    <h3>Application Update</h3>
                    <p>Dear {$applicant['firstname']} {$applicant['lastname']},</p>
                    <p>We regret to inform you that your application has been <strong>Rejected</strong>.</p>
                    <br>
                    <p>Thank you for applying,<br>NCST Admissions</p>
                ";
            }

            $mail->send();
        } catch (Exception $e) {
            error_log("Mailer Error: " . $mail->ErrorInfo);
            // optionally, show error message: echo "Email not sent: {$mail->ErrorInfo}";
        }
    }

    // 4️⃣ Redirect back with SweetAlert trigger
    header("Location: admission_request.php?status_updated=1");
    exit();

} else {
    die("Invalid request");
}
?>
