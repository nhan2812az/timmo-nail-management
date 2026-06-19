<?php
require_once "config/database.php";
require_once "config/auth.php";
requireLogin();

$totalCustomers = $pdo->query("
    SELECT COUNT(*) FROM customers
")->fetchColumn();

$totalAppointments = $pdo->query("
    SELECT COUNT(*) FROM appointments
")->fetchColumn();

$totalServices = $pdo->query("
    SELECT COUNT(*) FROM services
")->fetchColumn();

$totalStaff = $pdo->query("
    SELECT COUNT(*) FROM staff
")->fetchColumn();

$todayRevenue = $pdo->query("
    SELECT COALESCE(SUM(amount),0)
    FROM payments
    WHERE DATE(payment_date)=CURDATE()
")->fetchColumn();

$monthRevenue = $pdo->query("
    SELECT COALESCE(SUM(amount),0)
    FROM payments
    WHERE YEAR(payment_date)=YEAR(CURDATE())
    AND MONTH(payment_date)=MONTH(CURDATE())
")->fetchColumn();

$todayAppointments = $pdo->query("
    SELECT COUNT(*)
    FROM appointments
    WHERE appointment_date = CURDATE()
")->fetchColumn();

$newCustomersThisMonth = $pdo->query("
    SELECT COUNT(*)
    FROM customers
    WHERE YEAR(created_at)=YEAR(CURDATE())
    AND MONTH(created_at)=MONTH(CURDATE())
")->fetchColumn();
$chartStmt = $pdo->query("
    SELECT DATE(payment_date) AS day,
           SUM(amount) AS revenue
    FROM payments
    GROUP BY DATE(payment_date)
    ORDER BY day ASC
    LIMIT 7
");

$chartData = $chartStmt->fetchAll(PDO::FETCH_ASSOC);

$chartLabels = [];
$chartRevenues = [];

foreach ($chartData as $row) {
    $chartLabels[] = $row["day"];
    $chartRevenues[] = (float)$row["revenue"];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Timmo - Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<div class="layout">
    <?php require_once __DIR__ . "/includes/sidebar.php"; ?>

    <main class="content">
        <h1>Dashboard</h1>

        <div class="cards">

            <div class="card">
                <h3>Khách hàng</h3>
                <p><?= $totalCustomers ?></p>
            </div>

            <div class="card">
                <h3>Lịch hẹn</h3>
                <p><?= $totalAppointments ?></p>
            </div>

            <div class="card">
                <h3>Dịch vụ</h3>
                <p><?= $totalServices ?></p>
            </div>

            <div class="card">
                <h3>Nhân viên</h3>
                <p><?= $totalStaff ?></p>
            </div>

            <div class="card">
                <h3>Doanh thu hôm nay</h3>
                <p><?= number_format($todayRevenue) ?>đ</p>
            </div>

            <div class="card">
                <h3>Doanh thu tháng</h3>
                <p><?= number_format($monthRevenue) ?>đ</p>
            </div>

            <div class="card">
                <h3>Lịch hôm nay</h3>
                <p><?= $todayAppointments ?></p>
            </div>

            <div class="card">
                <h3>Khách mới tháng</h3>
                <p><?= $newCustomersThisMonth ?></p>
            </div>

        </div>

        <div class="form-box">
            <h2>Biểu đồ doanh thu 7 ngày</h2>
            <canvas id="revenueChart" height="100"></canvas>
        </div>
        <?php

        $latestAppointments = $pdo->query("
        SELECT
            appointments.*,
            customers.name AS customer_name,
            services.name AS service_name
        FROM appointments
        JOIN customers ON appointments.customer_id = customers.id
        JOIN services ON appointments.service_id = services.id
        ORDER BY appointments.id DESC
        LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        ?>

        <h2>Lịch hẹn gần đây</h2>

        <table>
            <tr>
                <th>Khách hàng</th>
                <th>Dịch vụ</th>
                <th>Ngày</th>
                <th>Giờ</th>
                <th>Trạng thái</th>
            </tr>

            <?php foreach($latestAppointments as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item["customer_name"]) ?></td>
                <td><?= htmlspecialchars($item["service_name"]) ?></td>
                <td><?= htmlspecialchars($item["appointment_date"]) ?></td>
                <td><?= htmlspecialchars(substr($item["appointment_time"],0,5)) ?></td>
                <td><?= htmlspecialchars($item["status"]) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </main>
</div>
<script>
const labels = <?= json_encode($chartLabels) ?>;
const revenues = <?= json_encode($chartRevenues) ?>;

const ctx = document.getElementById("revenueChart");

if (ctx) {
    new Chart(ctx, {
        type: "line",
        data: {
            labels: labels,
            datasets: [{
                label: "Doanh thu",
                data: revenues,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}
</script>
</body>
</html>