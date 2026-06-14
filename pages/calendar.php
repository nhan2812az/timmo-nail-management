<?php
require_once "../config/database.php";

$month = isset($_GET["month"]) ? (int)$_GET["month"] : (int)date("m");
$year = isset($_GET["year"]) ? (int)$_GET["year"] : (int)date("Y");

if ($month < 1 || $month > 12) {
    $month = (int)date("m");
}

$startDate = sprintf("%04d-%02d-01", $year, $month);
$endDate = date("Y-m-t", strtotime($startDate));

$prevMonth = $month - 1;
$prevYear = $year;
if ($prevMonth < 1) {
    $prevMonth = 12;
    $prevYear--;
}

$nextMonth = $month + 1;
$nextYear = $year;
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
}

$sql = "
    SELECT 
        appointments.*,
        customers.name AS customer_name,
        customers.phone AS customer_phone,
        services.name AS service_name,
        services.duration AS service_duration,
        staff.name AS staff_name
    FROM appointments
    JOIN customers ON appointments.customer_id = customers.id
    JOIN services ON appointments.service_id = services.id
    JOIN staff ON appointments.staff_id = staff.id
    WHERE appointments.appointment_date BETWEEN ? AND ?
    ORDER BY appointments.appointment_date ASC, appointments.appointment_time ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$startDate, $endDate]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$appointmentsByDate = [];

foreach ($rows as $row) {
    $appointmentsByDate[$row["appointment_date"]][] = $row;
}

$daysInMonth = (int)date("t", strtotime($startDate));
$firstDayOfWeek = (int)date("w", strtotime($startDate));

$statusText = [
    "new" => "Mới",
    "confirmed" => "Đã xác nhận",
    "completed" => "Hoàn thành",
    "cancelled" => "Đã hủy"
];

$statusClass = [
    "new" => "status-new",
    "confirmed" => "status-confirmed",
    "completed" => "status-completed",
    "cancelled" => "status-cancelled"
];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Timmo - Calendar View</title>
    <link rel="stylesheet" href="../assets/style.css">

    <style>
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0;
        }

        .calendar-nav a {
            display: inline-block;
            padding: 8px 12px;
            background: #222;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            margin-right: 6px;
        }

        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
        }

        .day-name {
            background: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            border-radius: 6px;
        }

        .day {
            min-height: 130px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 8px;
            background: #fff;
        }

        .empty {
            background: #f5f5f5;
        }

        .date-number {
            font-weight: bold;
            margin-bottom: 8px;
        }

        .appointment-item {
            padding: 6px;
            margin-bottom: 6px;
            border-radius: 6px;
            font-size: 13px;
            border-left: 4px solid #999;
            background: #f7f7f7;
        }

        .appointment-item a {
            text-decoration: none;
            color: #111;
        }

        .status-new {
            border-left-color: #777;
        }

        .status-confirmed {
            border-left-color: #007bff;
        }

        .status-completed {
            border-left-color: #28a745;
        }

        .status-cancelled {
            border-left-color: #dc3545;
            opacity: 0.65;
        }

        .today {
            border: 2px solid #007bff;
        }
    </style>
</head>
<body>

<h1>Calendar View</h1>

<div>
    <a href="../index.php">Dashboard</a> |
    <a href="appointments.php">Danh sách lịch hẹn</a>
</div>

<div class="calendar-header">
    <div class="calendar-nav">
        <a href="?month=<?= $prevMonth ?>&year=<?= $prevYear ?>">← Tháng trước</a>
        <a href="?month=<?= date("m") ?>&year=<?= date("Y") ?>">Hôm nay</a>
        <a href="?month=<?= $nextMonth ?>&year=<?= $nextYear ?>">Tháng sau →</a>
    </div>

    <h2>Tháng <?= $month ?>/<?= $year ?></h2>
</div>

<div class="calendar">
    <div class="day-name">CN</div>
    <div class="day-name">T2</div>
    <div class="day-name">T3</div>
    <div class="day-name">T4</div>
    <div class="day-name">T5</div>
    <div class="day-name">T6</div>
    <div class="day-name">T7</div>

    <?php for ($i = 0; $i < $firstDayOfWeek; $i++): ?>
        <div class="day empty"></div>
    <?php endfor; ?>

    <?php for ($day = 1; $day <= $daysInMonth; $day++): ?>
        <?php
            $date = sprintf("%04d-%02d-%02d", $year, $month, $day);
            $isToday = $date === date("Y-m-d");
        ?>

        <div class="day <?= $isToday ? 'today' : '' ?>">
            <div class="date-number"><?= $day ?></div>

            <?php if (!empty($appointmentsByDate[$date])): ?>
                <?php foreach ($appointmentsByDate[$date] as $item): ?>
                    <?php
                        $class = $statusClass[$item["status"]] ?? "status-new";
                        $label = $statusText[$item["status"]] ?? $item["status"];
                    ?>

                    <div class="appointment-item <?= $class ?>">
                        <a href="edit_appointment.php?id=<?= $item["id"] ?>">
                            <strong><?= substr($item["appointment_time"], 0, 5) ?></strong>
                            - <?= htmlspecialchars($item["customer_name"]) ?><br>
                            <?= htmlspecialchars($item["service_name"]) ?><br>
                            NV: <?= htmlspecialchars($item["staff_name"]) ?><br>
                            <?= $label ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endfor; ?>
</div>

</body>
</html>