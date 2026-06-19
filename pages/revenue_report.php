<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

$from_date = $_GET["from_date"] ?? date("Y-m-01");
$to_date = $_GET["to_date"] ?? date("Y-m-d");

$stmt = $pdo->prepare("
    SELECT 
        DATE(payment_date) AS report_date,
        COUNT(*) AS total_payments,
        SUM(amount) AS total_revenue
    FROM payments
    WHERE DATE(payment_date) BETWEEN ? AND ?
    GROUP BY DATE(payment_date)
    ORDER BY report_date ASC
");
$stmt->execute([$from_date, $to_date]);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalStmt = $pdo->prepare("
    SELECT 
        COUNT(*) AS total_payments,
        COALESCE(SUM(amount), 0) AS total_revenue
    FROM payments
    WHERE DATE(payment_date) BETWEEN ? AND ?
");
$totalStmt->execute([$from_date, $to_date]);
$total = $totalStmt->fetch(PDO::FETCH_ASSOC);

$revenueLabels = [];
$revenueData = [];

foreach ($reports as $row) {
    $revenueLabels[] = $row["report_date"];
    $revenueData[] = (float)$row["total_revenue"];
}

$topServicesStmt = $pdo->prepare("
    SELECT
        services.name,
        COUNT(appointments.id) AS total_bookings
    FROM appointments
    JOIN services ON appointments.service_id = services.id
    WHERE appointments.appointment_date BETWEEN ? AND ?
    GROUP BY services.id, services.name
    ORDER BY total_bookings DESC
    LIMIT 5
");
$topServicesStmt->execute([$from_date, $to_date]);
$topServices = $topServicesStmt->fetchAll(PDO::FETCH_ASSOC);

$serviceLabels = [];
$serviceCounts = [];

foreach ($topServices as $service) {
    $serviceLabels[] = $service["name"];
    $serviceCounts[] = (int)$service["total_bookings"];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Timmo - Báo cáo doanh thu</title>
    <link rel="stylesheet" href="../assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="layout">
    <?php require_once __DIR__ . "/../includes/sidebar.php"; ?>

    <main class="content">
        <h1>Báo cáo doanh thu</h1>

        <div class="form-box">
            <form method="GET">
                <label>Từ ngày</label>
                <input type="date" name="from_date" value="<?= htmlspecialchars($from_date) ?>">

                <label>Đến ngày</label>
                <input type="date" name="to_date" value="<?= htmlspecialchars($to_date) ?>">

                <button type="submit">Xem báo cáo</button>
            </form>
        </div>

        <div class="cards">
            <div class="card">
                <h3>Số lượt thanh toán</h3>
                <p><?= (int)($total["total_payments"] ?? 0) ?></p>
            </div>

            <div class="card">
                <h3>Tổng doanh thu</h3>
                <p><?= number_format((float)($total["total_revenue"] ?? 0)) ?>đ</p>
            </div>
        </div>

        <h2>Biểu đồ doanh thu</h2>

        <div class="form-box">
            <canvas id="revenueChart" height="100"></canvas>
        </div>

        <h2>Top dịch vụ bán chạy</h2>

        <div class="form-box">
            <canvas id="serviceChart" height="100"></canvas>
        </div>

        <h2>Doanh thu theo ngày</h2>

        <table>
            <thead>
                <tr>
                    <th>Ngày</th>
                    <th>Số lượt thanh toán</th>
                    <th>Doanh thu</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($reports)): ?>
                    <tr>
                        <td colspan="3">Chưa có dữ liệu.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reports as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row["report_date"]) ?></td>
                            <td><?= (int)$row["total_payments"] ?></td>
                            <td><?= number_format((float)$row["total_revenue"]) ?>đ</td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</div>

<script>
const revenueLabels = <?= json_encode($revenueLabels) ?>;
const revenueData = <?= json_encode($revenueData) ?>;

new Chart(document.getElementById("revenueChart"), {
    type: "bar",
    data: {
        labels: revenueLabels,
        datasets: [{
            label: "Doanh thu",
            data: revenueData
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

const serviceLabels = <?= json_encode($serviceLabels) ?>;
const serviceCounts = <?= json_encode($serviceCounts) ?>;

new Chart(document.getElementById("serviceChart"), {
    type: "bar",
    data: {
        labels: serviceLabels,
        datasets: [{
            label: "Số lượt đặt",
            data: serviceCounts
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
</script>

</body>
</html>