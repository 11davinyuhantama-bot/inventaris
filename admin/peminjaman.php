<?php
require_once '../includes/header_admin.php';
require_once '../includes/koneksi.php';

$pesan = '';

// --- PROSES KEMBALIKAN ---
if (isset($_GET['kembali'])) {

    $id = (int)$_GET['kembali'];

    $result = mysqli_query($koneksi, "CALL kembalikan_barang($id)");
    $row = mysqli_fetch_assoc($result);

    if ($row['hasil'] == 'sukses') {
        $pesan = 'sukses|Barang berhasil dikembalikan dan stok bertambah kembali!';
    } else {
        $pesan = 'gagal|Barang sudah pernah dikembalikan sebelumnya.';
    }

    mysqli_next_result($koneksi); // Bersihkan buffer setelah CALL
}

// Filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'semua';

$where = '';

if ($filter == 'dipinjam') {
    $where = "WHERE p.status = 'dipinjam'";
}

if ($filter == 'dikembalikan') {
    $where = "WHERE p.status = 'dikembalikan'";
}

$sql = "
SELECT 
    p.*, 
    u.nama AS nama_user, 
    b.nama_barang
FROM peminjaman p
JOIN user u ON p.id_user = u.id_user
JOIN barang b ON p.id_barang = b.id_barang
$where
ORDER BY p.tanggal_pinjam DESC
";

$res = mysqli_query($koneksi, $sql);

if ($pesan):
    [$tipe, $msg] = explode('|', $pesan, 2);
endif;
?>

<style>
    .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 22px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .filter-group {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .table-wrapper {
        overflow-x: auto;
        border-radius: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        overflow: hidden;
        border-radius: 10px;
        background: #fff;
    }

    thead th {
        background: #1a3c5e;
        color: #fff;
        padding: 14px 12px;
        font-size: 14px;
        text-align: left;
        white-space: nowrap;
    }

    tbody td {
        padding: 13px 12px;
        border-bottom: 1px solid #eee;
        font-size: 14px;
        vertical-align: middle;
    }

    tbody tr:hover {
        background: #f8fbff;
        transition: 0.2s;
    }

    .status-box {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .badge-dipinjam,
    .badge-kembali {
        padding: 5px 11px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-dipinjam {
        background: #fff3cd;
        color: #856404;
    }

    .badge-kembali {
        background: #d4edda;
        color: #155724;
    }

    .btn {
        border: none;
        padding: 8px 14px;
        border-radius: 7px;
        font-size: 13px;
        text-decoration: none;
        display: inline-block;
        transition: 0.2s;
        cursor: pointer;
    }

    .btn:hover {
        transform: translateY(-1px);
        opacity: 0.9;
    }

    .btn-primary {
        background: #1a3c5e;
        color: #fff;
    }

    .btn-warning {
        background: #f0ad4e;
        color: #fff;
    }

    .btn-success {
        background: #27ae60;
        color: #fff;
    }

    .alert {
        padding: 14px 18px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
    }

    .empty-text {
        color: #999;
        font-size: 13px;
        font-style: italic;
    }

    .card {
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(6px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="card">

    <div class="top-bar">
        <div>
            <h2>📋 Data Peminjaman</h2>
            <p style="font-size:13px;color:#666;margin-top:5px;">
                Kelola data peminjaman dan pengembalian barang inventaris sekolah
            </p>
        </div>

        <div class="filter-group">
            <a href="peminjaman.php?filter=semua" class="btn btn-primary">
                Semua
            </a>

            <a href="peminjaman.php?filter=dipinjam" class="btn btn-warning">
                Dipinjam
            </a>

            <a href="peminjaman.php?filter=dikembalikan" class="btn btn-success">
                Dikembalikan
            </a>
        </div>
    </div>

    <?php if ($pesan): ?>
        <div class="alert alert-<?= $tipe == 'sukses' ? 'success' : 'danger' ?>">
            <?= $msg ?>
        </div>
    <?php endif; ?>

    <div class="table-wrapper">

        <table>

            <thead>
                <tr>
                    <th>No</th>
                    <th>Peminjam</th>
                    <th>Barang</th>
                    <th>Jumlah</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Status</th>
                    <th style="width:150px;">Aksi</th>
                </tr>
            </thead>

            <tbody>

                <?php $no = 1; ?>

                <?php while ($row = mysqli_fetch_assoc($res)): ?>

                    <tr>

                        <td><?= $no++ ?></td>

                        <td>
                            <strong>
                                <?= htmlspecialchars($row['nama_user']) ?>
                            </strong>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['nama_barang']) ?>
                        </td>

                        <td>
                            <?= $row['jumlah_pinjam'] ?>
                        </td>

                        <td>
                            <?= $row['tanggal_pinjam'] ?>
                        </td>

                        <td>
                            <?= $row['tanggal_kembali'] ?? '-' ?>
                        </td>

                        <td>

                            <?php if ($row['status'] == 'dipinjam'): ?>

                                <span class="badge-dipinjam">
                                    Dipinjam
                                </span>

                            <?php else: ?>

                                <span class="badge-kembali">
                                    Dikembalikan
                                </span>

                            <?php endif; ?>

                        </td>

                        <td>

                            <?php if ($row['status'] == 'dipinjam'): ?>

                                <a href="peminjaman.php?kembali=<?= $row['id_pinjam'] ?>"
                                   class="btn btn-success"
                                   onclick="return confirm('Konfirmasi pengembalian barang ini?')">

                                    Kembalikan

                                </a>

                            <?php else: ?>

                                <span class="empty-text">
                                    ✔ Selesai
                                </span>

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</div>

<?php require_once '../includes/footer.php'; ?>