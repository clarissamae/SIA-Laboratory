<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // adjust if needed

$mail = new PHPMailer(true);

try {
    // Debug output
    $mail->SMTPDebug = 3; // 2 = client/server, 3 = connection issues
    $mail->Debugoutput = function($str, $level) {
        echo "<pre>[$level] $str</pre>";
    };

    // SMTP settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'clarissamae537@gmail.com'; // your Gmail/GSuite email
    $mail->Password = 'rupy qoqm lzpu ukka'; // use App Password, not normal password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // STARTTLS for port 587
    $mail->Port = 587;

    // SSL/TLS options
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];

    // Sender/recipient
    $mail->setFrom('navanes.clarissamae@ncst.edu.ph', 'NCST Test'); 
    $mail->addAddress('clarissamae.navanes@example.com', 'Clarissa');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'PHPMailer Test';
    $mail->Body    = '<p>This is a <b>test</b> email sent using PHPMailer!</p>';
    $mail->AltBody = 'This is a test email sent using PHPMailer!';

    // Send
    $mail->send();
    echo "Message sent successfully!";
} catch (Exception $e) {
    echo "Message could not be sent. Error: {$mail->ErrorInfo}";
}
