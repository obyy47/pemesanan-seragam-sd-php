<?php
require __DIR__ . '/includes/init.php';
if (empty($_SESSION['user_id'])) {
    redirect(BASE . '/auth/login.php');
}
redirect($_SESSION['role'] === 'admin' ? BASE . '/admin/index.php' : BASE . '/user/dashboard.php');
