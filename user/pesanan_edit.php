<?php
require __DIR__ . '/../includes/init.php';
wajib_ortu();
$uid = $_SESSION['user_id'];
$id  = (int)($_GET['id'] ?? 0);

// milik sendiri DAN masih menunggu pembayaran
$pesanan = ambil_satu(q("SELECT * FROM pesanan WHERE id=? AND user_id=? AND status='menunggu_pembayaran'", 'ii', $id, $uid));
if (!$pesanan) { flash('Pesanan tidak bisa diedit', 'err'); redirect(BASE . '/user/pesanan.php'); }

$anak_list = ambil_semua(q('SELECT id, nama_anak, kelas FROM anak WHERE user_id=? ORDER BY nama_anak', 'i', $uid));
$err = [];
$v = ['anak_id' => $pesanan['anak_id'], 'ukuran' => $pesanan['ukuran'], 'jumlah' => $pesanan['jumlah']];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    [$err, $v, $jumlah] = validasi_pesanan($uid);
    if (!$err) {
        $harga = HARGA[$v['ukuran']];
        $total = $harga * $jumlah;
        $stmt = q("UPDATE pesanan SET anak_id=?, ukuran=?, jumlah=?, harga_satuan=?, total_harga=?
                   WHERE id=? AND user_id=? AND status='menunggu_pembayaran'", 'isiiiii',
                   $v['anak_id'], $v['ukuran'], $jumlah, $harga, $total, $id, $uid);
        flash('Pesanan diperbarui');
        redirect(BASE . '/user/pesanan.php');
    }
}
$judul = 'Edit pesanan';
$tombol = 'Simpan';
require __DIR__ . '/../includes/header.php';
tampil_error($err);
require __DIR__ . '/../includes/form_pesanan.php';
?>
<p><a href="<?= BASE ?>/user/pesanan.php">Kembali</a></p>
<?php require __DIR__ . '/../includes/footer.php'; ?>
