<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

$customers = $pdo->query("SELECT * FROM customers ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$services = $pdo->query("SELECT * FROM services WHERE status = 'active' ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$staffList = $pdo->query("SELECT * FROM staff WHERE status = 'active' ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

$where = [];
$params = [];

if (!empty($_GET["date"])) {
    $where[] = "appointments.appointment_date = ?";
    $params[] = $_GET["date"];
}

if (!empty($_GET["status"])) {
    $where[] = "appointments.status = ?";
    $params[] = $_GET["status"];
}

if (!empty($_GET["staff_id"])) {
    $where[] = "appointments.staff_id = ?";
    $params[] = $_GET["staff_id"];
}

if (!empty($_GET["keyword"])) {
    $where[] = "(customers.name LIKE ? OR customers.phone LIKE ?)";
    $params[] = "%" . $_GET["keyword"] . "%";
    $params[] = "%" . $_GET["keyword"] . "%";
}

$whereSql = "";

if (count($where) > 0) {
    $whereSql = "WHERE " . implode(" AND ", $where);
}

$sql = "
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
    $whereSql
    ORDER BY appointments.appointment_date DESC, appointments.appointment_time DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$error = $_GET["error"] ?? "";
$success = $_GET["success"] ?? "";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Timmo - Lịch hẹn</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="layout">
    <?php include "../includes/sidebar.php"; ?>

    <main class="content">
        <h1>Quản lý lịch hẹn</h1>

        <?php if ($error): ?>
            <p style="color: red; font-weight: bold;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p style="color: green; font-weight: bold;">
                <?= htmlspecialchars($success) ?>
            </p>
        <?php endif; ?>
        <?php if ($error === "appointment_conflict"): ?>
            <p style="color: red;">
                Nhân viên này đã có lịch trong khoảng thời gian đó. Vui lòng chọn giờ khác.
            </p>
        <?php endif; ?>

        <div class="form-box">
            <h2>Tạo lịch hẹn mới</h2>

            <form action="../actions/create_appointment.php" method="POST">
                <label>Khách hàng</label>
                <select name="customer_id" required>
                    <option value="">-- Chọn khách hàng --</option>
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?= $customer["id"] ?>">
                            <?= htmlspecialchars($customer["name"]) ?> - <?= htmlspecialchars($customer["phone"]) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Dịch vụ</label>
                <select name="service_id" required>
                    <option value="">-- Chọn dịch vụ --</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= $service["id"] ?>">
                            <?= htmlspecialchars($service["name"]) ?> - <?= number_format($service["price"]) ?>đ
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Nhân viên</label>
                <select name="staff_id" required>
                    <option value="">-- Chọn nhân viên --</option>
                    <?php foreach ($staffList as $staff): ?>
                        <option value="<?= $staff["id"] ?>">
                            <?= htmlspecialchars($staff["name"]) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Ngày hẹn</label>
                <input type="date" name="appointment_date" required>

                <label>Giờ hẹn</label>
                <input type="time" name="appointment_time" required>

                <label>Ghi chú</label>
                <textarea name="note" placeholder="Ví dụ: khách muốn làm màu hồng, đến sớm 10 phút..."></textarea>
                <label>Trạng thái</label>
                <select name="status">
                    <option value="new">Mới</option>
                    <option value="confirmed">Đã xác nhận</option>
                    <option value="completed">Hoàn thành</option>
                    <option value="cancelled">Đã hủy</option>
                </select>

                <button type="submit">Tạo lịch hẹn</button>
            </form>
        </div>

        <h2>Danh sách lịch hẹn</h2>
        <div class="form-box">
    <h2>Lọc lịch hẹn</h2>

    <form method="GET">
        <label>Từ khóa khách hàng / SĐT</label>
        <input 
            type="text" 
            name="keyword" 
            placeholder="Nhập tên hoặc số điện thoại"
            value="<?= htmlspecialchars($_GET["keyword"] ?? "") ?>"
        >

        <label>Ngày hẹn</label>
        <input 
            type="date" 
            name="date"
            value="<?= htmlspecialchars($_GET["date"] ?? "") ?>"
        >

        <label>Trạng thái</label>
        <select name="status">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="new" <?= ($_GET["status"] ?? "") === "new" ? "selected" : "" ?>>Mới</option>
            <option value="confirmed" <?= ($_GET["status"] ?? "") === "confirmed" ? "selected" : "" ?>>Đã xác nhận</option>
            <option value="completed" <?= ($_GET["status"] ?? "") === "completed" ? "selected" : "" ?>>Hoàn thành</option>
            <option value="cancelled" <?= ($_GET["status"] ?? "") === "cancelled" ? "selected" : "" ?>>Đã hủy</option>
        </select>

        <label>Nhân viên</label>
        <select name="staff_id">
            <option value="">-- Tất cả nhân viên --</option>
            <?php foreach ($staffList as $staff): ?>
                <option 
                    value="<?= $staff["id"] ?>"
                    <?= ($_GET["staff_id"] ?? "") == $staff["id"] ? "selected" : "" ?>
                >
                    <?= htmlspecialchars($staff["name"]) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Lọc lịch hẹn</button>

        <a href="appointments.php">Xóa bộ lọc</a>
    </form>
    <p>
    Tìm thấy <strong><?= count($appointments) ?></strong> lịch hẹn.
</p>
</div>

        <table>
    <tr>
        <th>Khách hàng</th>
        <th>SĐT</th>
        <th>Dịch vụ</th>
        <th>Giá</th>
        <th>Nhân viên</th>
        <th>Ngày</th>
        <th>Giờ</th>
        <th>Trạng thái</th>
        <th>Thanh toán</th>
        <th>Ghi chú</th>
        <th>Thao tác</th>
    </tr>

    <?php foreach ($appointments as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item["customer_name"]) ?></td>
            <td><?= htmlspecialchars($item["customer_phone"]) ?></td>
            <td><?= htmlspecialchars($item["service_name"]) ?></td>
            <td><?= number_format($item["service_price"]) ?>đ</td>
            <td><?= htmlspecialchars($item["staff_name"]) ?></td>
            <td><?= htmlspecialchars($item["appointment_date"]) ?></td>
            <td><?= htmlspecialchars(substr($item["appointment_time"], 0, 5)) ?></td>

            <td>
                <?php
                $statusText = [
                    "new" => "Mới",
                    "confirmed" => "Đã xác nhận",
                    "completed" => "Hoàn thành",
                    "cancelled" => "Đã hủy"
                ];

                echo $statusText[$item["status"]] ?? $item["status"];
                ?>
            </td>

            <td>
                <?= (($item["payment_status"] ?? "unpaid") === "paid")
                    ? "Đã thanh toán"
                    : "Chưa thanh toán" ?>
            </td>

            <td><?= htmlspecialchars($item["note"] ?? "") ?></td>

            <td>
                <a href="edit_appointment.php?id=<?= $item["id"] ?>">Sửa</a>
                |
                <a href="../actions/update_appointment_status.php?id=<?= $item["id"] ?>&status=confirmed">
                    Xác nhận
                </a>
                |
                <a href="../actions/update_appointment_status.php?id=<?= $item["id"] ?>&status=completed">
                    Hoàn thành
                </a>
                |
                <a href="../actions/update_appointment_status.php?id=<?= $item["id"] ?>&status=cancelled">
                    Hủy
                </a>
                |
                <a href="payment.php?id=<?= $item["id"] ?>">
                    Thanh toán
                </a>
                |
                <a
                    href="../actions/delete_appointment.php?id=<?= $item["id"] ?>"
                    onclick="return confirm('Bạn có chắc muốn xóa lịch hẹn này không?')"
                >
                    Xóa
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

    </main>
</div>

</body>
</html>