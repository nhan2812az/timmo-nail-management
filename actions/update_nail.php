<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

$id = isset($_POST["id"]) ? (int)$_POST["id"] : 0;
$title = trim($_POST["title"] ?? "");
$note = trim($_POST["note"] ?? "");

if ($id <= 0) {
    header("Location: ../pages/nail_gallery.php?error=Thiếu ID mẫu nail");
    exit;
}

if ($title === "") {
    header("Location: ../pages/edit_nail.php?id=" . $id . "&error=Thiếu tên mẫu nail");
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

    $imagePath = $nail["image_path"];

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
        $allowedTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];

        if (!in_array($_FILES["image"]["type"], $allowedTypes)) {
            header("Location: ../pages/edit_nail.php?id=" . $id . "&error=Chỉ cho phép JPG, PNG, GIF, WEBP");
            exit;
        }

        $uploadDir = "../uploads/nails/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $fileName = time() . "_" . uniqid() . "." . $extension;
        $targetPath = $uploadDir . $fileName;

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
            header("Location: ../pages/edit_nail.php?id=" . $id . "&error=Không thể lưu ảnh mới");
            exit;
        }

        if (!empty($nail["image_path"])) {
            $oldFilePath = "../" . $nail["image_path"];

            if (file_exists($oldFilePath) && is_file($oldFilePath)) {
                unlink($oldFilePath);
            }
        }

        $imagePath = "uploads/nails/" . $fileName;
    }

    $stmt = $pdo->prepare("
        UPDATE nail_gallery
        SET title = ?, note = ?, image_path = ?
        WHERE id = ?
    ");
    $stmt->execute([$title, $note, $imagePath, $id]);

    header("Location: ../pages/nail_gallery.php?success=Cập nhật mẫu nail thành công");
    exit;

} catch (Exception $e) {
    header("Location: ../pages/edit_nail.php?id=" . $id . "&error=Không thể cập nhật mẫu nail");
    exit;
}