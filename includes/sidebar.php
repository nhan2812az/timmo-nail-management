<aside class="sidebar">

    <h2>Timmo</h2>

    <?php
$base_url = "/timmo-nail-management";
?>
<a href="<?= $base_url ?>/pages/revenue_report.php">Báo cáo doanh thu</a>
<a href="<?= $base_url ?>/index.php">Dashboard</a>
<a href="<?= $base_url ?>/pages/appointments.php">Lịch hẹn</a>
<a href="<?= $base_url ?>/pages/calendar.php">Calendar</a>
<a href="<?= $base_url ?>/pages/customers.php">Khách hàng</a>
<a href="<?= $base_url ?>/pages/services.php">Dịch vụ</a>
<a href="<?= $base_url ?>/pages/nail_gallery.php">Mẫu nail</a>
<a href="<?= $base_url ?>/pages/staff.php">Nhân viên</a>
<a href="<?= $base_url ?>/pages/users.php">Tài khoản</a>


    <hr>

    <p>
        Xin chào,
        <strong>
            <?= htmlspecialchars($_SESSION["user"]["name"] ?? "Guest") ?>
        </strong>
    </p>

    <p>
        Quyền:
        <strong>
            <?= htmlspecialchars($_SESSION["user"]["role"] ?? "-") ?>
        </strong>
    </p>

    <a href="/timmo-nail-management/logout.php">
        Đăng xuất
    </a>

</aside>