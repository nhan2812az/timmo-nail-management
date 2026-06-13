<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

$id = $_GET["id"] ?? null;

if (!$id) {
    header("Location: appointments.php?error=Không tìm thấy lịch hẹn");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ?");
$stmt->execute([$id]);
$appointment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$appointment) {
    header("Location: appointments.php?error=Lịch hẹn không tồn tại");
    exit;
}

$customers = $pdo->query("SELECT * FROM customers ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$services = $pdo->query("SELECT * FROM services WHERE status = 'active' ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$staffList = $pdo->query("SELECT * FROM staff WHERE status = 'active' ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa lịch hẹn - Timmo</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="layout">
    <?php include "../includes/sidebar.php"; ?>

    <main class="content">
        <h1>Sửa lịch hẹn</h1>

        <div class="form-box">
            <form action="../actions/update_appointment.php" method="POST">
                <input type="hidden" name="id" value="<?= $appointment["id"] ?>">

                <label>Khách hàng</label>
                <select name="customer_id" required>
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?= $customer["id"] ?>" <?= $customer["id"] == $appointment["customer_id"] ? "selected" : "" ?>>
                            <?= htmlspecialchars($customer["name"]) ?> - <?= htmlspecialchars($customer["phone"]) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Dịch vụ</label>
                <select name="service_id" required>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= $service["id"] ?>" <?= $service["id"] == $appointment["service_id"] ? "selected" : "" ?>>
                            <?= htmlspecialchars($service["name"]) ?> - <?= number_format($service["price"]) ?>đ
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Nhân viên</label>
                <select name="staff_id" required>
                    <?php foreach ($staffList as $staff): ?>
                        <option value="<?= $staff["id"] ?>" <?= $staff["id"] == $appointment["staff_id"] ? "selected" : "" ?>>
                            <?= htmlspecialchars($staff["name"]) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Ngày hẹn</label>
                <input type="date" name="appointment_date" value="<?= $appointment["appointment_date"] ?>" required>

                <label>Giờ hẹn</label>
                <input type="time" name="appointment_time" value="<?= substr($appointment["appointment_time"], 0, 5) ?>" required>

                <label>Trạng thái</label>
                <select name="status" required>
                    <option value="new" <?= $appointment["status"] === "new" ? "selected" : "" ?>>Mới</option>
                    <option value="confirmed" <?= $appointment["status"] === "confirmed" ? "selected" : "" ?>>Đã xác nhận</option>
                    <option value="completed" <?= $appointment["status"] === "completed" ? "selected" : "" ?>>Hoàn thành</option>
                    <option value="cancelled" <?= $appointment["status"] === "cancelled" ? "selected" : "" ?>>Đã hủy</option>
                </select>

                <label>Ghi chú</label>
                <textarea name="note"><?= htmlspecialchars($appointment["note"]) ?></textarea>

                <button type="submit">Lưu thay đổi</button>
            </form>
        </div>
    </main>
</div>

</body>
</html>