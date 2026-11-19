CREATE DATABASE IF NOT EXISTS seragam_sd CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE seragam_sd;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('ortu','admin') NOT NULL DEFAULT 'ortu',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE anak (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  nama_anak VARCHAR(100) NOT NULL,
  kelas VARCHAR(20) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE pesanan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  anak_id INT NOT NULL,
  ukuran ENUM('S','M','L','XL') NOT NULL,
  jumlah INT NOT NULL,
  harga_satuan INT NOT NULL,
  total_harga INT NOT NULL,
  status ENUM('menunggu_pembayaran','dibayar','diproses','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu_pembayaran',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (anak_id) REFERENCES anak(id)
);
