<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id <= 0) {
    header("Location: nail_gallery.php?error=Thiếu ID mẫu nail");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM nail_gallery WHERE id = ?");
$stmt->execute([$id]);
$nail = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$nail) {
    header("Location: nail_gallery.php?error=Không tìm thấy mẫu nail");
    exit;
}

$error = $_GET["error"] ?? "";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Timmo - Sửa mẫu nail</title>
    <link rel="stylesheet" href="../assets/style.css">

    <style>
        .preview-image {
            width: 260px;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin: 12px 0;
            display: block;
        }

        .btn-back {
            display: inline-block;
            margin-bottom: 16px;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="layout">
    <?php include "../includes/sidebar.php"; ?>

    <main class="content">
        <a class="btn-back" href="nail_gallery.php">← Quay lại Gallery</a>

        <h1>Sửa mẫu nail</h1>

        <?php if ($error): ?>
            <p style="color: red; font-weight: bold;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <div class="form-box">
            <form action="../actions/update_nail.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= (int)$nail["id"] ?>">

                <label>Tên mẫu</label>
                <input
                    type="text"
                    name="title"
                    value="<?= htmlspecialchars($nail["title"]) ?>"
                    required
                >

                <label>Ảnh hiện tại</label>
                <img
                    class="preview-image"
                    src="../<?= htmlspecialchars($nail["image_path"]) ?>"
                    alt="<?= htmlspecialchars($nail["title"]) ?>"
                >

                <label>Đổi ảnh mới nếu muốn</label>
                <input type="file" name="image" accept="image/*">

                <label>Ghi chú</label>
                <textarea name="note" rows="5"><?= htmlspecialchars($nail["note"] ?? "") ?></textarea>

                <button type="submit">Cập nhật</button>
            </form>
        </div>
    </main>
</div>

</body>
</html>