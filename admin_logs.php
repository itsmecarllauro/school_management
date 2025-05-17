<?php
require 'config.php';
require 'functions.php';

session_start();
ensure_logged_in();
ensure_role('admin');

$limit = 20;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$stmt = $pdo->query("SELECT COUNT(*) FROM audit_logs");
$total_logs = $stmt->fetchColumn();
$total_pages = ceil($total_logs / $limit);

$stmt = $pdo->prepare("
    SELECT audit_logs.*, users.name AS user_name 
    FROM audit_logs 
    JOIN users ON audit_logs.user_id = users.id 
    ORDER BY audit_logs.created_at DESC 
    LIMIT ? OFFSET ?
");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        th {
            color: #B85042;
        }
    </style>
</head>
<body class=" min-h-screen">
    <nav class=" shadow p-6 flex justify-between items-center max-w-7xl mx-auto" style="color: #B85042">
        <h1 class="text-2xl font-extrabold ">Audit Logs</h1>

    </nav>
    <main class="p-8 max-w-7xl mx-auto">
                <a href="admin_dashboard.php" class="inline-block mb-6 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition">← Back</a>
        <table class="min-w-full rounded-lg shadow overflow-hidden">
            <thead class=" text-left text-blue-700 font-semibold">
                <tr>
                    <th class="py-3 px-6">User</th>
                    <th class="py-3 px-6">Action</th>
                    <th class="py-3 px-6">Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr class="border-t border-gray-200 hover:bg-blue-50 transition">
                        <td class="py-3 px-6"><?= htmlspecialchars($log['user_name']) ?></td>
                        <td class="py-3 px-6"><?= htmlspecialchars($log['action']) ?></td>
                        <td class="py-3 px-6"><?= htmlspecialchars($log['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="mt-6 flex justify-center space-x-4">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>" class=" font-semibold hover:underline" style="color: #B85042">Previous</a>
            <?php endif; ?>
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?= $page + 1 ?>" class=" font-semibold hover:underline" style="color: #B85042">Next</a>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
