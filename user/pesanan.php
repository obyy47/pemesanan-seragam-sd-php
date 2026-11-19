<?php
require __DIR__ . '/../includes/init.php';
wajib_ortu();
$uid = $_SESSION['user_id'];

$daftar = ambil_semua(q(
    'SELECT p.*, a.nama_anak, a.kelas FROM pesanan p JOIN anak a ON a.id=p.anak_id
     WHERE p.user_id=? ORDER BY p.id DESC', 'i', $uid));

$judul = 'Riwayat pesanan';
require __DIR__ . '/../includes/header.php';
?>
<?php if (!$daftar): ?><p>Belum ada pesanan.</p><?php else: ?>
<table>
  <tr><th>Tanggal</th><th>Anak</th><th>Ukuran</th><th>Jml</th><th>Total</th><th>Status</th><th>Aksi</th></tr>
  <?php foreach ($daftar as $p): ?>
  <tr>
    <td><?= e(date('d/m/Y', strtotime($p['created_at']))) ?></td>
    <td><?= e($p['nama_anak']) ?> (<?= e($p['kelas']) ?>)</td>
    <td><?= e($p['ukuran']) ?></td><td><?= $p['jumlah'] ?></td>
    <td><?= rupiah($p['total_harga']) ?></td>
    <td><span class="badge <?= e($p['status']) ?>"><?= e(label($p['status'])) ?></span></td>
    <td class="aksi">
      <?php if ($p['status'] === 'menunggu_pembayaran'): ?>
        <a href="<?= BASE ?>/user/pesanan_edit.php?id=<?= $p['id'] ?>">Edit</a>
        <form method="post" action="<?= BASE ?>/user/pesanan_batal.php" class="inline" onsubmit="return confirm('Batalkan pesanan ini?')">
          <?= csrf_field() ?><input type="hidden" name="id" value="<?= $p['id'] ?>">
          <button class="link danger">Batalkan</button>
        </form>
      <?php else: ?>-<?php endif; ?>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
