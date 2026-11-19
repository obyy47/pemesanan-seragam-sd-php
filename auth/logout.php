<?php
require __DIR__ . '/../includes/init.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $_SESSION = [];
    session_destroy();
}
redirect(BASE . '/auth/login.php');
