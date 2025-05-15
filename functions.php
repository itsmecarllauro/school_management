<?php
// PHPMailer is not included in the project directory. To avoid errors, comment out the require_once lines.
// You need to install PHPMailer via Composer or manually download and place it in the project directory.

// require_once 'PHPMailer/src/Exception.php';
// require_once 'PHPMailer/src/PHPMailer.php';
// require_once 'PHPMailer/src/SMTP.php';

// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;

// Sanitize input
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Generate a random 6-digit verification code
function generate_verification_code() {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

// Use PHPMailer for sending emails
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Send verification email using PHPMailer
function send_verification_email($to_email, $to_name, $verification_code) {
    require_once 'vendor/autoload.php';

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = SMTP_AUTH;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;

        $mail->setFrom(SMTP_USERNAME, 'Contact Manager');
        $mail->addAddress($to_email, $to_name);
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification Code';
        $mail->Body = "
            <p>Hi <strong>{$to_name}</strong>,</p>
            <p>Thank you for registering. Please use the following verification code to verify your email address:</p>
            <h2>{$verification_code}</h2>
            <p>If you did not register, please ignore this email.</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

// Check if user is logged in
function is_logged_in() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['user_id']);
}

// Redirect to login if not logged in
function ensure_logged_in() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

// Get current logged in user's role
function get_user_role() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION['user_role'] ?? null;
}

// Check if current user has a specific role
function has_role($role) {
    $user_role = get_user_role();
    if (is_array($role)) {
        return in_array($user_role, $role);
    }
    return $user_role === $role;
}

// Ensure user has one of the allowed roles, otherwise redirect
function ensure_role($roles) {
    // Modified to allow all logged-in users regardless of role
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
    // Previously role check is bypassed to allow all logged-in users
    return;
}

// Log user actions for audit trail
function log_action($pdo, $user_id, $action) {
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, ip_address) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $action, $ip_address]);
}
?>
