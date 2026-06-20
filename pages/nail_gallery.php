<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

$success = $_GET["success"] ?? "";
$error = $_GET["error"] ?? "";
$search = trim($_GET["search"] ?? "");

if ($search !== "") {
    $stmt = $pdo->prepare("
        SELECT *
        FROM nail_gallery
        WHERE title LIKE ? OR note LIKE ?
        ORDER BY created_at DESC
    ");
    $stmt->execute(["%$search%", "%$search%"]);
    $nails = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $nails = $pdo->query("
        SELECT *
        FROM nail_gallery
        ORDER BY created_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Timmo - Mẫu nail</title>
    <link rel="stylesheet" href="../assets/style.css">

    <style>
        .gallery-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
        }

        .search-box {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
        }

        .search-box input {
            max-width: 320px;
        }

        .nail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .nail-card {
            background: #fff;
            border-radius: 12px;
            padding: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .nail-card img {
            width: 100%;
            height: 190px;
            object-fit: cover;
            border-radius: 10px;
            cursor: pointer;
            background: #f3f3f3;
        }

        .nail-card h3 {
            margin: 12px 0 6px;
        }

        .nail-card p {
            color: #666;
            min-height: 40px;
        }

        .nail-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 12px;
        }

        .btn-small {
            display: inline-block;
            padding: 8px 10px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }

        .btn-view {
            background: #e8f0ff;
            color: #1a56db;
        }

        .btn-edit {
            background: #fff4d6;
            color: #946200;
        }

        .btn-delete {
            background: #ffe2e2;
            color: #c62828;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            inset: 0;
            background: rgba(0,0,0,0.75);
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .modal img {
            max-width: 90%;
            max-height: 85vh;
            border-radius: 12px;
            background: white;
        }

        .modal-close {
            position: fixed;
            top: 24px;
            right: 32px;
            color: white;
            font-size: 36px;
            cursor: pointer;
            font-weight: bold;
        }

        .empty-box {
            background: #fff;
            padding: 24px;
            border-radius: 12px;
            text-align: center;
            color: #666;
        }
    </style>
</head>
<body>

<div class="layout">
    <?php include "../includes/sidebar.php"; ?>

    <main class="content">
        <div class="gallery-header">
            <h1>Mẫu nail</h1>
        </div>

        <?php if ($success): ?>
            <p style="color: green; font-weight: bold;">
                <?= htmlspecialchars($success) ?>
            </p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p style="color: red; font-weight: bold;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <div class="form-box">
            <h2>Upload mẫu nail mới</h2>

            <form action="../actions/upload_nail.php" method="POST" enctype="multipart/form-data">
                <label>Tên mẫu</label>
                <input type="text" name="title" required>

                <label>Ảnh mẫu nail</label>
                <input type="file" name="image" accept="image/*" required>

                <label>Ghi chú</label>
                <textarea name="note" rows="4"></textarea>

                <button type="submit">Upload</button>
            </form>
        </div>

        <h2>Danh sách mẫu nail</h2>

        <form class="search-box" method="GET">
            <input
                type="text"
                name="search"
                placeholder="Tìm theo tên hoặc ghi chú..."
                value="<?= htmlspecialchars($search) ?>"
            >
            <button type="submit">Tìm kiếm</button>

            <?php if ($search !== ""): ?>
                <a class="btn-small btn-view" href="nail_gallery.php">Xóa lọc</a>
            <?php endif; ?>
        </form>

        <?php if (empty($nails)): ?>
            <div class="empty-box">
                <p>Chưa có mẫu nail nào.</p>
            </div>
        <?php else: ?>
            <div class="nail-grid">
                <?php foreach ($nails as $nail): ?>
                    <div class="nail-card">
                        <img
                            src="../<?= htmlspecialchars($nail["image_path"]) ?>"
                            alt="<?= htmlspecialchars($nail["title"]) ?>"
                            onclick="openImageModal('../<?= htmlspecialchars($nail["image_path"]) ?>')"
                        >

                        <h3><?= htmlspecialchars($nail["title"]) ?></h3>

                        <p>
                            <?= nl2br(htmlspecialchars($nail["note"] ?? "")) ?>
                        </p>

                        <small>
                            Ngày tạo:
                            <?= htmlspecialchars(date("d/m/Y H:i", strtotime($nail["created_at"]))) ?>
                        </small>

                        <div class="nail-actions">
                            <button
                                type="button"
                                class="btn-small btn-view"
                                onclick="openImageModal('../<?= htmlspecialchars($nail["image_path"]) ?>')"
                            >
                                Xem ảnh
                            </button>

                            <a
                                class="btn-small btn-edit"
                                href="edit_nail.php?id=<?= (int)$nail["id"] ?>"
                            >
                                Sửa
                            </a>

                            <a
                                class="btn-small btn-delete"
                                href="../actions/delete_nail.php?id=<?= (int)$nail["id"] ?>"
                                onclick="return confirm('Bạn có chắc muốn xóa mẫu nail này?')"
                            >
                                Xóa
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<div class="modal" id="imageModal" onclick="closeImageModal()">
    <span class="modal-close">&times;</span>
    <img id="modalImage" src="" alt="Preview">
</div>

<script>
    function openImageModal(src) {
        document.getElementById("modalImage").src = src;
        document.getElementById("imageModal").style.display = "flex";
    }

    function closeImageModal() {
        document.getElementById("imageModal").style.display = "none";
        document.getElementById("modalImage").src = "";
    }
</script>

</body>
</html>