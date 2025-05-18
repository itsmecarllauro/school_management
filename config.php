<?php
// Database configuration
define('DB_HOST', 'sql311.infinityfree.com');
define('DB_NAME', 'if0_38791448_school_management');
define('DB_USER', 'if0_38791448');
define('DB_PASS', '230fJaKloYb');

// SMTP configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_AUTH', true);
define('SMTP_USERNAME', 'carllauro08@gmail.com');
define('SMTP_PASSWORD', 'bdxf kjdc lnfe hmzw'); // Consider moving to environment variable for security
define('SMTP_SECURE', 'ssl');
define('SMTP_PORT', 465);

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>
