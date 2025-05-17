<?php
require 'config.php';
require 'functions.php';

session_start();
ensure_logged_in();

$user_id = $_SESSION['user_id'];
$user_role = get_user_role();
$errors = [];
$success = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$contact_id = (int)$_GET['id'];

// Fetch contact based on role
if ($user_role === 'admin') {
    $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ?");
    $stmt->execute([$contact_id]);
} elseif ($user_role === 'teacher') {
    // Teacher can edit contacts assigned to their users or themselves
    $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ? AND (teacher_id = ? OR user_id = ?)");
    $stmt->execute([$contact_id, $user_id, $user_id]);
} else {
    // User can edit only their own contacts
    $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ? AND user_id = ?");
    $stmt->execute([$contact_id, $user_id]);
}
$contact = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$contact) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');

    if (empty($name)) {
        $errors[] = 'Name is required.';
    }
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email is not valid.';
    }

    if (empty($errors)) {
        if ($user_role === 'admin') {
            $stmt = $pdo->prepare("UPDATE contacts SET name = ?, phone = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $phone, $email, $contact_id]);
        } elseif ($user_role === 'teacher') {
            $stmt = $pdo->prepare("UPDATE contacts SET name = ?, phone = ?, email = ? WHERE id = ? AND (teacher_id = ? OR user_id = ?)");
            $stmt->execute([$name, $phone, $email, $contact_id, $user_id, $user_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE contacts SET name = ?, phone = ?, email = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$name, $phone, $email, $contact_id, $user_id]);
        }
        $success = 'Contact updated successfully.';
        // Refresh contact data
        if ($user_role === 'admin') {
            $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ?");
            $stmt->execute([$contact_id]);
        } elseif ($user_role === 'teacher') {
            $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ? AND (teacher_id = ? OR user_id = ?)");
            $stmt->execute([$contact_id, $user_id, $user_id]);
        } else {
            $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ? AND user_id = ?");
            $stmt->execute([$contact_id, $user_id]);
        }
        $contact = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Contact</title>
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
        h1 {
            color: #B85042;
        }

    </style>
</head>
<body class=" min-h-screen flex items-center justify-center">
    <div class=" p-10 rounded-xl shadow-lg w-full max-w-md" style="background-color: #E7E8D1;">
        <h1 class="text-3xl font-extrabold mb-8 text-center">Edit Contact</h1>
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
<?php
$return_param = isset($_GET['return']) ? '&return=' . urlencode($_GET['return']) : '';
?>
<form method="POST" action="edit_contact.php?id=<?= $contact_id . $return_param ?>" novalidate>
            <label class="block mb-2 font-semibold text-gray-700" for="name">Name</label>
            <input class="w-full p-3 mb-5 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2" type="text" id="name" name="name" placeholder="John Doe" value="<?= htmlspecialchars($contact['name']) ?>" required style="--tw-ring-color: #B85042;"/>

            <label class="block mb-2 font-semibold text-gray-700" for="phone">Phone</label>
            <input class="w-full p-3 mb-5 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2" type="text" id="phone" name="phone" placeholder="+1 234 567 890" value="<?= htmlspecialchars($contact['phone']) ?>" style="--tw-ring-color: #B85042;"/>

            <label class="block mb-2 font-semibold text-gray-700" for="email">Email</label>
            <input class="w-full p-3 mb-8 border border-gray-300 rounded-lg placeholder-gray-400 focus:ring-2" type="email" id="email" name="email" placeholder="contact@example.com" value="<?= htmlspecialchars($contact['email']) ?>" style="--tw-ring-color: #B85042;"/>

            <button class="w-full  text-white py-3 rounded-lg font-semibold " type="submit" style="background-color: #B85042">Update Contact</button>
        </form>
<p class="mt-6 text-center">
            <?php
if ($user_role === 'admin') {
    $return_page = 'admin_teacher_contacts.php';
} elseif ($user_role === 'teacher') {
    $return_page = 'teacher_contacts.php';
} else {
    $return_page = 'dashboard.php';
}
                if (isset($_GET['return'])) {
$allowed_pages = ['admin_teacher_contacts.php', 'admin_search.php', 'teacher_contacts.php'];
                    if (in_array($_GET['return'], $allowed_pages)) {
                        $return_page = $_GET['return'];
                    }
                }
            ?>
            <a href="<?= htmlspecialchars($return_page) ?>" class=" font-semibold" style="color: #B85042;">Back to Dashboard</a>
        </p>
    </div>
</body>
</html>
