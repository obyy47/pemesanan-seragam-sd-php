<?php
require __DIR__ . '/../includes/init.php';
if (!empty($_SESSION['user_id'])) {
    redirect(BASE . '/');
}

$err = [];
$nama = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $nama  = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pw    = $_POST['password'] ?? '';

    if ($nama === '' || mb_strlen($nama) > 100) {
        $err[] = 'Nama wajib diisi (maks 100 karakter)';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err[] = 'Format email tidak valid';
    }
    if (strlen($pw) < 8) {
        $err[] = 'Password minimal 8 karakter';
    }
    if (!$err && ambil_satu(q('SELECT id FROM users WHERE email=?', 's', $email))) {
        $err[] = 'Email sudah terdaftar';
    }

    if (!$err) {
        $hash = password_hash($pw, PASSWORD_DEFAULT);
        q("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'ortu')", 'sss', $nama, $email, $hash);
        flash('Pendaftaran berhasil, silakan login');
        redirect(BASE . '/auth/login.php');
    }
}

$judul     = 'Daftar Akun';
$auth_page = true;
require __DIR__ . '/../includes/header.php';
?>
<div class="auth-card">
    <h1>Daftar Akun</h1>
    <p class="auth-subtitle">Buat akun orang tua untuk mulai memesan seragam</p>
    <?php tampil_error($err); ?>
    <form method="post">
        <?= csrf_field() ?>
        <label>Nama Lengkap <input type="text" name="nama" value="<?= e($nama) ?>" placeholder="Nama lengkap" required></label>
        <label>Email <input type="email" name="email" value="<?= e($email) ?>" placeholder="nama@email.com" required></label>
        <label>Password (min. 8 karakter) <input type="password" name="password" placeholder="Minimal 8 karakter" required></label>
        <button>Daftar</button>
    </form>
    <p class="auth-footer">Sudah punya akun? <a href="<?= BASE ?>/auth/login.php">Login di sini</a></p>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>