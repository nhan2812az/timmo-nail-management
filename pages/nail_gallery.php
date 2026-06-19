<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

$nails = $pdo->query("
    SELECT *
    FROM nail_gallery
    ORDER BY created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

$success = $_GET["success"] ?? "";
$error = $_GET["error"] ?? "";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Timmo - Mẫu nail</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="layout">
    <?php include "../includes/sidebar.php"; ?>

    <main class="content">
        <h1>Mẫu nail</h1>

        <?php if ($success): ?>
            <p style="color: green; font-weight: bold;"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p style="color: red; font-weight: bold;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <div class="form-box">
            <h2>Upload mẫu nail mới</h2>

            <form action="../actions/upload_nail.php" method="POST" enctype="multipart/form-data">
                <label>Tên mẫu</label>
                <input type="text" name="title" required>

                <label>Ảnh mẫu nail</label>
                <input type="file" name="image" accept="image/*" required>

                <label>Ghi chú</label>
                <textarea name="note"></textarea>

                <button type="submit">Upload</button>
            </form>
        </div>

        <h2>Danh sách mẫu nail</h2>

        <div class="cards">
            <?php if (empty($nails)): ?>
                <div class="card">
                    <p>Chưa có mẫu nail nào.</p>
                </div>
            <?php else: ?>
                <?php foreach ($nails as $nail): ?>
                    <div class="card">
                        <img 
                            src="../<?= htmlspecialchars($nail["image_path"]) ?>" 
                            alt="<?= htmlspecialchars($nail["title"]) ?>"
                            style="width:100%; height:180px; object-fit:cover; border-radius:8px;"
                        >

                        <h3><?= htmlspecialchars($nail["title"]) ?></h3>
                        <p><?= htmlspecialchars($nail["note"]) ?></p>

                        <a 
                            href="../actions/delete_nail.php?id=<?= $nail["id"] ?>"
                            onclick="return confirm('Xóa mẫu nail này?')"
                        >
                            Xóa
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</div>

</body>
</html>