<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

$keyword = $_GET["keyword"] ?? "";

if ($keyword !== "") {
    $stmt = $pdo->prepare("
        SELECT * FROM customers
        WHERE name LIKE ? OR phone LIKE ? OR email LIKE ?
        ORDER BY created_at DESC
    ");

    $search = "%" . $keyword . "%";
    $stmt->execute([$search, $search, $search]);
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $customers = $pdo->query("
        SELECT * FROM customers 
        ORDER BY created_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
}

$success = $_GET["success"] ?? "";
$error = $_GET["error"] ?? "";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Timmo - Khách hàng</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="layout">
   <?php include "../includes/sidebar.php"; ?>

    <main class="content">
        <h1>Quản lý khách hàng</h1>

        <?php if ($success): ?>
            <p style="color: green; font-weight: bold;"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p style="color: red; font-weight: bold;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <div class="form-box">
            <h2>Thêm khách hàng mới</h2>

            <form action="../actions/create_customer.php" method="POST">
                <label>Tên khách hàng</label>
                <input type="text" name="name" placeholder="Ví dụ: Nguyễn Thị Mai" required>

                <label>Số điện thoại</label>
                <input type="text" name="phone" placeholder="Ví dụ: 0912345678" required>

                <label>Email</label>
                <input type="email" name="email" placeholder="Ví dụ: mai@gmail.com">

                <label>Ghi chú</label>
                <textarea name="note" placeholder="Ví dụ: khách quen, thích màu hồng..."></textarea>

                <button type="submit">Thêm khách hàng</button>
            </form>
        </div>

        <h2>Danh sách khách hàng</h2>
        <div class="form-box">
    <h2>Tìm kiếm khách hàng</h2>

    <form method="GET">
        <input 
            type="text" 
            name="keyword" 
            placeholder="Nhập tên, số điện thoại hoặc email"
            value="<?= htmlspecialchars($keyword) ?>"
        >

        <button type="submit">Tìm kiếm</button>

        <a href="customers.php">Xóa tìm kiếm</a>
    </form>
</div>

<p>
    Tìm thấy <strong><?= count($customers) ?></strong> khách hàng.
</p>

        <table>
            <tr>
                <th>Tên</th>
                <th>Số điện thoại</th>
                <th>Email</th>
                <th>Ghi chú</th>
                <th>Ngày tạo</th>
                <th>Thao tác</th>
            </tr>

            <?php foreach ($customers as $customer): ?>
                <tr>
                    <td><?= htmlspecialchars($customer["name"]) ?></td>
                    <td><?= htmlspecialchars($customer["phone"]) ?></td>
                    <td><?= htmlspecialchars($customer["email"]) ?></td>
                    <td><?= htmlspecialchars($customer["note"]) ?></td>
                    <td><?= htmlspecialchars($customer["created_at"]) ?></td>
                    <td>
    <a href="customer_history.php?id=<?= $customer["id"] ?>">Lịch sử</a>
    |
    <a href="customer_analytics.php?id=<?= $customer["id"] ?>">Thống kê</a>
    |
    <a href="edit_customer.php?id=<?= $customer["id"] ?>">Sửa</a>
    |
    <a 
        href="../actions/delete_customer.php?id=<?= $customer["id"] ?>"
        onclick="return confirm('Xóa khách hàng này? Các lịch hẹn liên quan có thể bị ảnh hưởng.')"
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