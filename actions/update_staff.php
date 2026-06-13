<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/staff.php");
    exit;
}

$id = $_POST["id"];
$name = trim($_POST["name"]);
$phone = trim($_POST["phone"] ?? "");
$role = trim($_POST["role"] ?? "");
$status = $_POST["status"];

if ($name === "" || !in_array($status, ["active", "inactive"])) {
    header("Location: ../pages/edit_staff.php?id=$id&error=Dữ liệu nhân viên không hợp lệ");
    exit;
}

$stmt = $pdo->prepare("
    UPDATE staff
    SET name = ?, phone = ?, role = ?, status = ?
    WHERE id = ?
");

$stmt->execute([
    $name,
    $phone,
    $role,
    $status,
    $id
]);

header("Location: ../pages/staff.php?success=Cập nhật nhân viên thành công");
exit;