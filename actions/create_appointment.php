<?php
require_once "../config/database.php";

$customer_id = $_POST["customer_id"] ?? null;
$service_id = $_POST["service_id"] ?? null;
$staff_id = $_POST["staff_id"] ?? null;
$appointment_date = $_POST["appointment_date"] ?? null;
$appointment_time = $_POST["appointment_time"] ?? null;
$note = $_POST["note"] ?? "";

if (!$customer_id || !$service_id || !$staff_id || !$appointment_date || !$appointment_time) {
    header("Location: ../pages/appointments.php?error=Thiếu thông tin lịch hẹn");
    exit;
}

// Lấy thời lượng dịch vụ đang được đặt
$stmt = $pdo->prepare("SELECT duration FROM services WHERE id = ?");
$stmt->execute([$service_id]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    header("Location: ../pages/appointments.php?error=Không tìm thấy dịch vụ");
    exit;
}

$newStart = strtotime($appointment_date . " " . $appointment_time);
$newEnd = $newStart + ((int)$service["duration"] * 60);

// Kiểm tra trùng lịch theo thời lượng
$stmt = $pdo->prepare("
    SELECT 
        a.appointment_time,
        s.duration
    FROM appointments a
    JOIN services s ON a.service_id = s.id
    WHERE a.staff_id = ?
      AND a.appointment_date = ?
      AND a.status != 'cancelled'
");
$stmt->execute([$staff_id, $appointment_date]);
$existingAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($existingAppointments as $item) {
    $oldStart = strtotime($appointment_date . " " . $item["appointment_time"]);
    $oldEnd = $oldStart + ((int)$item["duration"] * 60);

    if ($newStart < $oldEnd && $newEnd > $oldStart) {
        header("Location: ../pages/appointments.php?error=Nhân viên này đã có lịch trong khoảng thời gian đó");
        exit;
    }
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