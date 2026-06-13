<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

$id = $_GET["id"] ?? null;

if (!$id) {
    header("Location: services.php?error=Không tìm thấy dịch vụ");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
$stmt->execute([$id]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    header("Location: services.php?error=Dịch vụ không tồn tại");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa dịch vụ - Timmo</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="layout">
    <?php include "../includes/sidebar.php"; ?>

    <main class="content">
        <h1>Sửa dịch vụ</h1>

        <div class="form-box">
            <form action="../actions/update_service.php" method="POST">
                <input type="hidden" name="id" value="<?= $service["id"] ?>">

                <label>Tên dịch vụ</label>
                <input type="text" name="name" value="<?= htmlspecialchars($service["name"]) ?>" required>

                <label>Giá tiền</label>
                <input type="number" name="price" value="<?= htmlspecialchars($service["price"]) ?>" min="0" required>

                <label>Thời lượng</label>
                <input type="number" name="duration" value="<?= htmlspecialchars($service["duration"]) ?>" min="1" required>

                <label>Trạng thái</label>
                <select name="status" required>
                    <option value="active" <?= $service["status"] === "active" ? "selected" : "" ?>>Đang hoạt động</option>
                    <option value="inactive" <?= $service["status"] === "inactive" ? "selected" : "" ?>>Đã tắt</option>
                </select>

                <button type="submit">Lưu thay đổi</button>
            </form>
        </div>
    </main>
</div>

</body>
</html>