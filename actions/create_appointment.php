<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/appointments.php");
    exit;
}

$customer_id = $_POST["customer_id"];
$service_id = $_POST["service_id"];
$staff_id = $_POST["staff_id"];
$appointment_date = $_POST["appointment_date"];
$appointment_time = $_POST["appointment_time"];
$note = $_POST["note"] ?? "";

// Kiểm tra trùng lịch nhân viên
$check = $pdo->prepare("
    SELECT COUNT(*) 
    FROM appointments
    WHERE staff_id = ?
    AND appointment_date = ?
    AND appointment_time = ?
    AND status != 'cancelled'
");

$check->execute([
    $staff_id,
    $appointment_date,
    $appointment_time
]);

$isConflict = $check->fetchColumn();

if ($isConflict > 0) {
    header("Location: ../pages/appointments.php?error=Nhân viên này đã có lịch vào thời gian đó");
    exit;
}

// Tạo lịch hẹn
$stmt = $pdo->prepare("
    INSERT INTO appointments 
    (customer_id, service_id, staff_id, appointment_date, appointment_time, note, status)
    VALUES (?, ?, ?, ?, ?, ?, 'new')
");

$stmt->execute([
    $customer_id,
    $service_id,
    $staff_id,
    $appointment_date,
    $appointment_time,
    $note
]);

header("Location: ../pages/appointments.php?success=Tạo lịch hẹn thành công");
exit;