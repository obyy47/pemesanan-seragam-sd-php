<?php
require __DIR__ . '/../includes/init.php';
wajib_ortu();
$uid = $_SESSION['user_id'];

$jml_anak    = ambil_satu(q('SELECT COUNT(*) c FROM anak WHERE user_id=?', 'i', $uid))['c'];
$jml_pesanan = ambil_satu(q('SELECT COUNT(*) c FROM pesanan WHERE user_id=?', 'i', $uid))['c'];
$jml_belum   = ambil_satu(q("SELECT COUNT(*) c FROM pesanan WHERE user_id=? AND status='menunggu_pembayaran'", 'i', $uid))['c'];
$terbaru = ambil_semua(q(
    'SELECT p.*, a.nama_anak FROM pesanan p JOIN anak a ON a.id=p.anak_id
     WHERE p.user_id=? ORDER BY p.id DESC LIMIT 5', 'i', $uid));

$judul = 'Dashboard';
require __DIR__ . '/../includes/header.php';
?>
<p>Halo, <?= e($_SESSION['nama']) ?>!</p>
<div class="cards">
  <div class="card"><b><?= $jml_anak ?></b>Anak</div>
  <div class="card"><b><?= $jml_pesanan ?></b>Pesanan</div>
  <div class="card"><b><?= $jml_belum ?></b>Menunggu pembayaran</div>
</div>
<h2>Pesanan terbaru</h2>
<?php if (!$terbaru): ?>
  <p>Belum ada pesanan. <a href="<?= BASE ?>/user/pesanan_buat.php">Buat pesanan</a></p>
<?php else: ?>
<table>
  <tr><th>Anak</th><th>Ukuran</th><th>Jumlah</th><th>Total</th><th>Status</th></tr>
  <?php foreach ($terbaru as $p): ?>
  <tr>
    <td><?= e($p['nama_anak']) ?></td><td><?= e($p['ukuran']) ?></td><td><?= $p['jumlah'] ?></td>
    <td><?= rupiah($p['total_harga']) ?></td><td><span class="badge <?= e($p['status']) ?>"><?= e(label($p['status'])) ?></span></td>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
