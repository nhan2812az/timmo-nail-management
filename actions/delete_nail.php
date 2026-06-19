<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

$id = $_GET["id"] ?? null;

if (!$id) {
    header("Location: ../pages/nail_gallery.php?error=Thiếu ID mẫu nail");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM nail_gallery WHERE id = ?");
$stmt->execute([$id]);
$nail = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$nail) {
    header("Location: ../pages/nail_gallery.php?error=Không tìm thấy mẫu nail");
    exit;
}

$filePath = "../" . $nail["image_path"];

if (file_exists($filePath)) {
    unlink($filePath);
}

$stmt = $pdo->prepare("DELETE FROM nail_gallery WHERE id = ?");
$stmt->execute([$id]);

header("Location: ../pages/nail_gallery.php?success=Đã xóa mẫu nail");
exit;