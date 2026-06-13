<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

$id = $_GET["id"] ?? null;
$status = $_GET["status"] ?? null;

if (!$id || !in_array($status, ["active", "inactive"])) {
    header("Location: ../pages/services.php");
    exit;
}

$stmt = $pdo->prepare("UPDATE services SET status = ? WHERE id = ?");
$stmt->execute([$status, $id]);

header("Location: ../pages/services.php?success=Cập nhật trạng thái dịch vụ thành công");
exit;