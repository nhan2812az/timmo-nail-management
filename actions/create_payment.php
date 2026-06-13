<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/appointments.php");
    exit;
}

$appointment_id = $_POST["appointment_id"];
$amount = $_POST["amount"];
$payment_method = $_POST["payment_method"];
$note = trim($_POST["note"] ?? "");

if ($amount <= 0) {
    header("Location: ../pages/payment.php?id=$appointment_id&error=Số tiền không hợp lệ");
    exit;
}

$check = $pdo->prepare("
    SELECT payment_status 
    FROM appointments 
    WHERE id = ?
");
$check->execute([$appointment_id]);
$appointment = $check->fetch(PDO::FETCH_ASSOC);

if (!$appointment) {
    header("Location: ../pages/appointments.php?error=Lịch hẹn không tồn tại");
    exit;
}

if ($appointment["payment_status"] === "paid") {
    header("Location: ../pages/appointments.php?error=Lịch hẹn này đã thanh toán rồi");
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO payments 
    (appointment_id, amount, payment_method, note)
    VALUES (?, ?, ?, ?)
");

$stmt->execute([
    $appointment_id,
    $amount,
    $payment_method,
    $note
]);

$update = $pdo->prepare("
    UPDATE appointments 
    SET payment_status = 'paid', status = 'completed'
    WHERE id = ?
");

$update->execute([$appointment_id]);

header("Location: ../pages/appointments.php?success=Thanh toán thành công");
exit;