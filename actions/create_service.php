<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/services.php");
    exit;
}

$name = trim($_POST["name"]);
$price = $_POST["price"];
$duration = $_POST["duration"];

if ($name === "" || $price < 0 || $duration <= 0) {
    header("Location: ../pages/services.php?error=Dữ liệu dịch vụ không hợp lệ");
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO services (name, price, duration, status)
    VALUES (?, ?, ?, 'active')
");

$stmt->execute([
    $name,
    $price,
    $duration
]);

header("Location: ../pages/services.php?success=Thêm dịch vụ thành công");
exit;