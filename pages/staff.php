<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

$staffList = $pdo->query("
    SELECT * FROM staff 
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$success = $_GET["success"] ?? "";
$error = $_GET["error"] ?? "";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Timmo - Nhân viên</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="layout">
    <?php include "../includes/sidebar.php"; ?>

    <main class="content">
        <h1>Quản lý nhân viên</h1>

        <?php if ($success): ?>
            <p style="color: green; font-weight: bold;"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p style="color: red; font-weight: bold;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <div class="form-box">
            <h2>Thêm nhân viên mới</h2>

            <form action="../actions/create_staff.php" method="POST">
                <label>Tên nhân viên</label>
                <input type="text" name="name" placeholder="Ví dụ: Linh" required>

                <label>Số điện thoại</label>
                <input type="text" name="phone" placeholder="Ví dụ: 0900000001">

                <label>Vai trò</label>
                <input type="text" name="role" placeholder="Ví dụ: Nail Artist">

                <button type="submit">Thêm nhân viên</button>
            </form>
        </div>

        <h2>Danh sách nhân viên</h2>

        <table>
            <tr>
                <th>Tên</th>
                <th>Số điện thoại</th>
                <th>Vai trò</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>

            <?php foreach ($staffList as $staff): ?>
                <tr>
                    <td><?= htmlspecialchars($staff["name"]) ?></td>
                    <td><?= htmlspecialchars($staff["phone"]) ?></td>
                    <td><?= htmlspecialchars($staff["role"]) ?></td>
                    <td>
                        <?php if ($staff["status"] === "active"): ?>
                            Đang làm việc
                        <?php else: ?>
                            Đã nghỉ / tạm tắt
                        <?php endif; ?>
                    </td>
                    <td>
    <a href="edit_staff.php?id=<?= $staff["id"] ?>">Sửa</a>
    |

    <?php if ($staff["status"] === "active"): ?>
        <a href="../actions/toggle_staff.php?id=<?= $staff["id"] ?>&status=inactive">
            Tắt
        </a>
    <?php else: ?>
        <a href="../actions/toggle_staff.php?id=<?= $staff["id"] ?>&status=active">
            Bật
        </a>
    <?php endif; ?>

    |

    <a 
        href="../actions/delete_staff.php?id=<?= $staff["id"] ?>"
        onclick="return confirm('Xóa nhân viên này?')"
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