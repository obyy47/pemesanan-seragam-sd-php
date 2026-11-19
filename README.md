# Sistem Pemesanan Seragam SD

Aplikasi web sederhana berbasis **PHP Native** dan **MySQL** untuk mengelola pemesanan seragam sekolah dasar dengan dua peran pengguna: **Orang Tua** dan **Admin**. Proyek ini dibangun sebagai latihan implementasi CRUD, relasi basis data, autentikasi berbasis peran, dan praktik keamanan dasar di PHP prosedural.

## Fitur Utama

### Autentikasi & Hak Akses
- Registrasi akun baru dengan peran default `ortu` (validasi panjang nama, format email, keunikan email, dan panjang password minimal 8 karakter).
- Login menggunakan verifikasi hash (`password_verify`) dan regenerasi ID sesi (`session_regenerate_id`).
- Pembatasan akses halaman berdasarkan sesi dan peran (`wajib_login`, `wajib_ortu`, `wajib_admin`).
- Logout diproses melalui metode `POST` dengan validasi token CSRF.

### Peran Orang Tua (`user/`)
- **Dashboard:** Menampilkan ringkasan jumlah anak terdaftar, total pesanan, jumlah pesanan yang menunggu pembayaran, serta 5 pesanan terbaru.
- **Data Anak (CRUD):** Menambah, melihat, mengubah, dan menghapus data anak (nama dan kelas 1–6). Data anak yang sudah memiliki riwayat pesanan tidak dapat dihapus untuk menjaga integritas data.
- **Pemesanan Seragam:** Membuat pesanan berdasarkan data anak milik sendiri, memilih ukuran (`S`, `M`, `L`, `XL`), dan menentukan jumlah (1–100). Estimasi harga ditampilkan otomatis di form menggunakan JavaScript, sementara perhitungan akhir tetap divalidasi dari sisi server.
- **Riwayat Pesanan:** Melihat daftar seluruh pesanan milik sendiri, serta mengubah atau membatalkan pesanan selama statusnya masih `menunggu_pembayaran`.

### Peran Admin (`admin/`)
- **Ringkasan Statistik:** Menampilkan total pesanan, pesanan menunggu pembayaran, pesanan dibayar/diproses, dan total omzet dari pesanan valid (`dibayar`, `diproses`, `selesai`) menggunakan fungsi agregasi SQL (`COUNT`, `SUM`, `CASE WHEN`).
- **Filter & Pencarian:** Menyaring pesanan berdasarkan status serta mencari data berdasarkan nama orang tua atau nama anak.
- **Pembaruan Status Pesanan:** Mengubah status pesanan secara bertahap (`menunggu_pembayaran` -> `dibayar` -> `diproses` -> `selesai`) dengan konfirmasi sebelum eksekusi.

## Aspek Teknis & Keamanan yang Diterapkan
- **Prepared Statements (`mysqli`):** Seluruh query database dijalankan melalui helper `q()` menggunakan parameter binding.
- **Proteksi CSRF:** Setiap aksi pengubahan data melalui `POST` (login, daftar, logout, CRUD anak, pesanan, ubah status) memeriksa token CSRF.
- **Output Escaping:** Data yang ditampilkan ke HTML di-escape menggunakan `htmlspecialchars` melalui helper `e()`.
- **Validasi Kepemilikan Data:** Pengguna dengan peran `ortu` hanya dapat melihat, mengubah, atau menghapus data anak dan pesanan miliknya sendiri.

## Teknologi yang Digunakan
- **Backend:** PHP Native (prosedural, ekstensi `mysqli`)
- **Database:** MySQL / MariaDB
- **Frontend:** HTML, CSS (native), JavaScript (vanilla untuk kalkulasi estimasi harga di form)

## Skema Database
File skema tersedia di `database/seragam_sd.sql` untuk database `seragam_sd` dengan 3 tabel utama:
1. `users`: Menyimpan data akun (`id`, `nama`, `email` unik, `password`, `role`, `created_at`).
2. `anak`: Menyimpan data anak yang berelasi ke `users(id)` dengan `ON DELETE CASCADE`.
3. `pesanan`: Menyimpan transaksi pemesanan yang berelasi ke `users(id)` dan `anak(id)` beserta ukuran, jumlah, harga satuan, total harga, dan status.

## Struktur Folder
```text
pemesanan-seragam-sd/
├── admin/
│   ├── index.php          # Ringkasan statistik, filter/cari, dan daftar seluruh pesanan
│   └── ubah_status.php    # Proses pembaruan status pesanan oleh admin
├── assets/
│   └── css/
│       └── style.css      # Styling tampilan aplikasi
├── auth/
│   ├── login.php          # Halaman & proses login
│   ├── logout.php         # Proses logout (POST)
│   └── register.php       # Halaman & proses pendaftaran akun orang tua
├── database/
│   └── seragam_sd.sql     # Skema database MySQL
├── includes/
│   ├── footer.php         # Penutup layout HTML
│   ├── form_pesanan.php   # Komponen form buat/edit pesanan + preview harga JS
│   ├── header.php         # Navigasi & flash message
│   └── init.php           # Koneksi DB, deteksi BASE URL, helper query, CSRF, middleware
├── user/
│   ├── anak.php           # Tambah & daftar data anak
│   ├── anak_edit.php      # Edit data anak
│   ├── anak_hapus.php     # Hapus data anak
│   ├── dashboard.php      # Ringkasan aktivitas orang tua
│   ├── pesanan.php        # Riwayat pesanan orang tua
│   ├── pesanan_batal.php  # Pembatalan pesanan
│   ├── pesanan_buat.php   # Pembuatan pesanan baru
│   └── pesanan_edit.php   # Perubahan pesanan yang belum dibayar
├── buat_admin.php         # Script CLI untuk membuat akun admin
├── index.php              # Pengalihan awal sesuai sesi dan peran
└── README.md
```

## Cara Menjalankan di Lokal
1. Salin folder `pemesanan-seragam-sd` ke direktori web server lokal (`htdocs` pada XAMPP atau `www` pada Laragon).
2. Buat dan import database dengan menjalankan `database/seragam_sd.sql` melalui phpMyAdmin atau HeidiSQL.
3. Sesuaikan kredensial koneksi database di `includes/init.php` bila berbeda dari default (`localhost`, user `root`, password kosong, database `seragam_sd`). URL `BASE` sudah dideteksi otomatis.
4. Buat akun admin melalui terminal (cukup dijalankan sekali, password minimal 8 karakter):
   ```bash
   php buat_admin.php "Admin Seragam" admin@email.com Password123
   ```
5. Akses aplikasi lewat browser, misalnya `http://localhost/pemesanan-seragam-sd/`.

## Batasan Proyek
- Proses pembayaran belum terintegrasi dengan *payment gateway*; perubahan status menjadi `dibayar` dilakukan secara manual oleh admin.
- Belum tersedia fitur unggah bukti pembayaran, reset password, maupun ekspor laporan.