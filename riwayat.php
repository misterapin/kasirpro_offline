<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') { header("Location: index.php"); exit; }
include 'config/koneksi.php';

$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
$where = $tanggal ? "WHERE DATE(date) = '$tanggal'" : "";

$page_title = "Laporan Transaksi - POS Kasir";
include 'includes/header.php';
?>

<div class="container py-4" style="max-width: 1000px;">
    <div class="bg-white p-4 rounded-4 shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="fas fa-file-invoice-dollar text-primary me-2"></i>Laporan Transaksi</h4>
            <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Kembali</a>
        </div>

        <form method="GET" class="row g-3 mb-4 align-items-end bg-light p-3 rounded-4">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Filter Berdasarkan Tanggal:</label>
                <input type="date" name="tanggal" value="<?= $tanggal ?>" class="form-control rounded-pill">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4">Tampilkan</button>
                <a href="riwayat.php" class="btn btn-outline-secondary rounded-pill px-3">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No Invoice</th>
                        <th>Tanggal</th>
                        <th>Metode</th>
                        <th>Total</th>
                        <th>Bayar</th>
                        <th>Kembali</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $query = mysqli_query($conn, "SELECT * FROM transactions $where ORDER BY date DESC");
                    $total_omzet = 0;
                    if(mysqli_num_rows($query) > 0):
                        while($row = mysqli_fetch_assoc($query)): 
                            $total_omzet += $row['total'];
                    ?>
                    <tr>
                        <td class="fw-bold text-primary"><?= $row['invoice_no'] ?></td>
                        <td class="small text-muted"><?= $row['date'] ?></td>
                        <td><span class="badge bg-light text-dark border"><?= $row['payment_method'] ?></span></td>
                        <td class="fw-semibold">Rp <?= number_format($row['total']) ?></td>
                        <td>Rp <?= number_format($row['cash_paid']) ?></td>
                        <td>Rp <?= number_format($row['cash_change']) ?></td>
                        <td>
                            <a href="cetak.php?inv=<?= $row['invoice_no'] ?>" class="btn btn-outline-info btn-sm rounded-pill px-3" target="_blank">Cetak</a>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data transaksi.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($tanggal): ?>
            <div class="alert alert-success mt-3 rounded-4 border-0 shadow-sm">
                <strong>Total Omzet Tanggal <?= $tanggal ?>:</strong> Rp <?= number_format($total_omzet) ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>