<?php
require 'config.php';
require 'functions.php';

session_start();
ensure_logged_in();
ensure_role('admin');

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
    </style>
</head>
<body class=" min-h-screen">
    <nav class=" shadow p-6 flex justify-between items-center max-w-7xl mx-auto" style="color: #A7BEAE;">
        <h1 class="text-2xl font-extrabold ">Admin Dashboard</h1>
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

            <a href="profile.php" class="mr-6 text-blue-600 font-semibold ">Profile</a>
            <a href="logout.php" class="text-red-600 font-semibold">Logout</a>
        </div>
    </nav>
    <main class="p-8 max-w-7xl mx-auto">
        <h2 class="text-xl font-semibold mb-4">Welcome to the Admin Dashboard</h2>
        <p>Here you can manage users, view logs, and perform administrative tasks.</p>
        <div class="mt-6 space-y-2">
            <a href="admin_users.php" class="font-semibold hover:underline" style="color:  #B85042;">Manage Users</a>
            <br />
            <a href="admin_teacher_contacts.php" class="font-semibold hover:underline" style="color: #B85042;">Manage Teacher Contacts</a>
            <br />
            <a href="admin_create_teacher.php" class="font-semibold hover:underline" style="color:  #B85042;">Create Teacher Account</a>
            <br />
            <a href="admin_search.php" class="font-semibold hover:underline" style="color: #B85042;">Global Search</a>
            <br />
            <a href="admin_logs.php" class="font-semibold hover:underline" style="color:  #B85042;">View Audit Logs</a>
            <br />
            <a href="admin_export.php" class="font-semibold hover:underline" style="color:  #B85042;">Export Users CSV</a>
            <br />
            <a href="admin_analytics.php" class="font-semibold hover:underline" style="color:  #B85042;">View Analytics</a>
        </div>
    </main>
</body>
</html>
