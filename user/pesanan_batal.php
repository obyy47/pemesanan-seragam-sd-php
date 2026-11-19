<?php
require __DIR__ . '/../includes/init.php';
wajib_ortu();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect(BASE . '/user/pesanan.php'); }
csrf_cek();

$uid = $_SESSION['user_id'];
$id  = (int)($_POST['id'] ?? 0);
$stmt = q("UPDATE pesanan SET status='dibatalkan' WHERE id=? AND user_id=? AND status='menunggu_pembayaran'", 'ii', $id, $uid);

if (mysqli_stmt_affected_rows($stmt) > 0) { flash('Pesanan dibatalkan'); }
else { flash('Pesanan tidak bisa dibatalkan', 'err'); }
redirect(BASE . '/user/pesanan.php');
