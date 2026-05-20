<?php
// admin/edit_barang.php
session_start();
require_once '../includes/auth.php';
require_once '../includes/koneksi.php';
cekAdmin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header("Location: barang.php"); exit(); }

// Ambil data barang
$stmt = $conn->prepare("SELECT * FROM barang WHERE id_barang = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$barang = $stmt->get_result()->fetch_assoc();

if (!$barang) { header("Location: barang.php"); exit(); }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama    = trim($_POST['nama_barang']);
    $jumlah  = (int)$_POST['jumlah'];
    $kondisi = $_POST['kondisi_barang'];

    if ($nama === '' || $jumlah < 0) {
        $error = 'Isi semua kolom dengan benar!';
    } else {
        $stmt = $conn->prepare("UPDATE barang SET nama_barang=?, jumlah=?, kondisi_barang=? WHERE id_barang=?");
        $stmt->bind_param("sisi", $nama, $jumlah, $kondisi, $id);
        $stmt->execute();
        header("Location: barang.php?pesan=edit");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Barang</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',sans-serif; background:#f0f2f5; }
        .navbar { background:#1a73e8; color:white; padding:14px 28px; display:flex; justify-content:space-between; align-items:center; }
        .navbar h1 { font-size:18px; }
        .navbar a { color:white; text-decoration:none; font-size:13px; margin-left:16px; }
        .container { padding:28px; max-width:500px; }
        h2 { color:#333; margin-bottom:20px; }
        .form-card { background:white; padding:28px; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.07); }
        label { display:block; font-size:13px; color:#555; font-weight:600; margin-bottom:6px; }
        input, select { width:100%; padding:10px 14px; border:1.5px solid #ddd; border-radius:8px; font-size:14px; margin-bottom:18px; outline:none; }
        input:focus, select:focus { border-color:#1a73e8; }
        .btn { padding:11px 22px; border-radius:8px; border:none; cursor:pointer; font-size:14px; font-weight:600; text-decoration:none; display:inline-block; }
        .btn-blue { background:#1a73e8; color:white; }
        .btn-grey { background:#e0e0e0; color:#333; margin-left:8px; }
        .error { background:#fde8e8; color:#c62828; padding:10px 14px; border-radius:8px; margin-bottom:16px; font-size:13px; }
    </style>
</head>
<body>
<div class="navbar">
    <h1>📦 Inventaris — Admin</h1>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="barang.php">Kelola Barang</a>
        <a href="../logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>✏️ Edit Barang</h2>
    <div class="form-card">
        <?php if ($error): ?>
            <div class="error">❌ <?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <label>Nama Barang</label>
            <input type="text" name="nama_barang" value="<?= htmlspecialchars($barang['nama_barang']) ?>" required>

            <label>Jumlah</label>
            <input type="number" name="jumlah" min="0" value="<?= $barang['jumlah'] ?>" required>

            <label>Kondisi Barang</label>
            <select name="kondisi_barang">
                <option value="baik"  <?= $barang['kondisi_barang'] === 'baik'  ? 'selected' : '' ?>>Baik</option>
                <option value="rusak" <?= $barang['kondisi_barang'] === 'rusak' ? 'selected' : '' ?>>Rusak</option>
            </select>

            <button type="submit" class="btn btn-blue">💾 Update</button>
            <a href="barang.php" class="btn btn-grey">Batal</a>
        </form>
    </div>
</div>
</body>
</html>
