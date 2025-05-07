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

// Dummy send_verification_email function to avoid errors until PHPMailer is set up
function send_verification_email($to_email, $to_name, $verification_code) {
    // TODO: Implement email sending with PHPMailer after installation
    // For now, just return true to simulate success
    return true;
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
?>
