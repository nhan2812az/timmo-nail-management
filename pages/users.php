<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireRole(["admin"]);

$users = $pdo->query("
    SELECT * FROM users
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$success = $_GET["success"] ?? "";
$error = $_GET["error"] ?? "";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý tài khoản - Timmo</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="layout">
    <?php require_once __DIR__ . "/../includes/sidebar.php"; ?>

    <main class="content">
        <h1>Quản lý tài khoản</h1>

        <?php if ($success): ?>
            <p style="color:green;font-weight:bold;"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p style="color:red;font-weight:bold;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <div class="form-box">
            <h2>Thêm tài khoản mới</h2>

            <form action="../actions/create_user.php" method="POST">
                <label>Họ tên</label>
                <input type="text" name="name" required>

                <label>Email</label>
                <input type="email" name="email" required>

                <label>Mật khẩu</label>
                <input type="password" name="password" required>

                <label>Quyền</label>
                <select name="role" required>
                    <option value="admin">Admin</option>
                    <option value="reception">Lễ tân</option>
                    <option value="staff">Nhân viên</option>
                </select>

                <button type="submit">Tạo tài khoản</button>
            </form>
        </div>

        <h2>Danh sách tài khoản</h2>

        <table>
            <tr>
                <th>Tên</th>
                <th>Email</th>
                <th>Quyền</th>
                <th>Trạng thái</th>
            </tr>

            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user["name"]) ?></td>
                    <td><?= htmlspecialchars($user["email"]) ?></td>
                    <td><?= htmlspecialchars($user["role"]) ?></td>
                    <td><?= htmlspecialchars($user["status"]) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </main>
</div>

</body>
</html>