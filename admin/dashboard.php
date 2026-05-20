<?php
require_once '../includes/header_admin.php';
require_once '../includes/koneksi.php';

$total_barang = mysqli_fetch_row(
    mysqli_query($koneksi, "SELECT COUNT(*) FROM barang")
)[0];

$total_user = mysqli_fetch_row(
    mysqli_query($koneksi, "SELECT COUNT(*) FROM user WHERE role='peminjam'")
)[0];

$total_pinjam = mysqli_fetch_row(
    mysqli_query($koneksi, "SELECT COUNT(*) FROM peminjaman WHERE status='dipinjam'")
)[0];

$total_kembali = mysqli_fetch_row(
    mysqli_query($koneksi, "SELECT COUNT(*) FROM peminjaman WHERE status='dikembalikan'")
)[0];
?>

<style>
    body {
        background: #eef3f9;
    }

    .hero-dashboard {
        background: linear-gradient(135deg, #0f172a, #1e3a5f);
        border-radius: 22px;
        padding: 35px;
        color: white;
        margin-bottom: 25px;
        position: relative;
        overflow: hidden;
    }

    .hero-dashboard::before {
        content: '';
        position: absolute;
        width: 250px;
        height: 250px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
        top: -90px;
        right: -80px;
    }

    .hero-dashboard h1 {
        font-size: 32px;
        margin-bottom: 10px;
    }

    .hero-dashboard p {
        color: rgba(255,255,255,0.85);
        font-size: 15px;
    }

    .hero-dashboard strong {
        color: #fff;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }

    .dashboard-box {
        background: white;
        border-radius: 18px;
        padding: 25px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        transition: 0.25s;
        border: 1px solid #edf1f7;
    }

    .dashboard-box:hover {
        transform: translateY(-5px);
    }

    .dashboard-icon {
        width: 55px;
        height: 55px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 18px;
    }

    .icon-blue {
        background: #dbeafe;
    }

    .icon-green {
        background: #dcfce7;
    }

    .icon-orange {
        background: #fef3c7;
    }

    .icon-purple {
        background: #ede9fe;
    }

    .dashboard-number {
        font-size: 36px;
        font-weight: bold;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .dashboard-text {
        color: #64748b;
        font-size: 14px;
    }

    .card-table {
        background: white;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        border: 1px solid #edf1f7;
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .table-header h2 {
        color: #0f172a;
        font-size: 24px;
    }

    .mini-badge {
        background: #fff7ed;
        color: #c2410c;
        padding: 7px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead th {
        background: #f8fafc;
        color: #334155;
        padding: 15px;
        font-size: 13px;
        text-transform: uppercase;
        border-bottom: 2px solid #e2e8f0;
    }

    tbody td {
        padding: 16px 15px;
        border-bottom: 1px solid #edf2f7;
        font-size: 14px;
        color: #334155;
    }

    tbody tr:hover {
        background: #f8fbff;
    }

    .badge-dipinjam {
        background: #fef3c7;
        color: #92400e;
        padding: 6px 12px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .empty-row {
        text-align: center;
        color: #94a3b8;
        padding: 30px !important;
        font-style: italic;
    }

    .user-name {
        font-weight: 600;
        color: #0f172a;
    }
</style>

<div class="hero-dashboard">

    <h1>📊 Dashboard Inventaris</h1>

    <p>
        Selamat datang kembali,
        <strong><?= $_SESSION['nama'] ?></strong>.
        Kelola data inventaris sekolah dengan lebih mudah dan cepat.
    </p>

</div>

<div class="dashboard-grid">

    <div class="dashboard-box">

        <div class="dashboard-icon icon-blue">
            📦
        </div>

        <div class="dashboard-number">
            <?= $total_barang ?>
        </div>

        <div class="dashboard-text">
            Total Barang Inventaris
        </div>

    </div>

    <div class="dashboard-box">

        <div class="dashboard-icon icon-green">
            👥
        </div>

        <div class="dashboard-number">
            <?= $total_user ?>
        </div>

        <div class="dashboard-text">
            Total Peminjam
        </div>

    </div>

    <div class="dashboard-box">

        <div class="dashboard-icon icon-orange">
            📋
        </div>

        <div class="dashboard-number">
            <?= $total_pinjam ?>
        </div>

        <div class="dashboard-text">
            Barang Dipinjam
        </div>

    </div>

    <div class="dashboard-box">

        <div class="dashboard-icon icon-purple">
            ✅
        </div>

        <div class="dashboard-number">
            <?= $total_kembali ?>
        </div>

        <div class="dashboard-text">
            Barang Dikembalikan
        </div>

    </div>

</div>

<div class="card-table">

    <div class="table-header">

        <h2>📋 Peminjaman Aktif</h2>

        <div class="mini-badge">
            10 Data Terbaru
        </div>

    </div>

    <div class="table-wrapper">

        <table>

            <thead>
                <tr>
                    <th>Peminjam</th>
                    <th>Barang</th>
                    <th>Jumlah</th>
                    <th>Tanggal Pinjam</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

                <?php
                $sql = "
                SELECT 
                    p.*, 
                    u.nama, 
                    b.nama_barang 
                FROM peminjaman p
                JOIN user u ON p.id_user = u.id_user
                JOIN barang b ON p.id_barang = b.id_barang
                WHERE p.status = 'dipinjam'
                ORDER BY p.tanggal_pinjam DESC
                LIMIT 10
                ";

                $res = mysqli_query($koneksi, $sql);

                if (mysqli_num_rows($res) > 0):
                    while ($row = mysqli_fetch_assoc($res)):
                ?>

                        <tr>

                            <td class="user-name">
                                <?= htmlspecialchars($row['nama']) ?>
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
                                <span class="badge-dipinjam">
                                    Dipinjam
                                </span>
                            </td>

                        </tr>

                    <?php
                    endwhile;
                else:
                    ?>

                    <tr>
                        <td colspan="5" class="empty-row">
                            Belum ada data peminjaman aktif
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<?php require_once '../includes/footer.php'; ?>