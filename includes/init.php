<?php

// Inisialisasi & Konfigurasi Utama
session_name('seragam_sd');
session_start();

// Deteksi BASE URL otomatis (berfungsi di XAMPP, Laragon, subfolder, maupun virtual host)
$autoBase = '/pemesanan-seragam-sd';
if (php_sapi_name() !== 'cli' && !empty($_SERVER['DOCUMENT_ROOT'])) {
    $projectRoot = str_replace('\\', '/', realpath(__DIR__ . '/..') ?: '');
    $docRoot     = rtrim(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']) ?: ''), '/');
    if ($projectRoot !== '' && $docRoot !== '') {
        if (strcasecmp($projectRoot, $docRoot) === 0) {
            $autoBase = '';
        } elseif (stripos($projectRoot, $docRoot . '/') === 0) {
            $autoBase = rtrim(substr($projectRoot, strlen($docRoot)), '/');
        }
    }
}
define('BASE', $autoBase);
const HARGA = ['S' => 75000, 'M' => 80000, 'L' => 85000, 'XL' => 90000];
const STATUS = ['menunggu_pembayaran', 'dibayar', 'diproses', 'selesai', 'dibatalkan'];

// Koneksi Database
$conn = mysqli_connect('localhost', 'root', '', 'seragam_sd');
if (!$conn) {
    die('Koneksi database gagal');
}
mysqli_set_charset($conn, 'utf8mb4');

// ---------- helper tampilan (UI Helpers) ----------
function e($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}
function rupiah($n)
{
    return 'Rp ' . number_format((int)$n, 0, ',', '.');
}
function label($s)
{
    return ucwords(str_replace('_', ' ', $s));
}
function redirect($url)
{
    header('Location: ' . $url);
    exit;
}
function flash($pesan, $tipe = 'ok')
{
    $_SESSION['flash'] = [$pesan, $tipe];
}
function tampil_error($err)
{
    foreach ($err as $x) {
        echo '<div class="alert err">' . e($x) . '</div>';
    }
}

// ---------- helper query (selalu prepared statement) ----------
function q($sql, $types = '', ...$params)
{
    global $conn;
    $stmt = mysqli_prepare($conn, $sql);
    if ($types !== '') {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    return $stmt;
}
function ambil_semua($stmt)
{
    return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
}
function ambil_satu($stmt)
{
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

// ---------- benteng CSRF (Cross-Site Request Forgery) ----------
function csrf_field()
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return '<input type="hidden" name="csrf" value="' . $_SESSION['csrf'] . '">';
}
function csrf_cek()
{
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(403);
        die('Token tidak valid');
    }
}

// ---------- Penjaga Halaman (Middleware Akses) ----------
function wajib_login()
{
    if (empty($_SESSION['user_id'])) {
        redirect(BASE . '/auth/login.php');
    }
}
function wajib_ortu()
{
    wajib_login();
    if ($_SESSION['role'] !== 'ortu') {
        redirect(BASE . '/admin/index.php');
    }
}
function wajib_admin()
{
    wajib_login();
    if ($_SESSION['role'] !== 'admin') {
        http_response_code(403);
        die('Akses ditolak');
    }
}

// ---------- validasi pesanan (dipakai buat & edit) ----------
function validasi_pesanan($uid)
{
    $err = [];
    $v = [
        'anak_id' => (int)($_POST['anak_id'] ?? 0),
        'ukuran'  => $_POST['ukuran'] ?? '',
        'jumlah'  => $_POST['jumlah'] ?? '',
    ];
    // anak harus milik user yang login
    if (!ambil_satu(q('SELECT id FROM anak WHERE id=? AND user_id=?', 'ii', $v['anak_id'], $uid))) {
        $err[] = 'Anak tidak valid';
    }
    if (!is_string($v['ukuran']) || !isset(HARGA[$v['ukuran']])) {
        $err[] = 'Ukuran tidak valid';
    }
    $j = filter_var($v['jumlah'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 100]]);
    if ($j === false) {
        $err[] = 'Jumlah harus angka 1 sampai 100';
    }
    return [$err, $v, $j];
}
