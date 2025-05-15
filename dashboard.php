<?php
require 'config.php';
require 'functions.php';

session_start();
ensure_logged_in();

$user_id = $_SESSION['user_id'];
$user_role = get_user_role();
$search = sanitize_input($_GET['search'] ?? '');

// Handle delete contact
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    if ($user_role === 'admin') {
        $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
        $stmt->execute([$delete_id]);
    } elseif ($user_role === 'teacher') {
        $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ? AND (teacher_id = ? OR user_id = ?)");
        $stmt->execute([$delete_id, $user_id, $user_id]);
    } else {
        $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ? AND user_id = ?");
        $stmt->execute([$delete_id, $user_id]);
    }
    header('Location: dashboard.php');
    exit;
}

// Fetch contacts based on role and search
$like_search = "%$search%";
if ($user_role === 'admin') {
    if ($search) {
        $stmt = $pdo->prepare("SELECT * FROM contacts WHERE name LIKE ? OR email LIKE ? OR phone LIKE ? ORDER BY created_at DESC");
        $stmt->execute([$like_search, $like_search, $like_search]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM contacts ORDER BY created_at DESC");
        $stmt->execute();
    }
} elseif ($user_role === 'teacher') {
    if ($search) {
        $stmt = $pdo->prepare("SELECT * FROM contacts WHERE user_id = ? AND (name LIKE ? OR email LIKE ? OR phone LIKE ?) ORDER BY created_at DESC");
        $stmt->execute([$user_id, $like_search, $like_search, $like_search]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM contacts WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
    }
} else {
    if ($search) {
        $stmt = $pdo->prepare("SELECT * FROM contacts WHERE user_id = ? AND (name LIKE ? OR email LIKE ? OR phone LIKE ?) ORDER BY created_at DESC");
        $stmt->execute([$user_id, $like_search, $like_search, $like_search]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM contacts WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
    }
}
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>User</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #A7BEAE;
        }
        a:focus, button:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
        }
        h1 {
            color: #B85042;
        }
        table {
            background-color: #E7E8D1;
        }
        .boxs {
            background-color: #B85042;
        }
    </style>
</head>
<body class="min-h-screen">
    <nav class="shadow p-6 flex justify-between items-center max-w-7xl mx-auto" style="background-color: #A7BEAE;">
        <h1 class="text-2xl font-extrabold " >Contact Manager</h1>
        <div>
            <span class="mr-6 font-semibold text-gray-700">Hello, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
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
            <a href="profile.php" class="mr-6 text-blue-600 font-semibold">Profile</a>
            <a href="logout.php" class="text-red-600 font-semibold ">Logout</a>
        </div>
    </nav>
    <main class="p-8 max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-extrabold" style="color: #B85042">Your Contacts</h2>
            <a href="add_contact.php" class="boxs text-white px-6 py-3 rounded-lg font-semibold">Add Contact</a>
        </div>
        <form method="GET" action="dashboard.php" class="mb-6 max-w-sm">
            <input type="text" name="search" placeholder="Search contacts..." value="<?= htmlspecialchars($search) ?>" class="p-3 border border-gray-300 rounded-lg w-full placeholder-gray-400 focus:ring-2" style="--tw-ring-color: #B85042;"/>
        </form>
        <?php if (count($contacts) === 0): ?>
            <p class="text-gray-600 text-lg">No contacts found.</p>
        <?php else: ?>
            <div class="overflow-x-auto rounded-lg shadow">
                <table class="min-w-full rounded-lg">
                    <thead>
                        <tr class=" text-left text-gray-700 font-semibold">
                            <th class="py-3 px-6" style="color: #B85042">Name</th>
                            <th class="py-3 px-6" style="color: #B85042">Phone</th>
                            <th class="py-3 px-6" style="color: #B85042">Email</th>
                            <th class="py-3 px-6" style="color: #B85042">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contacts as $contact): ?>
                            <tr class="border-t text-gray-700 border-gray-200 hover:bg-blue-50 transition">
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
