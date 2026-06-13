<aside class="sidebar">

    <h2>Timmo</h2>

    <a href="/myproject/index.php">
        Dashboard
    </a>

    <a href="/myproject/pages/appointments.php">
        Lịch hẹn
    </a>

    <a href="/myproject/pages/customers.php">
        Khách hàng
    </a>

    <a href="/myproject/pages/services.php">
        Dịch vụ
    </a>

    <a href="/myproject/pages/staff.php">
        Nhân viên
    </a>
    <?php if (($_SESSION["user"]["role"] ?? "") === "admin"): ?>
    <a href="/myproject/pages/users.php">
        Tài khoản
    </a>
<?php endif; ?>

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

    <a href="/myproject/logout.php">
        Đăng xuất
    </a>

</aside>