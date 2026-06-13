<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireRole(["admin"]);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/users.php");
    exit;
}

$name = trim($_POST["name"]);
$email = trim($_POST["email"]);
$password = $_POST["password"];
$role = $_POST["role"];

if ($name === "" || $email === "" || $password === "") {
    header("Location: ../pages/users.php?error=Vui lòng nhập đầy đủ thông tin");
    exit;
}

if (!in_array($role, ["admin", "reception", "staff"])) {
    header("Location: ../pages/users.php?error=Quyền không hợp lệ");
    exit;
}

$check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
$check->execute([$email]);

if ($check->fetchColumn() > 0) {
    header("Location: ../pages/users.php?error=Email đã tồn tại");
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
    INSERT INTO users (name, email, password, role, status)
    VALUES (?, ?, ?, ?, 'active')
");

$stmt->execute([$name, $email, $hash, $role]);

header("Location: ../pages/users.php?success=Tạo tài khoản thành công");
exit;