<?php
require 'config.php';
require 'functions.php';

session_start();
ensure_logged_in();

$user_id = $_SESSION['user_id'];
$search = sanitize_input($_GET['search'] ?? '');

// Handle delete contact
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ? AND user_id = ?");
    $stmt->execute([$delete_id, $user_id]);
    header('Location: dashboard.php');
    exit;
}

// Fetch contacts
if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM contacts WHERE user_id = ? AND (name LIKE ? OR email LIKE ? OR phone LIKE ?) ORDER BY created_at DESC");
    $like_search = "%$search%";
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
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        a:focus, button:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
        }
    </style>
</head>
<body class="bg-gradient-to-r from-blue-100 via-white to-blue-100 min-h-screen">
    <nav class="bg-white shadow p-6 flex justify-between items-center max-w-7xl mx-auto">
        <h1 class="text-2xl font-extrabold text-blue-700">Contact Manager</h1>
        <div>
            <span class="mr-6 font-semibold text-gray-700">Hello, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <a href="profile.php" class="mr-6 text-blue-600 font-semibold">Profile</a>
            <a href="logout.php" class="text-red-600 font-semibold ">Logout</a>
        </div>
    </nav>
    <main class="p-8 max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-extrabold text-gray-800">Your Contacts</h2>
            <a href="add_contact.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">Add Contact</a>
        </div>
        <form method="GET" action="dashboard.php" class="mb-6 max-w-sm">
            <input type="text" name="search" placeholder="Search contacts..." value="<?= htmlspecialchars($search) ?>" class="p-3 border border-gray-300 rounded-lg w-full placeholder-gray-400 focus:ring-2 focus:ring-blue-500 transition" />
        </form>
        <?php if (count($contacts) === 0): ?>
            <p class="text-gray-600 text-lg">No contacts found.</p>
        <?php else: ?>
            <div class="overflow-x-auto rounded-lg shadow">
                <table class="min-w-full bg-white rounded-lg">
                    <thead>
                        <tr class="bg-blue-100 text-left text-blue-700 font-semibold">
                            <th class="py-3 px-6">Name</th>
                            <th class="py-3 px-6">Phone</th>
                            <th class="py-3 px-6">Email</th>
                            <th class="py-3 px-6">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contacts as $contact): ?>
                            <tr class="border-t border-gray-200 hover:bg-blue-50 transition">
                                <td class="py-3 px-6"><?= htmlspecialchars($contact['name']) ?></td>
                                <td class="py-3 px-6"><?= htmlspecialchars($contact['phone']) ?></td>
                                <td class="py-3 px-6"><?= htmlspecialchars($contact['email']) ?></td>
                                <td class="py-3 px-6 space-x-4">
                                    <a href="edit_contact.php?id=<?= $contact['id'] ?>" class="text-blue-600 font-semibold ">Edit</a>
                                    <a href="dashboard.php?delete=<?= $contact['id'] ?>" onclick="return confirm('Are you sure you want to delete this contact?');" class="text-red-600 font-semibold ">Delete</a>
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
