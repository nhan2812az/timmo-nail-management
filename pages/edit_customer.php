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
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa khách hàng - Timmo</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="layout">
   <?php include "../includes/sidebar.php"; ?>

    <main class="content">
        <h1>Sửa khách hàng</h1>

        <div class="form-box">
            <form action="../actions/update_customer.php" method="POST">
                <input type="hidden" name="id" value="<?= $customer["id"] ?>">

                <label>Tên khách hàng</label>
                <input type="text" name="name" value="<?= htmlspecialchars($customer["name"]) ?>" required>

                <label>Số điện thoại</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($customer["phone"]) ?>" required>

                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($customer["email"]) ?>">

                <label>Ghi chú</label>
                <textarea name="note"><?= htmlspecialchars($customer["note"]) ?></textarea>

                <button type="submit">Lưu thay đổi</button>
            </form>
        </div>
    </main>
</div>

</body>
</html>