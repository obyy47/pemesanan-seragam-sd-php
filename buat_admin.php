<?php
// Jalankan SEKALI lewat terminal: php buat_admin.php "Nama Admin" admin@email.com PasswordKuat123
if (php_sapi_name() !== 'cli') {
    http_response_code(404);
    exit;
}
require __DIR__ . '/includes/init.php';

[$_, $nama, $email, $pw] = $argv + [null, null, null, null];
if (!$nama || !$email || !$pw) {
    exit("Cara pakai: php buat_admin.php \"Nama\" email password\n");
}
if (strlen($pw) < 8) {
    exit("Password minimal 8 karakter\n");
}

$hash = password_hash($pw, PASSWORD_DEFAULT);
q("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'admin')", 'sss', $nama, $email, $hash);
echo "Admin dibuat: $email\n";
