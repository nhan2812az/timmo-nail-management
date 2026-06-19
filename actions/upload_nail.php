<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

$title = $_POST["title"] ?? "";
$note = $_POST["note"] ?? "";

if ($title === "") {
    header("Location: ../pages/nail_gallery.php?error=Thiếu tên mẫu nail");
    exit;
}

if (!isset($_FILES["image"]) || $_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
    header("Location: ../pages/nail_gallery.php?error=Upload ảnh thất bại");
    exit;
}

$allowedTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];

if (!in_array($_FILES["image"]["type"], $allowedTypes)) {
    header("Location: ../pages/nail_gallery.php?error=Chỉ cho phép JPG, PNG, GIF, WEBP");
    exit;
}

$uploadDir = "../uploads/nails/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
$fileName = time() . "_" . uniqid() . "." . $extension;
$targetPath = $uploadDir . $fileName;

if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
    header("Location: ../pages/nail_gallery.php?error=Không thể lưu ảnh");
    exit;
}

$imagePath = "uploads/nails/" . $fileName;

$stmt = $pdo->prepare("
    INSERT INTO nail_gallery (title, image_path, note)
    VALUES (?, ?, ?)
");
$stmt->execute([$title, $imagePath, $note]);

header("Location: ../pages/nail_gallery.php?success=Upload mẫu nail thành công");
exit;