<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

$id = $_GET["id"] ?? null;
$status = $_GET["status"] ?? null;

$allowedStatus = ["new", "confirmed", "completed", "cancelled"];

if (!$id || !in_array($status, $allowedStatus)) {
    header("Location: ../pages/appointments.php?error=Trạng thái không hợp lệ");
    exit;
}

$stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ?");
$stmt->execute([$status, $id]);

header("Location: ../pages/appointments.php?success=Cập nhật trạng thái lịch hẹn thành công");
exit;