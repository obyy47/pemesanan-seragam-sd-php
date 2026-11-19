<?php
require __DIR__ . '/../includes/init.php';
if (!empty($_SESSION['user_id'])) {
    redirect(BASE . '/');
}

$err = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $email = trim($_POST['email'] ?? '');
    $pw    = $_POST['password'] ?? '';
    $user  = ambil_satu(q('SELECT * FROM users WHERE email=?', 's', $email));

    if ($user && password_verify($pw, $user['password'])) {
        session_regenerate_id(true); // cegah session fixation
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama']    = $user['nama'];
        $_SESSION['role']    = $user['role'];
        redirect(BASE . '/');
    }
    $err[] = 'Email atau password salah';
}

$judul     = 'Login';
$auth_page = true;
require __DIR__ . '/../includes/header.php';
?>
<div class="auth-card">
    <h1>Login</h1>
    <p class="auth-subtitle">Masuk untuk mengelola pemesanan seragam SD</p>
    <?php tampil_error($err); ?>
    <form method="post">
        <?= csrf_field() ?>
        <label>Email <input type="email" name="email" placeholder="nama@email.com" required></label>
        <label>Password <input type="password" name="password" placeholder="Masukkan password" required></label>
        <button>Login</button>
    </form>
    <p class="auth-footer">Belum punya akun? <a href="<?= BASE ?>/auth/register.php">Daftar di sini</a></p>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>