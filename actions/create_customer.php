<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/customers.php");
    exit;
}

$name = trim($_POST["name"]);
$phone = trim($_POST["phone"]);
$email = trim($_POST["email"] ?? "");
$note = trim($_POST["note"] ?? "");

if ($name === "" || $phone === "") {
    header("Location: ../pages/customers.php?error=Tên và số điện thoại không được để trống");
    exit;
}

$check = $pdo->prepare("SELECT COUNT(*) FROM customers WHERE phone = ?");
$check->execute([$phone]);

if ($check->fetchColumn() > 0) {
    header("Location: ../pages/customers.php?error=Số điện thoại này đã tồn tại");
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO customers (name, phone, email, note)
    VALUES (?, ?, ?, ?)
");

$stmt->execute([
    $name,
    $phone,
    $email,
    $note
]);

header("Location: ../pages/customers.php?success=Thêm khách hàng thành công");
exit;