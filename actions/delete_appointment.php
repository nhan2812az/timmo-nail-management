<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

$id = $_GET["id"] ?? null;

if (!$id) {
    header("Location: ../pages/appointments.php");
    exit;
}

$stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
$stmt->execute([$id]);

header("Location: ../pages/appointments.php?success=Xóa lịch hẹn thành công");
exit;