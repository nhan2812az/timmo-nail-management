<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

$id = $_GET["id"] ?? null;
$status = $_GET["status"] ?? null;

if (!$id || !in_array($status, ["active", "inactive"])) {
    header("Location: ../pages/staff.php");
    exit;
}

$stmt = $pdo->prepare("UPDATE staff SET status = ? WHERE id = ?");
$stmt->execute([$status, $id]);

header("Location: ../pages/staff.php?success=Cập nhật trạng thái nhân viên thành công");
exit;