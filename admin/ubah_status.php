<?php
require __DIR__ . '/../includes/init.php';
wajib_admin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(BASE . '/admin/index.php'); }
csrf_cek();

$berikut = ['menunggu_pembayaran' => 'dibayar', 'dibayar' => 'diproses', 'diproses' => 'selesai'];
$id = (int)($_POST['id'] ?? 0);
$p  = ambil_satu(q('SELECT status FROM pesanan WHERE id=?', 'i', $id));

if ($p && isset($berikut[$p['status']])) {
    // WHERE status=... mencegah update ganda kalau status sudah berubah
    q('UPDATE pesanan SET status=? WHERE id=? AND status=?', 'sis', $berikut[$p['status']], $id, $p['status']);
    flash('Status pesanan #' . $id . ' diubah ke ' . label($berikut[$p['status']]));
} else {
    flash('Status tidak bisa diubah', 'err');
}
redirect(BASE . '/admin/index.php');
