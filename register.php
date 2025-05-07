<?php
require 'config.php';
require 'functions.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name)) {
        $errors[] = 'Name is required.';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }
    if (empty($password)) {
        $errors[] = 'Password is required.';
    }
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {

        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email is already registered.';
        } else {
   
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
 
            $verification_code = generate_verification_code();

            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, verification_code) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $password_hash, $verification_code]);

            $mail = new PHPMailer(true);

            try {

                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; 
                $mail->SMTPAuth = true;
                $mail->Username = 'carllauro08@gmail.com';
                $mail->Password = 'bdxf kjdc lnfe hmzw'; 
                $mail->SMTPSecure = 'ssl'; 
                $mail->Port = 465; 

                $mail->setFrom('carllauro08@gmail.com', 'Contact Manager'); 
                $mail->addAddress($email, $name); 
                $mail->isHTML(true);
                $mail->Subject = 'Email Verification Code';
                $mail->Body = "
                    <p>Hi <strong>$name</strong>,</p>
                    <p>Thank you for registering. Please use the following verification code to verify your email address:</p>
                    <h2>$verification_code</h2>
                    <p>If you did not register, please ignore this email.</p>
                ";

                $mail->send();
                $success = 'Verification email sent. Please check your inbox.';

                $_SESSION['email'] = $email;
                $_SESSION['name'] = $name;

                header('Location: verify.php');
                exit;
            } catch (Exception $e) {
                $errors[] = "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        input:focus, button:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
        }
    </style>
</head>
<body class="bg-gradient-to-r from-blue-100 via-white to-blue-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-10 rounded-xl shadow-lg w-full max-w-md">
        <h1 class="text-3xl font-extrabold mb-8 text-center text-blue-700">Create Your Account</h1>
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
        <form method="POST" action="register.php" novalidate>
            <label class="block mb-2 font-semibold text-gray-700" for="name">Full Name</label>
            <input class="w-full p-3 mb-5 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2 focus:ring-blue-500 transition" type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" />

            <label class="block mb-2 font-semibold text-gray-700" for="email">Email Address</label>
            <input class="w-full p-3 mb-5 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2 focus:ring-blue-500 transition" type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />

            <label class="block mb-2 font-semibold text-gray-700" for="password">Password</label>
            <input class="w-full p-3 mb-5 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2 focus:ring-blue-500 transition" type="password" id="password" name="password" required />

            <label class="block mb-2 font-semibold text-gray-700" for="confirm_password">Confirm Password</label>
            <input class="w-full p-3 mb-8 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2 focus:ring-blue-500 transition" type="password" id="confirm_password" name="confirm_password" required />

            <button class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition" type="submit">Register</button>
        </form>
        <p class="mt-6 text-center text-gray-600">
            Already have an account? <a href="login.php" class="text-blue-600 font-semibold ">Login here</a>.
        </p>
    </div>
</body>
</html>