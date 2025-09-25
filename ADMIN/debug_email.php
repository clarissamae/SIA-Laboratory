<?php
// debug_email.php
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

echo "<h3>Debugging Gmail SMTP Connection</h3>";
echo "<p>Trying to connect to Gmail SMTP server...</p>";

$mail = new PHPMailer(true);

// Enable very verbose debugging
$mail->SMTPDebug = SMTP::DEBUG_CONNECTION; // Shows all communication with the server
$mail->Debugoutput = function($str, $level) {
    echo "<pre style='background: #f0f0f0; padding: 10px; border-left: 4px solid #0073aa;'>[$level] " . htmlspecialchars($str) . "</pre>";
};

try {
    // Test different configurations one by one
    
    echo "<h4>Trying Configuration 1: TLS on port 587</h4>";
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'navanes.clarissamae@ncst.edu.ph';
    $mail->Password   = 'yourpassword'; // PUT YOUR REAL PASSWORD HERE
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    
    // Additional settings
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    
    // Recipients
    $mail->setFrom('admissions@ncst.edu.ph', 'NCST Test');
    $mail->addAddress('navanes.clarissamae@ncst.edu.ph', 'Your Name');
    
    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email from NCST System';
    $mail->Body    = 'This is a test email to confirm PHPMailer is working.';
    
    if ($mail->send()) {
        echo '<p style="color: green; font-weight: bold;">SUCCESS: Email sent successfully with Configuration 1!</p>';
        exit;
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Configuration 1 failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Error details: " . htmlspecialchars($mail->ErrorInfo) . "</p>";
}

// If first configuration failed, try a different one
try {
    echo "<h4>Trying Configuration 2: SSL on port 465</h4>";
    
    // Reset PHPMailer
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = SMTP::DEBUG_CONNECTION;
    $mail->Debugoutput = function($str, $level) {
        echo "<pre style='background: #f0f0f0; padding: 10px; border-left: 4px solid #0073aa;'>[$level] " . htmlspecialchars($str) . "</pre>";
    };
    
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'navanes.clarissamae@ncst.edu.ph';
    $mail->Password   = 'yourpassword'; // PUT YOUR REAL PASSWORD HERE
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;
    
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    
    $mail->setFrom('admissions@ncst.edu.ph', 'NCST Test');
    $mail->addAddress('navanes.clarissamae@ncst.edu.ph', 'Your Name');
    $mail->isHTML(true);
    $mail->Subject = 'Test Email from NCST System';
    $mail->Body    = 'This is a test email to confirm PHPMailer is working.';
    
    if ($mail->send()) {
        echo '<p style="color: green; font-weight: bold;">SUCCESS: Email sent successfully with Configuration 2!</p>';
        exit;
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Configuration 2 failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Error details: " . htmlspecialchars($mail->ErrorInfo) . "</p>";
}

// Final fallback - test basic connection without auth
try {
    echo "<h4>Testing basic connection (without authentication)</h4>";
    
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = SMTP::DEBUG_CONNECTION;
    $mail->Debugoutput = function($str, $level) {
        echo "<pre style='background: #f0f0f0; padding: 10px; border-left: 4px solid #0073aa;'>[$level] " . htmlspecialchars($str) . "</pre>";
    };
    
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = false; // No authentication
    $mail->Port = 587;
    
    // Just try to connect without sending
    if ($mail->smtpConnect()) {
        echo '<p style="color: green;">Basic connection to Gmail server successful!</p>';
        $mail->smtpClose();
    } else {
        echo '<p style="color: red;">Cannot even connect to Gmail server</p>';
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Basic connection test failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h4>Next Steps:</h4>";
echo "<ol>";
echo "<li>Make sure you've enabled 'Less secure app access' at: <a href='https://myaccount.google.com/lesssecureapps' target='_blank'>https://myaccount.google.com/lesssecureapps</a></li>";
echo "<li>If you have 2FA enabled, generate an App Password at: <a href='https://myaccount.google.com/apppasswords' target='_blank'>https://myaccount.google.com/apppasswords</a></li>";
echo "<li>Check if your network/firewall is blocking outgoing SMTP connections (ports 587 and 465)</li>";
echo "<li>Try using a different email provider or your web host's SMTP server</li>";
echo "</ol>";
?>