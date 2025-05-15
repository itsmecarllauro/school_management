<?php
require 'config.php';
require 'functions.php';

session_start();
ensure_logged_in();
ensure_role('admin');

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

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = 'Email is already registered.';
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, is_active, created_at) VALUES (?, ?, ?, 'teacher', 1, NOW())");
        $stmt->execute([$name, $email, $hashed_password]);
        $success = 'Teacher account created successfully.';
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body{
            background-color: #A7BEAE;
        }
        h1{
            color: #B85042;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center">
    <div class=" p-10 rounded-xl shadow-lg w-full max-w-md" style="background-color: #E7E8D1">
        <h1 class="text-3xl font-extrabold mb-8 text-center ">Create Teacher Account</h1>
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
        <form method="POST" action="admin_create_teacher.php" novalidate>
            <label class="block mb-2 font-semibold text-gray-700" for="name">Name</label>
            <input class="w-full p-3 mb-5 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2 " type="text" id="name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" style="--tw-ring-color: #B85042;"/>

            <label class="block mb-2 font-semibold text-gray-700" for="email">Email</label>
            <input class="w-full p-3 mb-5 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2 " type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" style="--tw-ring-color: #B85042;"/>

            <label class="block mb-2 font-semibold text-gray-700" for="password">Password</label>
            <input class="w-full p-3 mb-5 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2 " type="password" id="password" name="password" required style="--tw-ring-color: #B85042;"/>

            <label class="block mb-2 font-semibold text-gray-700" for="confirm_password">Confirm Password</label>
            <input class="w-full p-3 mb-8 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2 " type="password" id="confirm_password" name="confirm_password" required style="--tw-ring-color: #B85042;"/>

            <button class="w-full text-white py-3 rounded-lg font-semibold " type="submit" style="background-color: #B85042">Create Teacher</button>
        </form>
        <p class="mt-6 text-center">
            <a href="admin_dashboard.php" class="text-blue-600 font-semibold" style="color: #B85042">Back to Dashboard</a>
        </p>
    </div>
</body>
</html>
