<?php
require 'config.php';
require 'functions.php';

session_start();
ensure_logged_in();
ensure_role('admin');

$search = sanitize_input($_GET['q'] ?? '');
$users = [];
$contacts = [];

if ($search) {
    $like_search = "%$search%";

    $stmt = $pdo->prepare("SELECT id, name, email, role, is_active FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY created_at DESC");
    $stmt->execute([$like_search, $like_search]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT c.*, u.name AS assigned_user_name, t.name AS teacher_name FROM contacts c LEFT JOIN users u ON c.user_id = u.id LEFT JOIN users t ON c.teacher_id = t.id WHERE c.name LIKE ? OR c.email LIKE ? OR c.phone LIKE ? ORDER BY c.created_at DESC");
    $stmt->execute([$like_search, $like_search, $like_search]);
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        body {
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
        nav {
            background-color: #A7BEAE;
        }
    </style>
</head>
<body class=" min-h-screen">
    <nav class=" shadow p-6 flex justify-between items-center max-w-7xl mx-auto">
        <h1 class="text-2xl font-extrabold ">Global Search</h1>

    </nav>
    <main class="p-8 max-w-7xl mx-auto">
        <a href="admin_dashboard.php" class="inline-block mb-6 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition">← Back</a>
        <form method="GET" action="admin_search.php" class="mb-8 max-w-md">
            <input type="text" name="q" placeholder="Search users and contacts..." value="<?= htmlspecialchars($search) ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 transition" />
            <button type="submit" class="mt-4 bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">Search</button>
        </form>

        <?php if ($search): ?>
            <section class="mb-8">
                <h2 class="text-xl font-semibold mb-4" style="color: #B85042">Users</h2>
                <?php if (count($users) === 0): ?>
                    <p class="text-gray-600">No users found.</p>
                <?php else: ?>
                    <table class="min-w-full  rounded-lg shadow overflow-hidden">
                        <thead class="text-left font-semibold" style="color: #B85042">
                            <tr>
                                <th class="py-3 px-6">Name</th>
                                <th class="py-3 px-6">Email</th>
                                <th class="py-3 px-6">Role</th>
                                <th class="py-3 px-6">Status</th>
                                <th class="py-3 px-6">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr class="border-t border-gray-200 hover:bg-blue-50 transition">
                                    <td class="py-3 px-6"><?= htmlspecialchars($user['name']) ?></td>
                                    <td class="py-3 px-6"><?= htmlspecialchars($user['email']) ?></td>
                                    <td class="py-3 px-6"><?= htmlspecialchars(ucfirst($user['role'])) ?></td>
                                    <td class="py-3 px-6"><?= $user['is_active'] ? '<span class="text-green-600 font-semibold">Active</span>' : '<span class="text-red-600 font-semibold">Inactive</span>' ?></td>
                                    <td class="py-3 px-6">
                                        <a href="admin_users.php?edit=<?= $user['id'] ?>" class="text-blue-600 font-semibold">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>

            <section>
                <h2 class="text-xl font-semibold mb-4" style="color: #B85042">Contacts</h2>
                <?php if (count($contacts) === 0): ?>
                    <p class="text-gray-600">No contacts found.</p>
                <?php else: ?>
                    <table class="min-w-full bg-white rounded-lg shadow overflow-hidden">
                        <thead class="text-left font-semibold"style="color: #B85042;">
                            <tr>
                                <th class="py-3 px-6">Name</th>
                                <th class="py-3 px-6">Phone</th>
                                <th class="py-3 px-6">Email</th>
                                <th class="py-3 px-6">Assigned User</th>
                                <th class="py-3 px-6">Teacher</th>
                                <th class="py-3 px-6">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contacts as $contact): ?>
                                <tr class="border-t border-gray-200 hover:bg-blue-50 transition">
                                    <td class="py-3 px-6"><?= htmlspecialchars($contact['name']) ?></td>
                                    <td class="py-3 px-6"><?= htmlspecialchars($contact['phone']) ?></td>
                                    <td class="py-3 px-6"><?= htmlspecialchars($contact['email']) ?></td>
                                    <td class="py-3 px-6"><?= htmlspecialchars($contact['assigned_user_name'] ?? 'N/A') ?></td>
                                    <td class="py-3 px-6"><?= htmlspecialchars($contact['teacher_name'] ?? 'N/A') ?></td>
                                    <td class="py-3 px-6 space-x-4">
                                        <a href="edit_contact.php?id=<?= $contact['id'] ?>" class="text-blue-600 font-semibold">Edit</a>
                                        <a href="admin_teacher_contacts.php?delete=<?= $contact['id'] ?>" onclick="return confirm('Are you sure you want to delete this contact?');" class="text-red-600 font-semibold">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>
