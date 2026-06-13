<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

$id = $_GET["id"] ?? null;

if (!$id) {
    header("Location: staff.php?error=Không tìm thấy nhân viên");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM staff WHERE id = ?");
$stmt->execute([$id]);
$staff = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$staff) {
    header("Location: staff.php?error=Nhân viên không tồn tại");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa nhân viên - Timmo</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="layout">
   <?php include "../includes/sidebar.php"; ?>

    <main class="content">
        <h1>Sửa nhân viên</h1>

        <div class="form-box">
            <form action="../actions/update_staff.php" method="POST">
                <input type="hidden" name="id" value="<?= $staff["id"] ?>">

                <label>Tên nhân viên</label>
                <input type="text" name="name" value="<?= htmlspecialchars($staff["name"]) ?>" required>

                <label>Số điện thoại</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($staff["phone"]) ?>">

                <label>Vai trò</label>
                <input type="text" name="role" value="<?= htmlspecialchars($staff["role"]) ?>">

                <label>Trạng thái</label>
                <select name="status" required>
                    <option value="active" <?= $staff["status"] === "active" ? "selected" : "" ?>>
                        Đang làm việc
                    </option>
                    <option value="inactive" <?= $staff["status"] === "inactive" ? "selected" : "" ?>>
                        Đã nghỉ / tạm tắt
                    </option>
                </select>

                <button type="submit">Lưu thay đổi</button>
            </form>
        </div>
    </main>
</div>

</body>
</html>