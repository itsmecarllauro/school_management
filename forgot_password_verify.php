<?php
require 'config.php';
require 'functions.php';

session_start();

$errors = [];
$success = '';
$show_form = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email'] ?? '');
    $code = sanitize_input($_POST['code'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (empty($code) || !preg_match('/^\d{6}$/', $code)) {
        $errors[] = 'Please enter a valid 6-digit verification code.';
    }

    if (empty($errors)) {
        // Check if code matches and is not expired
        $stmt = $pdo->prepare("SELECT pr.user_id, pr.expires_at FROM password_resets pr JOIN users u ON pr.user_id = u.id WHERE u.email = ? AND pr.token = ?");
        $stmt->execute([$email, $code]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            $errors[] = 'Invalid email or verification code.';
        } elseif (strtotime($row['expires_at']) < time()) {
            $errors[] = 'Verification code has expired. Please request a new one.';
        } else {
            // Code verified, set session for password reset
            $_SESSION['password_reset_user_id'] = $row['user_id'];
            // Delete the used code
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?");
            $stmt->execute([$row['user_id']]);
            // Redirect to reset password page
            header('Location: reset_password.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Verify Password Reset Code</title>
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
        <h1 class="text-3xl font-extrabold mb-8 text-center">Verify Password Reset Code</h1>
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
        <?php if ($show_form): ?>
        <form method="POST" action="forgot_password_verify.php" novalidate>
            <label class="block mb-2 font-semibold text-gray-700" for="email">Email Address</label>
            <input class="w-full p-3 mb-6 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2" type="email" id="email" name="email" required style="--tw-ring-color: #B85042;" />
            <label class="block mb-2 font-semibold text-gray-700" for="code">6-digit Verification Code</label>
            <input class="w-full p-3 mb-8 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2" type="text" id="code" name="code" maxlength="6" pattern="\d{6}" required style="--tw-ring-color: #B85042;" />
            <button class="w-full text-white py-3 rounded-lg font-semibold" type="submit" style="background-color: #B85042">Verify Code</button>
        </form>
        <?php endif; ?>
        <p class="mt-6 text-center text-gray-600">
            Remembered your password? <a href="login.php" class="font-semibold" style="color: #B85042">Login</a>.
        </p>
    </div>
</body>
</html>
