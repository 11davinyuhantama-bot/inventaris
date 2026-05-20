<?php
require_once '../includes/header_admin.php';
require_once '../includes/koneksi.php';

$pesan = '';

// --- TAMBAH BARANG ---
if (isset($_POST['aksi']) && $_POST['aksi'] == 'tambah') {
    $nama    = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
    $jumlah  = (int)$_POST['jumlah'];
    $kondisi = $_POST['kondisi_barang'] == 'rusak' ? 'rusak' : 'baik';
    $sql = "INSERT INTO barang (nama_barang, jumlah, kondisi_barang)
            VALUES ('$nama', $jumlah, '$kondisi')";
    if (mysqli_query($koneksi, $sql)) $pesan = 'sukses|Barang berhasil ditambahkan!';
    else $pesan = 'gagal|Gagal menambahkan barang.';
}

// --- EDIT BARANG ---
if (isset($_POST['aksi']) && $_POST['aksi'] == 'edit') {
    $id      = (int)$_POST['id_barang'];
    $nama    = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
    $jumlah  = (int)$_POST['jumlah'];
    $kondisi = $_POST['kondisi_barang'] == 'rusak' ? 'rusak' : 'baik';
    $sql = "UPDATE barang SET nama_barang='$nama', jumlah=$jumlah, kondisi_barang='$kondisi'
            WHERE id_barang=$id";
    if (mysqli_query($koneksi, $sql)) $pesan = 'sukses|Data barang berhasil diubah!';
    else $pesan = 'gagal|Gagal mengubah data.';
}

// --- HAPUS BARANG ---
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    // Cek apakah masih ada peminjaman aktif
    $cek = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM peminjaman WHERE id_barang=$id AND status='dipinjam'"))[0];
    if ($cek > 0) {
        $pesan = 'gagal|Tidak bisa hapus: barang masih dipinjam!';
    } else {
        mysqli_query($koneksi, "DELETE FROM barang WHERE id_barang=$id");
        $pesan = 'sukses|Barang berhasil dihapus.';
    }
}

// Data edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $edit_data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM barang WHERE id_barang=$id"));
}

// Ambil semua barang
$barang_list = mysqli_query($koneksi, "SELECT *, status_barang(jumlah) AS status FROM barang ORDER BY id_barang DESC");

// Tampil pesan
if ($pesan):
    [$tipe, $msg] = explode('|', $pesan, 2);
    $kelas = $tipe == 'sukses' ? 'alert-success' : 'alert-danger';
endif;
?>

<div class="card">
  <h2><?= $edit_data ? '✏️ Edit Barang' : '➕ Tambah Barang' ?></h2>
  <?php if ($pesan): ?>
    <div class="alert <?= $kelas ?>"><?= $msg ?></div>
  <?php endif; ?>

  <form method="POST">
    <input type="hidden" name="aksi" value="<?= $edit_data ? 'edit' : 'tambah' ?>">
    <?php if ($edit_data): ?>
      <input type="hidden" name="id_barang" value="<?= $edit_data['id_barang'] ?>">
    <?php endif; ?>
    <label>Nama Barang</label>
    <input type="text" name="nama_barang" value="<?= $edit_data ? htmlspecialchars($edit_data['nama_barang']) : '' ?>" required>
    <label>Jumlah</label>
    <input type="number" name="jumlah" min="0" value="<?= $edit_data ? $edit_data['jumlah'] : '' ?>" required>
    <label>Kondisi Barang</label>
    <select name="kondisi_barang">
      <option value="baik" <?= ($edit_data && $edit_data['kondisi_barang']=='baik') ? 'selected' : '' ?>>Baik</option>
      <option value="rusak" <?= ($edit_data && $edit_data['kondisi_barang']=='rusak') ? 'selected' : '' ?>>Rusak</option>
    </select>
    <button type="submit" class="btn btn-primary">
      <?= $edit_data ? 'Simpan Perubahan' : 'Tambah Barang' ?>
    </button>
    <?php if ($edit_data): ?>
      &nbsp; <a href="barang.php" class="btn btn-warning">Batal</a>
    <?php endif; ?>
  </form>
</div>

<div class="card">
  <h2>📦 Daftar Barang Inventaris</h2>
  <table>
    <thead>
      <tr><th>No</th><th>Nama Barang</th><th>Jumlah</th><th>Kondisi</th><th>Status</th><th>Aksi</th></tr>
    </thead>
    <tbody>
    <?php $no=1; while ($row = mysqli_fetch_assoc($barang_list)): ?>
      <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
        <td><?= $row['jumlah'] ?></td>
        <td><span class="badge-<?= $row['kondisi_barang'] ?>"><?= ucfirst($row['kondisi_barang']) ?></span></td>
        <td>
          <?php if ($row['status'] == 'Tersedia'): ?>
            <span class="badge-tersedia">Tersedia</span>
          <?php else: ?>
            <span class="badge-habis">Habis</span>
          <?php endif; ?>
        </td>
        <td>
          <a href="barang.php?edit=<?= $row['id_barang'] ?>" class="btn btn-warning">Edit</a>
          <a href="barang.php?hapus=<?= $row['id_barang'] ?>" class="btn btn-danger"
             onclick="return confirm('Yakin hapus barang ini?')">Hapus</a>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php require_once '../includes/footer.php'; ?>
