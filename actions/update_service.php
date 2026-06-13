<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/services.php");
    exit;
}

$id = $_POST["id"];
$name = trim($_POST["name"]);
$price = $_POST["price"];
$duration = $_POST["duration"];
$status = $_POST["status"];

if ($name === "" || $price < 0 || $duration <= 0 || !in_array($status, ["active", "inactive"])) {
    header("Location: ../pages/edit_service.php?id=$id&error=Dữ liệu không hợp lệ");
    exit;
}

$stmt = $pdo->prepare("
    UPDATE services
    SET name = ?, price = ?, duration = ?, status = ?
    WHERE id = ?
");

$stmt->execute([
    $name,
    $price,
    $duration,
    $status,
    $id
]);

header("Location: ../pages/services.php?success=Cập nhật dịch vụ thành công");
exit;