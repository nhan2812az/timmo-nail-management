<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

$id = $_GET["id"] ?? null;

if (!$id) {
    header("Location: customers.php?error=Thiếu ID khách hàng");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    header("Location: customers.php?error=Không tìm thấy khách hàng");
    exit;
}

$summaryStmt = $pdo->prepare("
    SELECT
        COUNT(a.id) AS total_visits,
        COALESCE(SUM(p.amount), 0) AS total_spent,
        MAX(a.appointment_date) AS last_visit
    FROM appointments a
    LEFT JOIN payments p ON p.appointment_id = a.id
    WHERE a.customer_id = ?
");
$summaryStmt->execute([$id]);
$summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);

$favServiceStmt = $pdo->prepare("
    SELECT
        s.name,
        COUNT(a.id) AS total
    FROM appointments a
    JOIN services s ON a.service_id = s.id
    WHERE a.customer_id = ?
    GROUP BY s.id, s.name
    ORDER BY total DESC
    LIMIT 1
");
$favServiceStmt->execute([$id]);
$favService = $favServiceStmt->fetch(PDO::FETCH_ASSOC);

$favStaffStmt = $pdo->prepare("
    SELECT
        st.name,
        COUNT(a.id) AS total
    FROM appointments a
    JOIN staff st ON a.staff_id = st.id
    WHERE a.customer_id = ?
    GROUP BY st.id, st.name
    ORDER BY total DESC
    LIMIT 1
");
$favStaffStmt->execute([$id]);
$favStaff = $favStaffStmt->fetch(PDO::FETCH_ASSOC);

$appointmentsStmt = $pdo->prepare("
    SELECT
        a.*,
        s.name AS service_name,
        st.name AS staff_name,
        p.amount AS payment_amount
    FROM appointments a
    JOIN services s ON a.service_id = s.id
    JOIN staff st ON a.staff_id = st.id
    LEFT JOIN payments p ON p.appointment_id = a.id
    WHERE a.customer_id = ?
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");
$appointmentsStmt->execute([$id]);
$appointments = $appointmentsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Timmo - Thống kê khách hàng</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="layout">
    <?php include "../includes/sidebar.php"; ?>

    <main class="content">
        <h1>Thống kê khách hàng</h1>

        <div class="form-box">
            <h2><?= htmlspecialchars($customer["name"]) ?></h2>
            <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($customer["phone"]) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($customer["email"]) ?></p>
            <p><strong>Ghi chú:</strong> <?= htmlspecialchars($customer["note"]) ?></p>
        </div>

        <div class="cards">
            <div class="card">
                <h3>Tổng lượt đến</h3>
                <p><?= (int)($summary["total_visits"] ?? 0) ?></p>
            </div>

            <div class="card">
                <h3>Tổng chi tiêu</h3>
                <p><?= number_format((float)($summary["total_spent"] ?? 0)) ?>đ</p>
            </div>

            <div class="card">
                <h3>Lần gần nhất</h3>
                <p><?= htmlspecialchars($summary["last_visit"] ?? "Chưa có") ?></p>
            </div>

            <div class="card">
                <h3>Dịch vụ yêu thích</h3>
                <p><?= htmlspecialchars($favService["name"] ?? "Chưa có") ?></p>
            </div>

            <div class="card">
                <h3>Nhân viên thường chọn</h3>
                <p><?= htmlspecialchars($favStaff["name"] ?? "Chưa có") ?></p>
            </div>
        </div>

        <h2>Lịch sử sử dụng dịch vụ</h2>

        <table>
            <tr>
                <th>Ngày</th>
                <th>Giờ</th>
                <th>Dịch vụ</th>
                <th>Nhân viên</th>
                <th>Trạng thái</th>
                <th>Thanh toán</th>
            </tr>

            <?php if (empty($appointments)): ?>
                <tr>
                    <td colspan="6">Khách hàng chưa có lịch hẹn.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($appointments as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item["appointment_date"]) ?></td>
                        <td><?= htmlspecialchars(substr($item["appointment_time"], 0, 5)) ?></td>
                        <td><?= htmlspecialchars($item["service_name"]) ?></td>
                        <td><?= htmlspecialchars($item["staff_name"]) ?></td>
                        <td><?= htmlspecialchars($item["status"]) ?></td>
                        <td>
                            <?= $item["payment_amount"] !== null 
                                ? number_format((float)$item["payment_amount"]) . "đ" 
                                : "Chưa thanh toán" 
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>

        <br>
        <a href="customers.php">← Quay lại danh sách khách hàng</a>
    </main>
</div>

</body>
</html>