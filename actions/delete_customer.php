<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

$id = $_GET["id"] ?? null;

if (!$id) {
    header("Location: ../pages/customers.php");
    exit;
}

$check = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE customer_id = ?");
$check->execute([$id]);

if ($check->fetchColumn() > 0) {
    header("Location: ../pages/customers.php?error=Không thể xóa khách hàng đã có lịch hẹn");
    exit;
}

$stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
$stmt->execute([$id]);

header("Location: ../pages/customers.php?success=Xóa khách hàng thành công");
exit;