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
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Timmo - Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
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

</body>
</html>