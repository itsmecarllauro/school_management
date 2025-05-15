<?php
require 'config.php';
require 'functions.php';

session_start();
ensure_logged_in();
ensure_role('admin');

// Handle activate/deactivate user
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $user_id = (int)$_GET['toggle'];
    // Prevent admin from deactivating themselves
    if ($user_id === $_SESSION['user_id']) {
        $error = "You cannot deactivate your own account.";
    } else {
        $stmt = $pdo->prepare("SELECT is_active FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $new_status = $user['is_active'] ? 0 : 1;
            $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?");
            $stmt->execute([$new_status, $user_id]);
            header('Location: admin_users.php');
            exit;
        }
    }
}

// Handle edit user
if (isset($_POST['edit_user_id']) && is_numeric($_POST['edit_user_id'])) {
    $edit_user_id = (int)$_POST['edit_user_id'];
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $role = $_POST['role'] ?? '';
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if ($edit_user_id === $_SESSION['user_id']) {
        $error = "You cannot edit your own account here.";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role = ?, is_active = ? WHERE id = ?");
        $stmt->execute([$name, $email, $role, $is_active, $edit_user_id]);
        header('Location: admin_users.php');
        exit;
    }
}

// Handle delete user
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $user_id = (int)$_GET['delete'];
    // Prevent admin from deleting themselves
    if ($user_id === $_SESSION['user_id']) {
        $error = "You cannot delete your own account.";
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        header('Location: admin_users.php');
        exit;
    }
}

// Handle edit user
if (isset($_POST['edit_user_id']) && is_numeric($_POST['edit_user_id'])) {
    $edit_user_id = (int)$_POST['edit_user_id'];
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $role = $_POST['role'] ?? '';
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if ($edit_user_id === $_SESSION['user_id']) {
        $error = "You cannot edit your own account here.";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role = ?, is_active = ? WHERE id = ?");
        $stmt->execute([$name, $email, $role, $is_active, $edit_user_id]);
        header('Location: admin_users.php');
        exit;
    }
}

if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_user_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT id, name, email, role, is_active FROM users WHERE id = ?");
    $stmt->execute([$edit_user_id]);
    $edit_user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all users except current admin
$stmt = $pdo->prepare("SELECT id, name, email, role, is_active, created_at FROM users WHERE id != ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body
            {
                font-family: 'Inter', sans-serif;
                background-color: #A7BEAE;
            }
            h1 {
                color: #B85042;
            }
            table {
                background-color: #E7E8D1;
            }
            tr {
                background-color: #E7E8D1;
            }
    </style>
</head>
<body class="min-h-screen">
    <nav class="shadow p-6 flex justify-between items-center max-w-7xl mx-auto" style="color: #A7BEAE;">
        <h1 class="text-2xl font-extrabold " style="color: #B85042;">User Management</h1>
        <div>
            <?php
                $user_role = get_user_role();
                if ($user_role === 'admin') {
                    $dashboard_link = 'admin_dashboard.php';
                } elseif ($user_role === 'teacher') {
                    $dashboard_link = 'teacher_dashboard.php';
                } else {
                    $dashboard_link = 'dashboard.php';
                }
            ?>



        </div>
    </nav>
    <main class="p-8 max-w-7xl mx-auto">
        <p class="">
            <?php
                $user_role = get_user_role();
                if ($user_role === 'admin') {
                    $dashboard_link = 'admin_dashboard.php';
                } elseif ($user_role === 'admin') {
                    $dashboard_link = 'admin_search.php';
                } else {
                    $dashboard_link = 'admin_dashboard.php';
                }
                 if ($user_role === 'admin') {
                    $dashboard_link = 'admin_search.php';
                } elseif ($user_role === 'admin') {
                    $dashboard_link = 'admin_search.php';
                } else {
                    $dashboard_link = 'admin_dashboard.php';
                }

            ?>
                <a href="admin_dashboard.php" class="inline-block mb-6 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition">← Back</a>
        </p>

        <?php if (!empty($error)): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-300 text-red-700 rounded-lg">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <table class="min-w-full  rounded-lg shadow overflow-hidden">
            <thead class=" text-left font-semibold">
                <tr>
                    <th class="py-3 px-6" style="color: #B85042">Name</th>
                    <th class="py-3 px-6" style="color: #B85042">Email</th>
                    <th class="py-3 px-6" style="color: #B85042">Role</th>
                    <th class="py-3 px-6" style="color: #B85042">Status</th>
                    <th class="py-3 px-6" style="color: #B85042">Created At</th>
                    <th class="py-3 px-6" style="color: #B85042">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr class="border-t border-gray-200 hover:bg-blue-50 transition">
                        <td class="py-3 px-6"><?= htmlspecialchars($user['name']) ?></td>
                        <td class="py-3 px-6"><?= htmlspecialchars($user['email']) ?></td>
                        <td class="py-3 px-6"><?= htmlspecialchars(ucfirst($user['role'])) ?></td>
                        <td class="py-3 px-6"><?= $user['is_active'] ? '<span class="text-green-600 font-semibold">Active</span>' : '<span class="text-red-600 font-semibold">Inactive</span>' ?></td>
                        <td class="py-3 px-6"><?= htmlspecialchars($user['created_at']) ?></td>
                        <td class="py-3 px-6 space-x-4">
                            <a href="admin_users.php?toggle=<?= $user['id'] ?>" class="text-blue-600 font-semibold">
                                <?= $user['is_active'] ? 'Deactivate' : 'Activate' ?>
                            </a>
                            <a href="admin_users.php?delete=<?= $user['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?');" class="text-red-600 font-semibold">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
