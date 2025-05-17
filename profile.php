<?php
require 'config.php';
require 'functions.php';

session_start();
ensure_logged_in();

$user_id = $_SESSION['user_id'];
$errors = [];
$success = '';

// Fetch current user data
$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // User not found, logout
    session_destroy();
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name)) {
        $errors[] = 'Name is required.';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }

    // Check if email changed and is unique
    if ($email !== $user['email']) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user_id]);
        if ($stmt->fetch()) {
            $errors[] = 'Email is already taken.';
        }
    }

    // If changing password, validate current password and new passwords
    if ($new_password || $confirm_password) {
        if (empty($current_password)) {
            $errors[] = 'Current password is required to change password.';
        } else {
            $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user_password = $stmt->fetchColumn();
            if (!password_verify($current_password, $user_password)) {
                $errors[] = 'Current password is incorrect.';
            }
        }
        if ($new_password !== $confirm_password) {
            $errors[] = 'New passwords do not match.';
        }
    }

    if (empty($errors)) {
        // Update name and email
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->execute([$name, $email, $user_id]);

        // Update password if provided
        if ($new_password) {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$new_password_hash, $user_id]);
        }

        $success = 'Profile updated successfully.';
        $_SESSION['user_name'] = $name;
        $user['name'] = $name;
        $user['email'] = $email;
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Profile</title>
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
        .boxs {
            background-color: #E7E8D1;
        }
    </style>
</head>
<body class=" min-h-screen flex items-center justify-center">
    <div class="boxs p-10 rounded-xl shadow-lg w-full max-w-md">
        <h1 class="text-3xl font-extrabold mb-8 text-center " style="color: #B85042">Profile Settings</h1>
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
        <form method="POST" action="profile.php" novalidate>
            <label class="block mb-2 font-semibold text-gray-700" for="name">Full Name</label>
            <input class="w-full p-3 mb-5 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2 " type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required style="--tw-ring-color: #B85042;"/>

            <label class="block mb-2 font-semibold text-gray-700" for="email">Email Address</label>
            <input class="w-full p-3 mb-5 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2 " type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required style="--tw-ring-color: #B85042;"/>

            <hr class="my-6" />

            <label class="block mb-2 font-semibold text-gray-700" for="current_password">Current Password</label>
            <input class="w-full p-3 mb-5 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2 " type="password" id="current_password" name="current_password" style="--tw-ring-color: #B85042;"/>

            <label class="block mb-2 font-semibold text-gray-700" for="new_password">New Password</label>
            <input class="w-full p-3 mb-5 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2 " type="password" id="new_password" name="new_password" style="--tw-ring-color: #B85042;"/>

            <label class="block mb-2 font-semibold text-gray-700" for="confirm_password">Confirm New Password</label>
            <input class="w-full p-3 mb-8 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2 " type="password" id="confirm_password" name="confirm_password" style="--tw-ring-color: #B85042;"/>

            <button class="w-full  text-white py-3 rounded-lg font-semibold " type="submit" style="background-color: #B85042">Update Profile</button>
        </form>
        <p class="mt-6 text-center">
            <?php
                $user_role = get_user_role();
                if ($user_role === 'admin') {
                    $dashboard_link = 'admin_dashboard.php';
                } elseif ($user_role === 'teacher') {
                    $dashboard_link = 'teacher_dashboard.php';
                } else {
                    $dashboard_link = 'dashboard.php';
                }
                if ($user_role === 'admin') {
                    $dashboard_link = 'admin_dashboard.php';
                } elseif ($user_role === 'teacher') {
                    $dashboard_link = 'teacher_contacts.php';
                } else {
                    $dashboard_link = 'dashboard.php';
                }
                if ($user_role === 'admin') {
                    $dashboard_link = 'admin_dashboard.php';
                } elseif ($user_role === 'teacher') {
                    $dashboard_link = 'teacher_dashboard.php';
                } else {
                    $dashboard_link = 'dashboard.php';
                }
            ?>
            <a href="<?= $dashboard_link ?>" class="text-blue-600 font-semibold" style="color: #B85042">Back to Dashboard</a>
        </p>
    </div>
</body>
</html>
