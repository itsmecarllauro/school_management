<?php
require 'config.php';
require 'functions.php';

session_start();
ensure_logged_in();
ensure_role('admin');

// Fetch contacts assigned by teachers
$stmt = $pdo->prepare("
    SELECT c.*, u.name AS assigned_user_name, t.name AS teacher_name
    FROM contacts c
    LEFT JOIN users u ON c.user_id = u.id
    LEFT JOIN users t ON c.teacher_id = t.id
    ORDER BY c.created_at DESC
");
$stmt->execute();
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle delete contact
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
    $stmt->execute([$delete_id]);
    header('Location: admin_teacher_contacts.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin </title>
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
    <nav class=" shadow p-6 flex justify-between items-center max-w-7xl mx-auto" style="color: #A7BEAE;">
        <h1 class="text-2xl font-extrabold">Teacher Submitted Contacts</h1>
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
                        <a href="admin_dashboard.php" class="inline-block mb-6 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition">← Back</a>
        <?php if (count($contacts) === 0): ?>
            <p class="text-gray-600 text-lg">No contacts found.</p>
        <?php else: ?>
            <div class="overflow-x-auto rounded-lg shadow">
                <table class="min-w-full bg-white rounded-lg">
                    <thead>
                        <tr class=" text-left font-semibold" style="color: #B85042">
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
                                    <a href="edit_contact.php?id=<?= $contact['id'] ?>&return=admin_teacher_contacts.php" class="text-blue-600 font-semibold">Edit</a>
                                    <a href="admin_teacher_contacts.php?delete=<?= $contact['id'] ?>" onclick="return confirm('Are you sure you want to delete this contact?');" class="text-red-600 font-semibold">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
