<?php
require 'config.php';
require 'functions.php';

session_start();
ensure_logged_in();
ensure_role('teacher');

$user_id = $_SESSION['user_id'];
$search = sanitize_input($_GET['search'] ?? '');

// Handle delete contact
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ? AND user_id = ?");
    $stmt->execute([$delete_id, $user_id]);
    header('Location: teacher_contacts.php');
    exit;
}

// Fetch contacts for this teacher only
$like_search = "%$search%";
if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM contacts WHERE user_id = ? AND (name LIKE ? OR email LIKE ? OR phone LIKE ?) ORDER BY created_at DESC");
    $stmt->execute([$user_id, $like_search, $like_search, $like_search]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM contacts WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
}
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Teacher</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
                body {
            font-family: 'Inter', sans-serif;
            background-color: #A7BEAE;
        }
        h2 {
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
    <nav class=" shadow p-6 flex justify-between items-center max-w-7xl mx-auto" style="color: #A7BEAE;">
        <h1 class="text-2xl font-extrabold "style="color: #B85042">Manage Contacts</h1>
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
                        <a href="teacher_dashboard.php" class="inline-block mb-6 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition">← Back</a>
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-extrabold " >Your Contacts</h2>
            <a href="add_contact.php" class=" text-white px-6 py-3 rounded-lg font-semibold " style="background-color: #B85042">Add Contact</a>
        </div>
        <form method="GET" action="teacher_contacts.php" class="mb-6 max-w-sm">
            <input type="text" name="search" placeholder="Search contacts..." value="<?= htmlspecialchars($search) ?>" class="p-3 border border-gray-300 rounded-lg w-full placeholder-gray-400 focus:ring-2 focus:ring-blue-500 transition" />
        </form>
        <?php if (count($contacts) === 0): ?>
            <p class="text-gray-600 text-lg">No contacts found.</p>
        <?php else: ?>
            <div class="overflow-x-auto rounded-lg shadow">
                <table class="min-w-full rounded-lg">
                    <thead>
                        <tr class=" text-left  font-semibold">
                            <th class="py-3 px-6" style="color: #B85042">Name</th>
                            <th class="py-3 px-6" style="color: #B85042">Phone</th>
                            <th class="py-3 px-6" style="color: #B85042">Email</th>
                            <th class="py-3 px-6" style="color: #B85042">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contacts as $contact): ?>
                            <tr class="border-t border-gray-200 hover:bg-blue-50 transition">
                                <td class="py-3 px-6"><?= htmlspecialchars($contact['name']) ?></td>
                                <td class="py-3 px-6"><?= htmlspecialchars($contact['phone']) ?></td>
                                <td class="py-3 px-6"><?= htmlspecialchars($contact['email']) ?></td>
                                <td class="py-3 px-6 space-x-4">
                                    <a href="edit_contact.php?id=<?= $contact['id'] ?>&return=teacher_contacts.php" class="text-blue-600 font-semibold">Edit</a>
                                    <a href="teacher_contacts.php?delete=<?= $contact['id'] ?>" onclick="return confirm('Are you sure you want to delete this contact?');" class="text-red-600 font-semibold">Delete</a>
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
