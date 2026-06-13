<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/customers.php");
    exit;
}

$id = $_POST["id"];
$name = trim($_POST["name"]);
$phone = trim($_POST["phone"]);
$email = trim($_POST["email"] ?? "");
$note = trim($_POST["note"] ?? "");

if ($name === "" || $phone === "") {
    header("Location: ../pages/customers.php?error=Tên và số điện thoại không được để trống");
    exit;
}

$check = $pdo->prepare("
    SELECT COUNT(*) 
    FROM customers 
    WHERE phone = ? AND id != ?
");
$check->execute([$phone, $id]);

if ($check->fetchColumn() > 0) {
    header("Location: ../pages/edit_customer.php?id=$id&error=Số điện thoại này đã thuộc khách hàng khác");
    exit;
}

$stmt = $pdo->prepare("
    UPDATE customers
    SET name = ?, phone = ?, email = ?, note = ?
    WHERE id = ?
");

$stmt->execute([
    $name,
    $phone,
    $email,
    $note,
    $id
]);

header("Location: ../pages/customers.php?success=Cập nhật khách hàng thành công");
exit;