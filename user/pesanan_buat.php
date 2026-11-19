<?php
require __DIR__ . '/../includes/init.php';
wajib_ortu();
$uid = $_SESSION['user_id'];

$anak_list = ambil_semua(q('SELECT id, nama_anak, kelas FROM anak WHERE user_id=? ORDER BY nama_anak', 'i', $uid));
$err = [];
$v = ['anak_id' => '', 'ukuran' => 'M', 'jumlah' => 1];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    [$err, $v, $jumlah] = validasi_pesanan($uid);
    if (!$err) {
        $harga = HARGA[$v['ukuran']];      // harga dari server, bukan dari form
        $total = $harga * $jumlah;
        q('INSERT INTO pesanan (user_id, anak_id, ukuran, jumlah, harga_satuan, total_harga, status)
           VALUES (?, ?, ?, ?, ?, ?, ?)', 'iisiiis',
           $uid, $v['anak_id'], $v['ukuran'], $jumlah, $harga, $total, 'menunggu_pembayaran');
        flash('Pesanan dibuat. Total ' . rupiah($total));
        redirect(BASE . '/user/pesanan.php');
    }
}
$judul = 'Buat pesanan';
$tombol = 'Pesan';
require __DIR__ . '/../includes/header.php';
tampil_error($err);
require __DIR__ . '/../includes/form_pesanan.php';
require __DIR__ . '/../includes/footer.php';
