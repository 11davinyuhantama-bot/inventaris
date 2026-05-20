<?php
// admin/hapus_barang.php
session_start();
require_once '../includes/auth.php';
require_once '../includes/koneksi.php';
cekAdmin();

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    $stmt = $conn->prepare("DELETE FROM barang WHERE id_barang = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: barang.php?pesan=hapus");
exit();
?>
