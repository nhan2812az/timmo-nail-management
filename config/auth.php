<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin()
{
    if (!isset($_SESSION["user"])) {
        header("Location: /myproject/login.php");
        exit;
    }
}

function requireRole($roles)
{
    requireLogin();

    if (!in_array($_SESSION["user"]["role"], $roles)) {
        die("Bạn không có quyền truy cập chức năng này.");
    }
}