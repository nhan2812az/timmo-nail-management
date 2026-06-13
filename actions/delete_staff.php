<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

$id = $_GET["id"] ?? null;

if (!$id) {
    header("Location: ../pages/staff.php");
    exit;
}

$check = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE staff_id = ?");
$check->execute([$id]);

if ($check->fetchColumn() > 0) {
    header("Location: ../pages/staff.php?error=Không thể xóa nhân viên đã có lịch hẹn");
    exit;
}

$stmt = $pdo->prepare("DELETE FROM staff WHERE id = ?");
$stmt->execute([$id]);

header("Location: ../pages/staff.php?success=Xóa nhân viên thành công");
exit;