<?php
require 'config.php';
require 'functions.php';

session_start();
ensure_logged_in();

$user_id = $_SESSION['user_id'];
$user_role = get_user_role();
$errors = [];
$success = '';

$assignable_users = [];
if ($user_role === 'teacher') {
    $stmt = $pdo->prepare("SELECT id, name FROM users WHERE role = 'user' AND is_active = 1 ORDER BY name");
    $stmt->execute();
    $assignable_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');

    if ($user_role === 'teacher') {

        if (isset($_POST['assigned_user_id']) && is_numeric($_POST['assigned_user_id']) && $_POST['assigned_user_id'] !== '') {
            $assigned_user_id = (int)$_POST['assigned_user_id'];
        } else {
            $assigned_user_id = $user_id;
        }
    } else {
        $assigned_user_id = $user_id;
    }

    if (empty($name)) {
        $errors[] = 'Name is required.';
    }
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email is not valid.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO contacts (user_id, teacher_id, name, phone, email) VALUES (?, ?, ?, ?, ?)");
        $teacher_id = $user_role === 'teacher' ? $user_id : null;
        $stmt->execute([$assigned_user_id, $teacher_id, $name, $phone, $email]);
        $success = 'Contact added successfully.';
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Contact</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #A7BEAE
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
        <h1 class="text-3xl font-extrabold mb-8 text-center " style="color: #B85042">Add New Contact</h1>
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
        <form method="POST" action="add_contact.php" novalidate>
            <label class="block mb-2 font-semibold text-gray-700" for="name">Name</label>
            <input class="w-full p-3 mb-5 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2 " type="text" id="name" name="name"  value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required style="--tw-ring-color: #B85042;"/>

            <label class="block mb-2 font-semibold text-gray-700" for="phone">Phone</label>
            <input class="w-full p-3 mb-5 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2 " type="text" id="phone" name="phone"  value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" style="--tw-ring-color: #B85042;"/>

            <label class="block mb-2 font-semibold text-gray-700" for="email">Email</label>
            <input class="w-full p-3 mb-8 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2 " type="email" id="email" name="email"  value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" style="--tw-ring-color: #B85042;"/>

            <?php if ($user_role === 'teacher'): ?>
                <label class="block mb-2 font-semibold text-gray-700" for="assigned_user_id">Assign to User</label>
                <select id="assigned_user_id" name="assigned_user_id" class="w-full p-3 mb-8 border border-gray-300 rounded-lg focus:ring-2 " style="--tw-ring-color: #B85042;">
                    <option value="">Select a user</option>
                    <?php foreach ($assignable_users as $user): ?>
                        <option value="<?= $user['id'] ?>" <?= (($_POST['assigned_user_id'] ?? '') == $user['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($user['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <input type="hidden" name="assigned_user_id" value="<?= $user_id ?>">
            <?php endif; ?>

            <button class="w-full  text-white py-3 rounded-lg font-semibold " type="submit" style="background-color: #B85042">Add Contact</button>
        </form>
       <p class="mt-6 text-center">
            <?php
                $user_role = get_user_role();
                if ($user_role === 'admin') {
                    $dashboard_link = 'admin_dashboard.php';
                } elseif ($user_role === 'teacher') {
                    $dashboard_link = 'teacher_contacts.php';
                } else {
                    $dashboard_link = 'dashboard.php';
                } 
            ?>
            <a href="<?= $dashboard_link ?>" class=" font-semibold" style="color: #B85042">Back to Dashboard</a>
        </p>
    </div>
    </div>
</body>
</html>
