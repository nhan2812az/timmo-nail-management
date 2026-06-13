<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

$id = $_GET["id"] ?? null;

if (!$id) {
    header("Location: customers.php?error=Không tìm thấy khách hàng");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    header("Location: customers.php?error=Khách hàng không tồn tại");
    exit;
}

$stmt = $pdo->prepare("
    SELECT 
        appointments.*,
        services.name AS service_name,
        services.price AS service_price,
        staff.name AS staff_name
    FROM appointments
    JOIN services ON appointments.service_id = services.id
    JOIN staff ON appointments.staff_id = staff.id
    WHERE appointments.customer_id = ?
    ORDER BY appointments.appointment_date DESC, appointments.appointment_time DESC
");

$stmt->execute([$id]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalSpent = 0;

foreach ($appointments as $item) {
    if ($item["status"] === "completed") {
        $totalSpent += $item["service_price"];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch sử khách hàng - Timmo</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="layout">
    <?php include "../includes/sidebar.php"; ?>

    <main class="content">
        <h1>Lịch sử khách hàng</h1>

        <div class="form-box">
            <h2><?= htmlspecialchars($customer["name"]) ?></h2>
            <p><strong>SĐT:</strong> <?= htmlspecialchars($customer["phone"]) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($customer["email"]) ?></p>
            <p><strong>Ghi chú:</strong> <?= htmlspecialchars($customer["note"]) ?></p>
            <p><strong>Tổng chi tiêu hoàn thành:</strong> <?= number_format($totalSpent) ?>đ</p>
        </div>

        <h2>Lịch sử đặt lịch</h2>

        <table>
            <tr>
                <th>Ngày</th>
                <th>Giờ</th>
                <th>Dịch vụ</th>
                <th>Giá</th>
                <th>Nhân viên</th>
                <th>Trạng thái</th>
                <th>Ghi chú</th>
            </tr>

            <?php foreach ($appointments as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item["appointment_date"]) ?></td>
                    <td><?= htmlspecialchars(substr($item["appointment_time"], 0, 5)) ?></td>
                    <td><?= htmlspecialchars($item["service_name"]) ?></td>
                    <td><?= number_format($item["service_price"]) ?>đ</td>
                    <td><?= htmlspecialchars($item["staff_name"]) ?></td>
                    <td><?= htmlspecialchars($item["status"]) ?></td>
                    <td><?= htmlspecialchars($item["note"]) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <br>
        <a href="customers.php">← Quay lại khách hàng</a>
    </main>
</div>

</body>
</html>