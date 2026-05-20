<?php
// user/proses_pinjam.php
session_start();
require_once '../includes/auth.php';
require_once '../includes/koneksi.php';
cekUser();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard.php");
    exit();
}

$id_user   = (int)$_SESSION['id_user'];
$id_barang = (int)$_POST['id_barang'];
$jumlah    = (int)$_POST['jumlah'];

// Cek stok barang
$cek = $conn->prepare("SELECT jumlah, kondisi_barang FROM barang WHERE id_barang = ?");
$cek->bind_param("i", $id_barang);
$cek->execute();
$barang = $cek->get_result()->fetch_assoc();

if (!$barang || $barang['jumlah'] <= 0) {
    header("Location: dashboard.php?error=habis");
    exit();
}

if ($jumlah > $barang['jumlah']) {
    header("Location: dashboard.php?error=stok");
    exit();
}

// Panggil stored procedure
$conn->query("CALL pinjam_barang($id_user, $id_barang, $jumlah)");

header("Location: dashboard.php?pesan=sukses");
exit();
?>
