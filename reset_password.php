<?php
require 'config.php';
require 'functions.php';

session_start();

$errors = [];
$success = '';
$show_form = false;

// Check if user is authorized to reset password via session
if (isset($_SESSION['password_reset_user_id'])) {
    $user_id = $_SESSION['password_reset_user_id'];
    $show_form = true;
} else {
    $errors[] = 'Unauthorized access. Please verify your password reset code first.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $show_form) {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($password) || strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->execute([$password_hash, $user_id]);

        // Clear the session variable
        unset($_SESSION['password_reset_user_id']);

        $success = 'Your password has been reset successfully. You can now <a href="login.php" class="text-blue-600">login</a>.';
        $show_form = false;
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Reset Password</title>
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
        <h1 class="text-3xl font-extrabold mb-8 text-center">Reset Password</h1>
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
        <form method="POST" action="reset_password.php" novalidate>
            <label class="block mb-2 font-semibold text-gray-700" for="password">New Password</label>
            <input class="w-full p-3 mb-6 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2" type="password" id="password" name="password" required minlength="6" style="--tw-ring-color: #B85042;" />
            <label class="block mb-2 font-semibold text-gray-700" for="confirm_password">Confirm New Password</label>
            <input class="w-full p-3 mb-8 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2" type="password" id="confirm_password" name="confirm_password" required minlength="6" style="--tw-ring-color: #B85042;" />
            <button class="w-full text-white py-3 rounded-lg font-semibold" type="submit" style="background-color: #B85042" style="--tw-ring-color: #B85042;">Reset Password</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
