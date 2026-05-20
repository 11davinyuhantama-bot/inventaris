<?php
require_once '../includes/header_user.php';
require_once '../includes/koneksi.php';

$pesan = '';

// --- PROSES PINJAM ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_barang'])) {
    $id_user   = $_SESSION['id_user'];
    $id_barang = (int)$_POST['id_barang'];
    $jumlah    = (int)$_POST['jumlah'];

    if ($jumlah < 1) {
        $pesan = 'gagal|Jumlah harus minimal 1!';
    } else {
        $result = mysqli_query($koneksi, "CALL pinjam_barang($id_user, $id_barang, $jumlah)");
        $row = mysqli_fetch_assoc($result);
        if ($row['hasil'] == 'sukses') {
            $pesan = 'sukses|Peminjaman berhasil! Stok barang berkurang otomatis.';
        } else {
            $pesan = 'gagal|Stok tidak mencukupi atau barang tidak tersedia!';
        }
        mysqli_next_result($koneksi);
    }
}

// Ambil daftar barang
$barang_list = mysqli_query($koneksi, "SELECT *, status_barang(jumlah) AS status FROM barang ORDER BY nama_barang");

if ($pesan):
    [$tipe, $msg] = explode('|', $pesan, 2);
endif;
?>

<div class="card">
  <h2>📦 Daftar Barang Inventaris</h2>
  <?php if ($pesan): ?>
    <div class="alert alert-<?= $tipe == 'sukses' ? 'success' : 'danger' ?>"><?= $msg ?></div>
  <?php endif; ?>

  <table>
    <thead>
      <tr><th>No</th><th>Nama Barang</th><th>Stok</th><th>Kondisi</th><th>Status</th><th>Pinjam</th></tr>
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
          <?php if ($row['status'] == 'Tersedia' && $row['kondisi_barang'] == 'baik'): ?>
            <!-- Form pinjam inline -->
            <form method="POST" style="display:flex;gap:6px;align-items:center;">
              <input type="hidden" name="id_barang" value="<?= $row['id_barang'] ?>">
              <input type="number" name="jumlah" min="1" max="<?= $row['jumlah'] ?>"
                     value="1" style="width:65px;margin:0;padding:6px;">
              <button type="submit" class="btn btn-primary"
                      onclick="return confirm('Pinjam barang ini?')">Pinjam</button>
            </form>
          <?php elseif ($row['kondisi_barang'] == 'rusak'): ?>
            <span style="color:#c0392b;font-size:13px;">Rusak</span>
          <?php else: ?>
            <span style="color:#aaa;font-size:13px;">Habis</span>
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php require_once '../includes/footer.php'; ?>
