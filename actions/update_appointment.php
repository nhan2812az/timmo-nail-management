<?php
require_once "../config/database.php";
require_once "../config/auth.php";
requireLogin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/appointments.php");
    exit;
}

$id = $_POST["id"];
$customer_id = $_POST["customer_id"];
$service_id = $_POST["service_id"];
$staff_id = $_POST["staff_id"];
$appointment_date = $_POST["appointment_date"];
$appointment_time = $_POST["appointment_time"];
$status = $_POST["status"];
$note = $_POST["note"] ?? "";

$allowedStatus = ["new", "confirmed", "completed", "cancelled"];

if (!in_array($status, $allowedStatus)) {
    header("Location: ../pages/appointments.php?error=Trạng thái không hợp lệ");
    exit;
}

$check = $pdo->prepare("
    SELECT COUNT(*)
    FROM appointments
    WHERE staff_id = ?
    AND appointment_date = ?
    AND appointment_time = ?
    AND status != 'cancelled'
    AND id != ?
");

$check->execute([
    $staff_id,
    $appointment_date,
    $appointment_time,
    $id
]);

if ($check->fetchColumn() > 0) {
    header("Location: ../pages/edit_appointment.php?id=$id&error=Nhân viên này đã có lịch vào thời gian đó");
    exit;
}

$stmt = $pdo->prepare("
    UPDATE appointments
    SET customer_id = ?,
        service_id = ?,
        staff_id = ?,
        appointment_date = ?,
        appointment_time = ?,
        status = ?,
        note = ?
    WHERE id = ?
");

$stmt->execute([
    $customer_id,
    $service_id,
    $staff_id,
    $appointment_date,
    $appointment_time,
    $status,
    $note,
    $id
]);

header("Location: ../pages/appointments.php?success=Cập nhật lịch hẹn thành công");
exit;