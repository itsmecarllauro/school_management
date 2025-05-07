<?php
require 'config.php';
require 'functions.php';

session_start();

$errors = [];
$success = '';

if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

$email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_code = sanitize_input($_POST['verification_code'] ?? '');

    if (empty($input_code) || !preg_match('/^\d{6}$/', $input_code)) {
        $errors[] = 'Please enter a valid 6-digit verification code.';
    } else {
        $stmt = $pdo->prepare("SELECT id, verification_code, is_verified FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $errors[] = 'User not found.';
        } elseif ($user['is_verified']) {
            $errors[] = 'Account already verified. Please login.';
        } elseif ($user['verification_code'] !== $input_code) {
            $errors[] = 'Incorrect verification code.';
        } else {

            $stmt = $pdo->prepare("UPDATE users SET is_verified = 1, verification_code = NULL WHERE id = ?");
            $stmt->execute([$user['id']]);
            unset($_SESSION['email']);
            $success = 'Your account has been verified. You can now <a href="login.php" class="text-blue-600 ">login</a>.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Verify Email</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <script type="text/javascript"
        src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js">
    </script>


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
        <h1 class="text-3xl font-extrabold mb-8 text-center text-blue-700">Email Verification</h1>
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
        <?php if (!$success): ?>
        <form method="POST" action="verify.php" novalidate>
            <label class="block mb-2 font-semibold text-gray-700" for="verification_code">Enter 6-digit Verification Code</label>
            <input class="w-full p-3 mb-8 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2 focus:ring-blue-500 transition" type="text" id="verification_code" name="verification_code" maxlength="6" pattern="\d{6}" required />
            <button class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition" type="submit">Verify</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
