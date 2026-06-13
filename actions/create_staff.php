<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/staff.php");
    exit;
}

$name = trim($_POST["name"]);
$phone = trim($_POST["phone"] ?? "");
$role = trim($_POST["role"] ?? "");

if ($name === "") {
    header("Location: ../pages/staff.php?error=Tên nhân viên không được để trống");
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO staff (name, phone, role, status)
    VALUES (?, ?, ?, 'active')
");

$stmt->execute([
    $name,
    $phone,
    $role
]);

header("Location: ../pages/staff.php?success=Thêm nhân viên thành công");
exit;