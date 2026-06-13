<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

$services = $pdo->query("
    SELECT * FROM services 
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$success = $_GET["success"] ?? "";
$error = $_GET["error"] ?? "";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Timmo - Dịch vụ</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="layout">
    <?php include "../includes/sidebar.php"; ?>

    <main class="content">
        <h1>Quản lý dịch vụ</h1>

        <?php if ($success): ?>
            <p style="color: green; font-weight: bold;"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p style="color: red; font-weight: bold;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <div class="form-box">
            <h2>Thêm dịch vụ mới</h2>

            <form action="../actions/create_service.php" method="POST">
                <label>Tên dịch vụ</label>
                <input type="text" name="name" placeholder="Ví dụ: Sơn gel" required>

                <label>Giá tiền</label>
                <input type="number" name="price" placeholder="Ví dụ: 150000" min="0" required>

                <label>Thời lượng làm dịch vụ</label>
                <input type="number" name="duration" placeholder="Ví dụ: 45" min="1" required>

                <button type="submit">Thêm dịch vụ</button>
            </form>
        </div>

        <h2>Danh sách dịch vụ</h2>

        <table>
            <tr>
                <th>Tên dịch vụ</th>
                <th>Giá</th>
                <th>Thời lượng</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>

            <?php foreach ($services as $service): ?>
                <tr>
                    <td><?= htmlspecialchars($service["name"]) ?></td>
                    <td><?= number_format($service["price"]) ?>đ</td>
                    <td><?= htmlspecialchars($service["duration"]) ?> phút</td>
                    <td>
                        <?php if ($service["status"] === "active"): ?>
                            Đang hoạt động
                        <?php else: ?>
                            Đã tắt
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_service.php?id=<?= $service["id"] ?>">Sửa</a>
|
                        <?php if ($service["status"] === "active"): ?>
                            <a href="../actions/toggle_service.php?id=<?= $service["id"] ?>&status=inactive">
                                Tắt
                            </a>
                        <?php else: ?>
                            <a href="../actions/toggle_service.php?id=<?= $service["id"] ?>&status=active">
                                Bật
                            </a>
                        <?php endif; ?>

                        |
                        
                        <a 
                            href="../actions/delete_service.php?id=<?= $service["id"] ?>"
                            onclick="return confirm('Xóa dịch vụ này?')"
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