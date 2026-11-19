<?php
require __DIR__ . '/../includes/init.php';
wajib_ortu();
$uid = $_SESSION['user_id'];
$id  = (int)($_GET['id'] ?? 0);

// hanya anak milik user yang login
$anak = ambil_satu(q('SELECT * FROM anak WHERE id=? AND user_id=?', 'ii', $id, $uid));
if (!$anak) { http_response_code(404); die('Data tidak ditemukan'); }

$err = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $anak['nama_anak'] = trim($_POST['nama_anak'] ?? '');
    $anak['kelas']     = trim($_POST['kelas'] ?? '');
    if ($anak['nama_anak'] === '' || mb_strlen($anak['nama_anak']) > 100) { $err[] = 'Nama anak wajib diisi (maks 100 karakter)'; }
    if (!in_array($anak['kelas'], ['1', '2', '3', '4', '5', '6'], true)) { $err[] = 'Kelas harus 1 sampai 6'; }
    if (!$err) {
        q('UPDATE anak SET nama_anak=?, kelas=? WHERE id=? AND user_id=?', 'ssii', $anak['nama_anak'], $anak['kelas'], $id, $uid);
        flash('Data anak diperbarui');
        redirect(BASE . '/user/anak.php');
    }
}
$judul = 'Edit data anak';
require __DIR__ . '/../includes/header.php';
tampil_error($err);
?>
<form method="post">
  <?= csrf_field() ?>
  <label>Nama anak <input type="text" name="nama_anak" value="<?= e($anak['nama_anak']) ?>" required></label>
  <label>Kelas
    <select name="kelas" required>
      <?php foreach (['1','2','3','4','5','6'] as $k): ?>
        <option value="<?= $k ?>" <?= $anak['kelas'] === $k ? 'selected' : '' ?>><?= $k ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <button>Simpan</button> <a href="<?= BASE ?>/user/anak.php">Batal</a>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
