<?php
require_once '../includes/header_user.php';
require_once '../includes/koneksi.php';

$id_user = $_SESSION['id_user'];
$pesan = '';

// --- PROSES KEMBALIKAN ---
if (isset($_GET['kembali'])) {
    $id_pinjam = (int)$_GET['kembali'];

    // Pastikan peminjaman ini milik user yang login
    $cek = mysqli_fetch_assoc(mysqli_query($koneksi,
        "SELECT * FROM peminjaman WHERE id_pinjam=$id_pinjam AND id_user=$id_user"));

    if (!$cek) {
        $pesan = 'gagal|Data tidak ditemukan!';
    } elseif ($cek['status'] == 'dikembalikan') {
        $pesan = 'gagal|Barang sudah pernah dikembalikan!';
    } else {
        $result = mysqli_query($koneksi, "CALL kembalikan_barang($id_pinjam)");
        $row = mysqli_fetch_assoc($result);
        mysqli_next_result($koneksi);
        if ($row['hasil'] == 'sukses') {
            $pesan = 'sukses|Barang berhasil dikembalikan! Stok bertambah kembali.';
        } else {
            $pesan = 'gagal|Gagal mengembalikan barang.';
        }
    }
}

$sql = "SELECT p.*, b.nama_barang, b.kondisi_barang
        FROM peminjaman p
        JOIN barang b ON p.id_barang = b.id_barang
        WHERE p.id_user = $id_user
        ORDER BY p.tanggal_pinjam DESC";
$res = mysqli_query($koneksi, $sql);

$total   = mysqli_num_rows($res);
$aktif   = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM peminjaman WHERE id_user=$id_user AND status='dipinjam'"))[0];
$selesai = $total - $aktif;

if ($pesan) [$tipe, $msg] = explode('|', $pesan, 2);
?>

<div class="card">
  <h2>📋 Riwayat Peminjaman Saya</h2>

  <?php if ($pesan): ?>
    <div class="alert alert-<?= $tipe == 'sukses' ? 'success' : 'danger' ?>"><?= $msg ?></div>
  <?php endif; ?>

  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px;">
    <div style="background:#1a3c5e;color:#fff;padding:15px;border-radius:8px;text-align:center;">
      <div style="font-size:24px;font-weight:bold;"><?= $total ?></div>
      <div style="font-size:13px;">Total Pinjam</div>
    </div>
    <div style="background:#e6a817;color:#fff;padding:15px;border-radius:8px;text-align:center;">
      <div style="font-size:24px;font-weight:bold;"><?= $aktif ?></div>
      <div style="font-size:13px;">Masih Dipinjam</div>
    </div>
    <div style="background:#27ae60;color:#fff;padding:15px;border-radius:8px;text-align:center;">
      <div style="font-size:24px;font-weight:bold;"><?= $selesai ?></div>
      <div style="font-size:13px;">Selesai</div>
    </div>
  </div>

  <table>
    <thead>
      <tr><th>No</th><th>Barang</th><th>Kondisi</th><th>Jml</th><th>Tgl Pinjam</th><th>Tgl Kembali</th><th>Status</th><th>Aksi</th></tr>
    </thead>
    <tbody>
    <?php
    mysqli_data_seek($res, 0);
    $no = 1;
    while ($row = mysqli_fetch_assoc($res)):
    ?>
      <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
        <td><span class="badge-<?= $row['kondisi_barang'] ?>"><?= ucfirst($row['kondisi_barang']) ?></span></td>
        <td><?= $row['jumlah_pinjam'] ?></td>
        <td><?= $row['tanggal_pinjam'] ?></td>
        <td><?= $row['tanggal_kembali'] ?? '-' ?></td>
        <td>
          <?php if ($row['status'] == 'dipinjam'): ?>
            <span class="badge-dipinjam">Dipinjam</span>
          <?php else: ?>
            <span class="badge-kembali">Dikembalikan</span>
          <?php endif; ?>
        </td>
        <td>
          <?php if ($row['status'] == 'dipinjam'): ?>
            <a href="riwayat.php?kembali=<?= $row['id_pinjam'] ?>"
               class="btn btn-success"
               onclick="return confirm('Kembalikan barang <?= htmlspecialchars($row['nama_barang']) ?>?')">
              Kembalikan
            </a>
          <?php else: ?>
            <span style="color:#aaa;font-size:13px;">✓ Selesai</span>
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
    <?php if ($total == 0): ?>
      <tr><td colspan="8" style="text-align:center;color:#888;padding:20px;">Belum ada riwayat peminjaman.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once '../includes/footer.php'; ?>