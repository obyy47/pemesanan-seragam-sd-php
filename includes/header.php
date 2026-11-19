<?php
$halaman  = basename($_SERVER['PHP_SELF']);
$di_admin = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($judul) ?> - Seragam SD</title>
  <link rel="stylesheet" href="<?= BASE ?>/assets/css/style.css">
</head>

<body>
  <nav>
    <div class="nav-inner">
      <a class="brand" href="<?= BASE ?>/">Seragam SD</a>
      <?php if (!empty($_SESSION['user_id'])): ?>
        <?php if ($_SESSION['role'] === 'admin'): ?>
          <a class="<?= $di_admin ? 'active' : '' ?>" href="<?= BASE ?>/admin/index.php">Semua Pesanan</a>
        <?php else: ?>
          <a class="<?= $halaman === 'dashboard.php' ? 'active' : '' ?>" href="<?= BASE ?>/user/dashboard.php">Dashboard</a>
          <a class="<?= in_array($halaman, ['anak.php', 'anak_edit.php'], true) ? 'active' : '' ?>" href="<?= BASE ?>/user/anak.php">Data Anak</a>
          <a class="<?= $halaman === 'pesanan_buat.php' ? 'active' : '' ?>" href="<?= BASE ?>/user/pesanan_buat.php">Buat Pesanan</a>
          <a class="<?= in_array($halaman, ['pesanan.php', 'pesanan_edit.php'], true) ? 'active' : '' ?>" href="<?= BASE ?>/user/pesanan.php">Riwayat</a>
        <?php endif; ?>
        <form method="post" action="<?= BASE ?>/auth/logout.php" class="inline">
          <?= csrf_field() ?>
          <button class="btn-logout">Logout (<?= e($_SESSION['nama']) ?>)</button>
        </form>
      <?php else: ?>
        <a class="<?= $halaman === 'login.php' ? 'active' : '' ?>" href="<?= BASE ?>/auth/login.php">Login</a>
        <a class="<?= $halaman === 'register.php' ? 'active' : '' ?>" href="<?= BASE ?>/auth/register.php">Daftar</a>
      <?php endif; ?>
    </div>
  </nav>
  <main class="<?= !empty($auth_page) ? 'auth-wrap' : '' ?>">
    <?php if (empty($auth_page)): ?>
      <h1><?= e($judul) ?></h1>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash'])): [$pesan, $tipe] = $_SESSION['flash'];
      unset($_SESSION['flash']); ?>
      <div class="alert <?= $tipe === 'ok' ? 'ok' : 'err' ?>"><?= e($pesan) ?></div>
    <?php endif; ?>