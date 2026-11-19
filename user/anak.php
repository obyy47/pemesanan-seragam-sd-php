<?php
require __DIR__ . '/../includes/init.php';
wajib_ortu();
$uid = $_SESSION['user_id'];

$err = [];
$nama_anak = $kelas = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $nama_anak = trim($_POST['nama_anak'] ?? '');
    $kelas     = trim($_POST['kelas'] ?? '');
    if ($nama_anak === '' || mb_strlen($nama_anak) > 100) { $err[] = 'Nama anak wajib diisi (maks 100 karakter)'; }
    if (!in_array($kelas, ['1', '2', '3', '4', '5', '6'], true)) { $err[] = 'Kelas harus 1 sampai 6'; }
    if (!$err) {
        q('INSERT INTO anak (user_id, nama_anak, kelas) VALUES (?, ?, ?)', 'iss', $uid, $nama_anak, $kelas);
        flash('Data anak ditambahkan');
        redirect(BASE . '/user/anak.php');
    }
}
$daftar = ambil_semua(q('SELECT * FROM anak WHERE user_id=? ORDER BY nama_anak', 'i', $uid));

$judul = 'Data anak';
require __DIR__ . '/../includes/header.php';
tampil_error($err);
?>
<form method="post">
  <?= csrf_field() ?>
  <label>Nama anak <input type="text" name="nama_anak" value="<?= e($nama_anak) ?>" required></label>
  <label>Kelas
    <select name="kelas" required>
      <?php foreach (['1','2','3','4','5','6'] as $k): ?>
        <option value="<?= $k ?>" <?= $kelas === $k ? 'selected' : '' ?>><?= $k ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <button>Tambah anak</button>
</form>

<h2>Daftar anak</h2>
<?php if (!$daftar): ?><p>Belum ada data anak.</p><?php else: ?>
<table>
  <tr><th>Nama</th><th>Kelas</th><th>Aksi</th></tr>
  <?php foreach ($daftar as $a): ?>
  <tr>
    <td><?= e($a['nama_anak']) ?></td><td><?= e($a['kelas']) ?></td>
    <td class="aksi">
      <a href="<?= BASE ?>/user/anak_edit.php?id=<?= $a['id'] ?>">Edit</a>
      <form method="post" action="<?= BASE ?>/user/anak_hapus.php" class="inline" onsubmit="return confirm('Hapus data anak ini?')">
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= $a['id'] ?>">
        <button class="link danger">Hapus</button>
      </form>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
