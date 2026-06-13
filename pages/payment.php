<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

$id = $_GET["id"] ?? null;

if (!$id) {
    header("Location: appointments.php?error=Không tìm thấy lịch hẹn");
    exit;
}

$stmt = $pdo->prepare("
    SELECT 
        appointments.*,
        customers.name AS customer_name,
        customers.phone AS customer_phone,
        services.name AS service_name,
        services.price AS service_price,
        staff.name AS staff_name
    FROM appointments
    JOIN customers ON appointments.customer_id = customers.id
    JOIN services ON appointments.service_id = services.id
    JOIN staff ON appointments.staff_id = staff.id
    WHERE appointments.id = ?
");

$stmt->execute([$id]);
$appointment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$appointment) {
    header("Location: appointments.php?error=Lịch hẹn không tồn tại");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán - Timmo</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="layout">
    <?php include "../includes/sidebar.php"; ?>

    <main class="content">
        <h1>Thanh toán lịch hẹn</h1>

        <div class="form-box">
            <h2>Thông tin lịch hẹn</h2>

            <p><strong>Khách hàng:</strong> <?= htmlspecialchars($appointment["customer_name"]) ?></p>
            <p><strong>SĐT:</strong> <?= htmlspecialchars($appointment["customer_phone"]) ?></p>
            <p><strong>Dịch vụ:</strong> <?= htmlspecialchars($appointment["service_name"]) ?></p>
            <p><strong>Nhân viên:</strong> <?= htmlspecialchars($appointment["staff_name"]) ?></p>
            <p><strong>Ngày:</strong> <?= htmlspecialchars($appointment["appointment_date"]) ?></p>
            <p><strong>Giờ:</strong> <?= htmlspecialchars(substr($appointment["appointment_time"], 0, 5)) ?></p>
            <p><strong>Giá dịch vụ:</strong> <?= number_format($appointment["service_price"]) ?>đ</p>
            <p><strong>Trạng thái thanh toán:</strong> 
                <?= $appointment["payment_status"] === "paid" ? "Đã thanh toán" : "Chưa thanh toán" ?>
            </p>
        </div>

        <?php if ($appointment["payment_status"] !== "paid"): ?>
            <div class="form-box">
                <h2>Thực hiện thanh toán</h2>

                <form action="../actions/create_payment.php" method="POST">
                    <input type="hidden" name="appointment_id" value="<?= $appointment["id"] ?>">

                    <label>Số tiền</label>
                    <input 
                        type="number" 
                        name="amount" 
                        value="<?= htmlspecialchars($appointment["service_price"]) ?>" 
                        min="0" 
                        required
                    >

                    <label>Phương thức thanh toán</label>
                    <select name="payment_method" required>
                        <option value="cash">Tiền mặt</option>
                        <option value="bank">Chuyển khoản</option>
                        <option value="card">Thẻ</option>
                    </select>

                    <label>Ghi chú</label>
                    <textarea name="note" placeholder="Ví dụ: khách chuyển khoản, giảm giá, tip..."></textarea>

                    <button type="submit">Xác nhận thanh toán</button>
                </form>
            </div>
        <?php else: ?>
            <p style="color: green; font-weight: bold;">Lịch hẹn này đã được thanh toán.</p>
        <?php endif; ?>

        <a href="appointments.php">← Quay lại lịch hẹn</a>
    </main>
</div>

</body>
</html>