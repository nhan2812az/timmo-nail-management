<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id <= 0) {
    header("Location: ../pages/nail_gallery.php?error=Thiếu ID mẫu nail");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM nail_gallery WHERE id = ?");
    $stmt->execute([$id]);
    $nail = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$nail) {
        header("Location: ../pages/nail_gallery.php?error=Không tìm thấy mẫu nail");
        exit;
    }

    if (!empty($nail["image_path"])) {
        $filePath = "../" . $nail["image_path"];

        if (file_exists($filePath) && is_file($filePath)) {
            unlink($filePath);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM nail_gallery WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: ../pages/nail_gallery.php?success=Đã xóa mẫu nail thành công");
    exit;

} catch (Exception $e) {
    header("Location: ../pages/nail_gallery.php?error=Không thể xóa mẫu nail");
    exit;
}