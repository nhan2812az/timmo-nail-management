<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

$id = $_GET["id"] ?? null;

if (!$id) {
    header("Location: ../pages/services.php");
    exit;
}

$check = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE service_id = ?");
$check->execute([$id]);

if ($check->fetchColumn() > 0) {
    header("Location: ../pages/services.php?error=Không thể xóa dịch vụ đã có lịch hẹn");
    exit;
}

$stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
$stmt->execute([$id]);

header("Location: ../pages/services.php?success=Xóa dịch vụ thành công");
exit;