<?php
require 'config.php';
require 'functions.php';

session_start();
ensure_logged_in();
ensure_role('admin');

// Handle activate/deactivate user
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        $user_id = (int)$_GET['toggle'];
        if ($user_id === $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'error' => 'You cannot deactivate your own account.']);
            exit;
        }
        $stmt = $pdo->prepare("SELECT is_active FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $new_status = $user['is_active'] ? 0 : 1;
            $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?");
            $stmt->execute([$new_status, $user_id]);
            echo json_encode(['success' => true, 'is_active' => $new_status]);
            exit;
        } else {
            echo json_encode(['success' => false, 'error' => 'User not found.']);
            exit;
        }
    } else {
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
                header('Location: admin_users.php?return=' . urlencode($return_page));
                exit;
            }
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
            header('Location: admin_users.php?return=' . urlencode($return_page));
            exit;
    }
}

// Handle delete user
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        $user_id = (int)$_GET['delete'];
        if ($user_id === $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'error' => 'You cannot delete your own account.']);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        echo json_encode(['success' => true]);
        exit;
    } else {
        $user_id = (int)$_GET['delete'];
        // Prevent admin from deleting themselves
        if ($user_id === $_SESSION['user_id']) {
            $error = "You cannot delete your own account.";
        } else {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            header('Location: admin_users.php?return=' . urlencode($return_page));
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

$return_page = 'admin_users.php';
if (isset($_GET['return'])) {
    $allowed_pages = ['admin_search.php', 'admin_users.php', 'admin_dashboard.php'];
    if (in_array($_GET['return'], $allowed_pages)) {
        $return_page = $_GET['return'];
    }
}

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
            header('Location: admin_users.php?return=' . urlencode($return_page));
            exit;
        }
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
        header('Location: admin_users.php?return=' . urlencode($return_page));
        exit;
    }
}

if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_user_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT id, name, email, role, is_active FROM users WHERE id = ? AND email_verified = 1");
    $stmt->execute([$edit_user_id]);
    $edit_user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all users except current admin
$stmt = $pdo->prepare("SELECT id, name, email, role, is_active, created_at FROM users WHERE id != ? AND email_verified = 1 AND role != 'admin' AND email != 'admin' ORDER BY created_at DESC");
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
                if (isset($_GET['return'])) {
                    $allowed_pages = ['admin_search.php', 'admin_users.php', 'admin_dashboard.php'];
                    if (in_array($_GET['return'], $allowed_pages)) {
                        $dashboard_link = $_GET['return'];
                    } else {
                        $dashboard_link = 'admin_users.php';
                    }
                } else {
                    if ($user_role === 'admin') {
                        $dashboard_link = 'admin_dashboard.php';
                    } elseif ($user_role === 'teacher') {
                        $dashboard_link = 'teacher_dashboard.php';
                    } else {
                        $dashboard_link = 'dashboard.php';
                    }
                }
            ?>
                <a href="<?= htmlspecialchars($dashboard_link) ?>" class="inline-block mb-6 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition">← Back</a>
        </p>
        <div id="message" class="mb-6 p-4 hidden rounded-lg"></div>

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
            <tbody id="user-table-body">
                <?php foreach ($users as $user): ?>
                    <tr class="border-t border-gray-200 hover:bg-blue-50 transition" data-user-id="<?= $user['id'] ?>">
                        <td class="py-3 px-6"><?= htmlspecialchars($user['name']) ?></td>
                        <td class="py-3 px-6"><?= htmlspecialchars($user['email']) ?></td>
                        <td class="py-3 px-6"><?= htmlspecialchars(ucfirst($user['role'])) ?></td>
                        <td class="py-3 px-6 status-cell"><?= $user['is_active'] ? '<span class="text-green-600 font-semibold">Active</span>' : '<span class="text-red-600 font-semibold">Inactive</span>' ?></td>
                        <td class="py-3 px-6"><?= htmlspecialchars($user['created_at']) ?></td>
                <td class="py-3 px-6 space-x-4">
                    <button class="toggle-btn text-blue-600 font-semibold" data-user-id="<?= $user['id'] ?>">
                        <?= $user['is_active'] ? 'Deactivate' : 'Activate' ?>
                    </button>
                    <button class="delete-btn text-red-600 font-semibold" data-user-id="<?= $user['id'] ?>">
                        Delete
                    </button>
                </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const messageDiv = document.getElementById('message');

            function showMessage(text, type = 'success') {
                messageDiv.textContent = text;
                messageDiv.className = '';
                messageDiv.classList.add('mb-6', 'p-4', 'rounded-lg');
                if (type === 'success') {
                    messageDiv.classList.add('bg-green-50', 'border', 'border-green-300', 'text-green-700');
                } else if (type === 'error') {
                    messageDiv.classList.add('bg-red-50', 'border', 'border-red-300', 'text-red-700');
                }
                messageDiv.style.display = 'block';
                setTimeout(() => {
                    messageDiv.style.display = 'none';
                }, 3000);
            }

            function updateUserRow(userId, isActive) {
                const row = document.querySelector(`tr[data-user-id='${userId}']`);
                if (!row) return;
                const statusCell = row.querySelector('.status-cell');
                const toggleBtn = row.querySelector('.toggle-btn');
                if (isActive) {
                    statusCell.innerHTML = '<span class="text-green-600 font-semibold">Active</span>';
                    toggleBtn.textContent = 'Deactivate';
                } else {
                    statusCell.innerHTML = '<span class="text-red-600 font-semibold">Inactive</span>';
                    toggleBtn.textContent = 'Activate';
                }
            }

            document.querySelectorAll('.toggle-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const userId = this.getAttribute('data-user-id');
                    fetch(`admin_users.php?toggle=${userId}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateUserRow(userId, data.is_active);
                            showMessage('User status updated successfully.');
                        } else {
                            showMessage(data.error || 'Failed to update user status.', 'error');
                        }
                    })
                    .catch(() => {
                        showMessage('Failed to update user status.', 'error');
                    });
                });
            });

            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const userId = this.getAttribute('data-user-id');
                    if (!confirm('Are you sure you want to delete this user?')) return;
                    fetch(`admin_users.php?delete=${userId}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const row = document.querySelector(`tr[data-user-id='${userId}']`);
                            if (row) row.remove();
                            showMessage('User deleted successfully.');
                        } else {
                            showMessage(data.error || 'Failed to delete user.', 'error');
                        }
                    })
                    .catch(() => {
                        showMessage('Failed to delete user.', 'error');
                    });
                });
            });
        });
    </script>
</body>
</html>
