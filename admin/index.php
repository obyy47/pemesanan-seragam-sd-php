<?php
require __DIR__ . '/../includes/init.php';
wajib_admin();

// Ringkasan statistik pesanan menggunakan agregasi SQL
$ringkasan = ambil_satu(q(
  "SELECT
        COUNT(*) AS total_pesanan,
        COALESCE(SUM(CASE WHEN status = 'menunggu_pembayaran' THEN 1 ELSE 0 END), 0) AS menunggu,
        COALESCE(SUM(CASE WHEN status IN ('dibayar', 'diproses') THEN 1 ELSE 0 END), 0) AS aktif,
        COALESCE(SUM(CASE WHEN status IN ('dibayar', 'diproses', 'selesai') THEN total_harga ELSE 0 END), 0) AS total_omzet
     FROM pesanan"
));

$sql    = 'SELECT p.*, u.nama AS nama_ortu, a.nama_anak, a.kelas
           FROM pesanan p JOIN users u ON u.id=p.user_id JOIN anak a ON a.id=p.anak_id';
$where  = [];
$types  = '';
$params = [];

$filter = $_GET['status'] ?? '';
if (in_array($filter, STATUS, true)) {
  $where[]  = 'p.status = ?';
  $types   .= 's';
  $params[] = $filter;
} else {
  $filter = '';
}

$cari = trim($_GET['cari'] ?? '');
if ($cari !== '') {
  $where[]  = '(u.nama LIKE ? OR a.nama_anak LIKE ?)';
  $types   .= 'ss';
  $like     = '%' . $cari . '%';
  $params[] = $like;
  $params[] = $like;
}

if ($where) {
  $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY p.id DESC';

$daftar  = ambil_semua($types !== '' ? q($sql, $types, ...$params) : q($sql));
$berikut = ['menunggu_pembayaran' => 'dibayar', 'dibayar' => 'diproses', 'diproses' => 'selesai'];

$judul = 'Semua Pesanan';
require __DIR__ . '/../includes/header.php';
?>
<div class="cards">
  <div class="card"><b><?= (int)$ringkasan['total_pesanan'] ?></b>Total Pesanan</div>
  <div class="card"><b><?= (int)$ringkasan['menunggu'] ?></b>Menunggu Pembayaran</div>
  <div class="card"><b><?= (int)$ringkasan['aktif'] ?></b>Dibayar / Diproses</div>
  <div class="card omzet"><b><?= rupiah($ringkasan['total_omzet']) ?></b>Total Omzet Valid</div>
</div>

<form method="get" class="filter">
  <label>Filter Status
    <select name="status" onchange="this.form.submit()">
      <option value="">Semua Status</option>
      <?php foreach (STATUS as $s): ?>
        <option value="<?= $s ?>" <?= $filter === $s ? 'selected' : '' ?>><?= e(label($s)) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Cari Ortu / Anak
    <input type="text" name="cari" value="<?= e($cari) ?>" placeholder="Ketik nama ortu atau anak...">
  </label>
  <div class="filter-actions">
    <button type="submit">Cari</button>
    <?php if ($filter !== '' || $cari !== ''): ?>
      <a class="btn btn-secondary" href="<?= BASE ?>/admin/index.php">Reset</a>
    <?php endif; ?>
  </div>
</form>

<?php if (!$daftar): ?>
  <div class="empty-state">
    <p>Tidak ada pesanan yang sesuai filter.</p>
  </div>
<?php else: ?>
  <div class="table-wrap">
    <table>
      <tr>
        <th>#</th>
        <th>Tanggal</th>
        <th>Ortu</th>
        <th>Anak</th>
        <th>Ukuran</th>
        <th>Jml</th>
        <th>Total</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
      <?php foreach ($daftar as $p): ?>
        <tr>
          <td><?= $p['id'] ?></td>
          <td><?= e(date('d/m/Y', strtotime($p['created_at']))) ?></td>
          <td><?= e($p['nama_ortu']) ?></td>
          <td><?= e($p['nama_anak']) ?> (Kelas <?= e($p['kelas']) ?>)</td>
          <td><?= e($p['ukuran']) ?></td>
          <td><?= $p['jumlah'] ?></td>
          <td><?= rupiah($p['total_harga']) ?></td>
          <td><span class="badge <?= e($p['status']) ?>"><?= e(label($p['status'])) ?></span></td>
          <td>
            <?php if (isset($berikut[$p['status']])): ?>
              <form method="post" action="<?= BASE ?>/admin/ubah_status.php" class="inline"
                onsubmit="return confirm('Ubah status pesanan #<?= $p['id'] ?> menjadi <?= e(label($berikut[$p['status']])) ?>?')">
                <?= csrf_field() ?><input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button class="btn-sm">Jadikan <?= e(label($berikut[$p['status']])) ?></button>
              </form>
              <?php else: ?>-<?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>