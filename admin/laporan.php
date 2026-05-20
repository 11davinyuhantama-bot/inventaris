<?php
require_once '../includes/header_admin.php';
require_once '../includes/koneksi.php';

// Filter bulan & tahun
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('n');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

$sql = "SELECT p.*, u.nama AS nama_user, b.nama_barang, b.kondisi_barang
        FROM peminjaman p
        JOIN user u ON p.id_user = u.id_user
        JOIN barang b ON p.id_barang = b.id_barang
        WHERE MONTH(p.tanggal_pinjam) = $bulan AND YEAR(p.tanggal_pinjam) = $tahun
        ORDER BY p.tanggal_pinjam DESC";
$res = mysqli_query($koneksi, $sql);

// Ringkasan
$total     = mysqli_num_rows($res);
$dipinjam  = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM peminjaman WHERE status='dipinjam' AND MONTH(tanggal_pinjam)=$bulan AND YEAR(tanggal_pinjam)=$tahun"))[0];
$kembali   = $total - $dipinjam;

$nama_bulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
?>

<div class="card">
  <h2>📊 Laporan Peminjaman — <?= $nama_bulan[$bulan] ?> <?= $tahun ?></h2>

  <form method="GET" style="display:flex;gap:10px;align-items:flex-end;margin-bottom:20px;">
    <div>
      <label>Bulan</label>
      <select name="bulan" style="width:auto;">
        <?php for($i=1;$i<=12;$i++): ?>
          <option value="<?= $i ?>" <?= $i==$bulan?'selected':'' ?>><?= $nama_bulan[$i] ?></option>
        <?php endfor; ?>
      </select>
    </div>
    <div>
      <label>Tahun</label>
      <select name="tahun" style="width:auto;">
        <?php for($y=date('Y');$y>=2023;$y--): ?>
          <option value="<?= $y ?>" <?= $y==$tahun?'selected':'' ?>><?= $y ?></option>
        <?php endfor; ?>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Tampilkan</button>
  </form>

  <!-- Ringkasan -->
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px;">
    <div style="background:#1a3c5e;color:#fff;padding:15px;border-radius:8px;text-align:center;">
      <div style="font-size:26px;font-weight:bold;"><?= $total ?></div>
      <div style="font-size:13px;">Total Transaksi</div>
    </div>
    <div style="background:#e6a817;color:#fff;padding:15px;border-radius:8px;text-align:center;">
      <div style="font-size:26px;font-weight:bold;"><?= $dipinjam ?></div>
      <div style="font-size:13px;">Masih Dipinjam</div>
    </div>
    <div style="background:#27ae60;color:#fff;padding:15px;border-radius:8px;text-align:center;">
      <div style="font-size:26px;font-weight:bold;"><?= $kembali ?></div>
      <div style="font-size:13px;">Sudah Dikembalikan</div>
    </div>
  </div>

  <!-- Tabel Laporan -->
  <table>
    <thead>
      <tr><th>No</th><th>Peminjam</th><th>Barang</th><th>Kondisi</th><th>Jml</th><th>Tgl Pinjam</th><th>Tgl Kembali</th><th>Status</th></tr>
    </thead>
    <tbody>
    <?php
    mysqli_data_seek($res, 0);
    $no = 1;
    while ($row = mysqli_fetch_assoc($res)):
    ?>
      <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($row['nama_user']) ?></td>
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
      </tr>
    <?php endwhile; ?>
    <?php if ($total == 0): ?>
      <tr><td colspan="8" style="text-align:center;color:#888;padding:20px;">Tidak ada data untuk periode ini.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once '../includes/footer.php'; ?>
