-- ============================================
-- DATABASE INVENTARIS BARANG
-- Jobsheet Paket A
-- ============================================

CREATE DATABASE IF NOT EXISTS inventaris_db;
USE inventaris_db;

-- TABEL USER
CREATE TABLE IF NOT EXISTS user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100),
    username VARCHAR(50) UNIQUE,
    password VARCHAR(100),
    role ENUM('admin','peminjam')
);

-- TABEL BARANG
CREATE TABLE IF NOT EXISTS barang (
    id_barang INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(100),
    jumlah INT DEFAULT 0,
    kondisi_barang ENUM('baik','rusak') DEFAULT 'baik'
);

-- TABEL PEMINJAMAN (+ fitur pengembalian dari Tugas Praktik)
CREATE TABLE IF NOT EXISTS peminjaman (
    id_pinjam INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    id_barang INT,
    jumlah_pinjam INT,
    tanggal_pinjam DATE,
    tanggal_kembali DATE DEFAULT NULL,
    status ENUM('dipinjam','dikembalikan') DEFAULT 'dipinjam',
    FOREIGN KEY (id_user) REFERENCES user(id_user),
    FOREIGN KEY (id_barang) REFERENCES barang(id_barang)
);

-- STORED PROCEDURE: PINJAM BARANG
DROP PROCEDURE IF EXISTS pinjam_barang;
DELIMITER //
CREATE PROCEDURE pinjam_barang(
    IN p_id_user INT,
    IN p_id_barang INT,
    IN p_jumlah INT
)
BEGIN
    DECLARE stok_tersedia INT;
    SELECT jumlah INTO stok_tersedia FROM barang WHERE id_barang = p_id_barang;
    IF stok_tersedia >= p_jumlah THEN
        INSERT INTO peminjaman(id_user, id_barang, jumlah_pinjam, tanggal_pinjam, status)
        VALUES(p_id_user, p_id_barang, p_jumlah, CURDATE(), 'dipinjam');
        UPDATE barang SET jumlah = jumlah - p_jumlah WHERE id_barang = p_id_barang;
        SELECT 'sukses' AS hasil;
    ELSE
        SELECT 'gagal' AS hasil;
    END IF;
END //
DELIMITER ;

-- STORED PROCEDURE: KEMBALIKAN BARANG (Tugas Praktik no.1)
DROP PROCEDURE IF EXISTS kembalikan_barang;
DELIMITER //
CREATE PROCEDURE kembalikan_barang(IN p_id_pinjam INT)
BEGIN
    DECLARE v_id_barang INT;
    DECLARE v_jumlah INT;
    DECLARE v_status VARCHAR(20);
    SELECT id_barang, jumlah_pinjam, status
    INTO v_id_barang, v_jumlah, v_status
    FROM peminjaman WHERE id_pinjam = p_id_pinjam;
    IF v_status = 'dipinjam' THEN
        UPDATE peminjaman SET status='dikembalikan', tanggal_kembali=CURDATE() WHERE id_pinjam=p_id_pinjam;
        UPDATE barang SET jumlah = jumlah + v_jumlah WHERE id_barang = v_id_barang;
        SELECT 'sukses' AS hasil;
    ELSE
        SELECT 'sudah_dikembalikan' AS hasil;
    END IF;
END //
DELIMITER ;

-- FUNCTION: STATUS BARANG
DROP FUNCTION IF EXISTS status_barang;
DELIMITER //
CREATE FUNCTION status_barang(jumlah INT)
RETURNS VARCHAR(20)
DETERMINISTIC
BEGIN
    DECLARE hasil VARCHAR(20);
    IF jumlah <= 0 THEN SET hasil = 'Habis';
    ELSE SET hasil = 'Tersedia';
    END IF;
    RETURN hasil;
END //
DELIMITER ;

-- DATA AWAL
INSERT INTO user (nama, username, password, role) VALUES
('Administrator', 'admin', MD5('admin123'), 'admin'),
('Budi Santoso', 'budi', MD5('user123'), 'peminjam'),
('Siti Rahayu', 'siti', MD5('user123'), 'peminjam');

INSERT INTO barang (nama_barang, jumlah, kondisi_barang) VALUES
('Laptop Lenovo', 10, 'baik'),
('Proyektor Epson', 5, 'baik'),
('Kamera Canon', 3, 'baik'),
('Mouse Wireless', 20, 'baik'),
('Keyboard USB', 15, 'rusak');
