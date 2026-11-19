<?php
require __DIR__ . '/../includes/init.php';
wajib_ortu();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(BASE . '/user/anak.php'); }
csrf_cek();

$uid = $_SESSION['user_id'];
$id  = (int)($_POST['id'] ?? 0);

$ada_pesanan = ambil_satu(q('SELECT COUNT(*) c FROM pesanan WHERE anak_id=? AND user_id=?', 'ii', $id, $uid))['c'];
if ($ada_pesanan > 0) {
    flash('Anak ini punya riwayat pesanan, tidak bisa dihapus', 'err');
} else {
    $stmt = q('DELETE FROM anak WHERE id=? AND user_id=?', 'ii', $id, $uid);
    if (mysqli_stmt_affected_rows($stmt) > 0) { flash('Data anak dihapus'); }
    else { flash('Data tidak ditemukan', 'err'); }
}
redirect(BASE . '/user/anak.php');
