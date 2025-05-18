<?php
require 'config.php';
require 'functions.php';

session_start();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username'] ?? '');
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = sanitize_input($_POST['role'] ?? 'user');

    if (empty($username)) {
        $errors[] = 'Username is required.';
    }
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
    if (!in_array($role, ['admin', 'teacher', 'user'])) {
        $errors[] = 'Invalid role selected.';
    }

    if (empty($errors)) {

        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        if ($stmt->fetch()) {
            $errors[] = 'Email or username is already registered.';
        } else {
   
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
 
            $verification_code = generate_verification_code();

            $stmt = $pdo->prepare("INSERT INTO users (username, name, email, password_hash, verification_code, role) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$username, $name, $email, $password_hash, $verification_code, $role]);

            if (send_verification_email($email, $name, $verification_code)) {
                $success = 'Verification email sent. Please check your inbox.';
                $_SESSION['email'] = $email;
                $_SESSION['name'] = $name;
                header('Location: verify.php');
                exit;
            } else {
                $errors[] = 'Email could not be sent. Please try again later.';
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
            background-color: #A7BEAE;
        }
        input:focus, button:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
        }
        .boxs{
            background-color: #E7E8D1;
        }
        h1 {
            color: #B85042;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="boxs p-10 rounded-xl shadow-lg w-full max-w-md">
        <h1 class="text-3xl font-extrabold mb-8 text-center">Create Your Account</h1>
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
            <label class="block mb-2 font-semibold text-gray-700" for="username">Username</label>
            <input class="w-full p-3 mb-5 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2 " type="text" id="username" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required style="--tw-ring-color: #B85042;" />

            <label class="block mb-2 font-semibold text-gray-700" for="name">Full Name</label>
            <input class="w-full p-3 mb-5 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2 " type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" style="--tw-ring-color: #B85042;" />

            <label class="block mb-2 font-semibold text-gray-700" for="email">Email Address</label>
            <input class="w-full p-3 mb-5 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2 " type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" style="--tw-ring-color: #B85042;"/>

            <label class="block mb-2 font-semibold text-gray-700" for="role">Role</label>
            <select id="role" name="role" class="w-full p-3 mb-5 border border-gray-300 rounded-lg focus:ring-2 " style="--tw-ring-color: #B85042;">
                <option value="" <?= (($_POST['role'] ?? '') === 'user') ? 'selected' : '' ?> disabled selected>Choose Role</option>
                <option value="user" <?= (($_POST['role'] ?? '') === 'user') ? 'selected' : '' ?>>User</option>
                <option value="teacher" <?= (($_POST['role'] ?? '') === 'teacher') ? 'selected' : '' ?>>Teacher</option>

            </select>

            <label class="block mb-2 font-semibold text-gray-700" for="password">Password</label>
            <input class="w-full p-3 mb-5 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2 " type="password" id="password" name="password" required style="--tw-ring-color: #B85042;"/>

            <label class="block mb-2 font-semibold text-gray-700" for="confirm_password">Confirm Password</label>
            <input class="w-full p-3 mb-8 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2 " type="password" id="confirm_password" name="confirm_password" required style="--tw-ring-color: #B85042;"/>

            <button class="w-full text-white py-3 rounded-lg font-semibold " type="submit" style="background-color: #B85042">Register</button>
        </form>
        <p class="mt-6 text-center text-gray-600">
            Already have an account? <a href="login.php" class="font-semibold " style="color: #B85042">Login</a>.
        </p>
    </div>
</body>
</html>
