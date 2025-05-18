<?php
require 'config.php';
require 'functions.php';

session_start();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = ? AND email_verified = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $errors[] = 'No verified account found with that email.';
        } else {
            // Generate 6-digit numeric code and expiry
            $code = random_int(100000, 999999);
            $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Store code and expiry in password_resets table (create if not exists)
            $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE token = VALUES(token), expires_at = VALUES(expires_at)");
            $stmt->execute([$user['id'], $code, $expires_at]);

            // Set session for verification context and email
            $_SESSION['verification_context'] = 'forgot_password';
            $_SESSION['email'] = $email;

            // Send code email
            $subject = 'Password Reset Verification Code';
            $body = "
                <p>Hi " . htmlspecialchars($user['name']) . ",</p>
                <p>Your password reset verification code is: <strong>$code</strong></p>
                <p>This code will expire in 1 hour.</p>
                <p>If you did not request this, please ignore this email.</p>
            ";

            if (send_email($email, $user['name'], $subject, $body)) {
                // Redirect to verify.php for code verification
                header('Location: verify.php');
                exit;
            } else {
                $errors[] = 'Failed to send verification code email. Please try again later.';
            }
        }
    }
}

// Helper function to send email using PHPMailer
function send_email($to_email, $to_name, $subject, $body) {
    require_once 'vendor/autoload.php';
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = SMTP_AUTH;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;

        $mail->setFrom(SMTP_USERNAME, 'School Management');
        $mail->addAddress($to_email, $to_name);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #A7BEAE;
        }
        input:focus, button:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
        }
        .boxs {
            background-color: #E7E8D1;
        }
        h1 {
            color: #B85042;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="boxs p-10 rounded-xl shadow-lg w-full max-w-md">
        <h1 class="text-3xl font-extrabold mb-8 text-center">Forgot Password</h1>
        <?php if ($errors): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-300 text-red-700 rounded-lg">
                <ul class="list-disc list-inside space-y-1">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php elseif ($success): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-300 text-green-700 rounded-lg">
                <?= $success ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="forgot_password.php" novalidate>
            <label class="block mb-2 font-semibold text-gray-700" for="email">Email Address</label>
            <input class="w-full p-3 mb-8 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2" type="email" id="email" name="email" required style="--tw-ring-color: #B85042;" />
            <button class="w-full text-white py-3 rounded-lg font-semibold" type="submit" style="background-color: #B85042">Send Verification Code</button>
        </form>
        <p class="mt-6 text-center text-gray-600">
            Remembered your password? <a href="login.php" class="font-semibold" style="color: #B85042">Login</a>.
        </p>
    </div>
</body>
</html>
