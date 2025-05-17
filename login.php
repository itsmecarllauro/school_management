<?php
require 'config.php';
require 'functions.php';

session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }
    if (empty($password)) {
        $errors[] = 'Password is required.';
    }

if (empty($errors)) {
    $stmt = $pdo->prepare("SELECT id, name, email, password_hash, email_verified, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $errors[] = 'Invalid email or password.';
    } elseif (!$user['email_verified']) {
        $errors[] = 'Account not verified. Please check your email for the verification code.';
    } elseif (!password_verify($password, $user['password_hash'])) {
        $errors[] = 'Invalid email or password.';
    } else {
        // Login successful
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        // Log the login action
        log_action($pdo, $user['id'], 'User logged in');

        // Role-based redirection
        if ($user['role'] === 'admin') {
            header('Location: admin_dashboard.php');
        } elseif ($user['role'] === 'teacher') {
            header('Location: teacher_dashboard.php');
        } else {
            header('Location: dashboard.php');
        }
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
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #A7BEAE;
        }
        input:focus, button:focus {
            outline: none;
            box-shadow: 0 0 0 3px #B85042;
        }
        .boxs {
            background-color: #E7E8D1;
        }
        h1 {
            color: #B85042;
        }
    </style>
</head>
<body class=" flex items-center justify-center min-h-screen">
    <div class="boxs  p-10 rounded-xl shadow-lg w-full max-w-md">
        <h1 class="text-3xl font-extrabold mb-8 text-center ">School Contact Manager</h1>
        <?php if ($errors): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-300 text-red-700 rounded-lg">
                <ul class="list-disc list-inside space-y-1">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="POST" action="login.php" novalidate>
            <label class="block mb-2 font-semibold text-gray-700" for="email">Email</label>
            <input class="w-full p-3 mb-5 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2" type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required style="--tw-ring-color: #B85042;" />

            <label class="block mb-2 font-semibold text-gray-700" for="password">Password</label>
            <input class="w-full p-3 mb-6 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2" type="password" id="password" name="password" required style="--tw-ring-color: #B85042;"/>

            <button class="w-full  text-white py-3 rounded-lg font-semibold" type="submit" style="background-color: #B85042">Login</button>
        </form>
        <p class="mt-6 text-center text-gray-600">
            Don't have an account? <a href="register.php" class=" font-semibold " style="color: #B85042">Sign Up</a>.
        </p>
    </div>
</body>
</html>
