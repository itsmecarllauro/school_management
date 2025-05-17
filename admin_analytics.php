<?php
require 'config.php';
require 'functions.php';

session_start();
ensure_logged_in();
ensure_role('admin');

$stmt = $pdo->prepare("
    SELECT DATE(created_at) AS date, COUNT(*) AS count
    FROM users
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY DATE(created_at)
    ORDER BY DATE(created_at)
");
$stmt->execute();
$user_registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT DATE(created_at) AS date, COUNT(*) AS count
    FROM contacts
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY DATE(created_at)
    ORDER BY DATE(created_at)
");
$stmt->execute();
$contact_counts = $stmt->fetchAll(PDO::FETCH_ASSOC);

function prepare_chart_data($data) {
    $dates = [];
    $counts = [];
    foreach ($data as $row) {
        $dates[] = $row['date'];
        $counts[] = (int)$row['count'];
    }
    return ['dates' => $dates, 'counts' => $counts];
}

$user_data = prepare_chart_data($user_registrations);
$contact_data = prepare_chart_data($contact_counts);
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #A7BEAE;
        }
        h1 {
            color: #B85042;
        }

        nav {
            background-color: #A7BEAE;
        }
        th {
            color: #B85042;
        }
    </style>
</head>
<body class="min-h-screen">
    <nav class="shadow p-6 flex justify-between items-center max-w-7xl mx-auto" style="background-color: #A7BEAE;">
        <h1 class="text-2xl font-extrabold ">Analytics</h1>

    </nav>
    <main class="p-8 max-w-7xl mx-auto">
                <a href="admin_dashboard.php" class="inline-block mb-6 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition">← Back</a>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <section class="p-6 rounded-lg shadow" style="background-color: #E7E8D1;" >
                <h2 class="text-xl font-semibold mb-4" style="color: #B85042">User Registrations (Last 30 Days)</h2>
                <canvas id="userRegistrationsChart"></canvas>
            </section>
            <section class=" p-6 rounded-lg shadow" style="background-color: #E7E8D1;">
                <h2 class="text-xl font-semibold mb-4" style="color: #B85042">Contacts Added (Last 30 Days)</h2>
                <canvas id="contactsChart"></canvas>
            </section>
        </div>
    </main>
    <script>
        const userCtx = document.getElementById('userRegistrationsChart').getContext('2d');
        const userChart = new Chart(userCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($user_data['dates']) ?>,
                datasets: [{
                    label: 'User Registrations',
                    data: <?= json_encode($user_data['counts']) ?>,
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    fill: true,
                    tension: 0.3,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { display: true, title: { display: true, text: 'Date' } },
                    y: { display: true, title: { display: true, text: 'Count' }, beginAtZero: true }
                }
            }
        });

        const contactCtx = document.getElementById('contactsChart').getContext('2d');
        const contactChart = new Chart(contactCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($contact_data['dates']) ?>,
                datasets: [{
                    label: 'Contacts Added',
                    data: <?= json_encode($contact_data['counts']) ?>,
                    borderColor: 'rgba(16, 185, 129, 1)',
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    fill: true,
                    tension: 0.3,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { display: true, title: { display: true, text: 'Date' } },
                    y: { display: true, title: { display: true, text: 'Count' }, beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>
