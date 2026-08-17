<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') { header("Location: index.php"); exit; }
include 'config/koneksi.php';

// Ambil waktu mulai shift aktif milik user yang sedang login
$shift = mysqli_fetch_assoc(mysqli_query($conn, "SELECT start_time FROM shifts WHERE user_id = " . $_SESSION['user_id'] . " AND status = 'active' ORDER BY id DESC LIMIT 1"));
$shift_start = $shift['start_time'] ?? date('Y-m-d H:i:s');

// Query laporan hanya sejak shift dimulai
$sql = "SELECT * FROM transactions WHERE date >= '$shift_start' ORDER BY date DESC";
$result = mysqli_query($conn, $sql);

// Ambil tanggal filter dari input, default-nya adalah bulan berjalan
$dari_tanggal = $_GET['dari'] ?? date('Y-m-01');
$sampai_tanggal = $_GET['sampai'] ?? date('Y-m-d');

// Query transaksi berdasarkan rentang tanggal
$query = "SELECT * FROM transactions WHERE DATE(date) BETWEEN '$dari_tanggal' AND '$sampai_tanggal' ORDER BY date DESC";
$result = mysqli_query($conn, $query);

// Hitung total omzet pada periode tersebut
$q_total = mysqli_query($conn, "SELECT SUM(total) as total_omzet, COUNT(*) as total_transaksi FROM transactions WHERE DATE(date) BETWEEN '$dari_tanggal' AND '$sampai_tanggal'");
$stat = mysqli_fetch_assoc($q_total);

$page_title = "Laporan Penjualan - POS Kasir";
include 'includes/header.php';
?>

<div class="container py-4" style="max-width: 1100px;">
    <div class="bg-white p-4 rounded-4 shadow-sm">
        <!-- Header dengan Tombol Kembali ke Dashboard -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="fas fa-file-invoice-dollar text-primary me-2"></i>Laporan Penjualan Berdasarkan Periode</h4>
            <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <!-- Form Filter Tanggal -->
        <form method="GET" class="row g-3 mb-4 bg-light p-4 rounded-4 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control rounded-pill" value="<?= htmlspecialchars($dari_tanggal) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control rounded-pill" value="<?= htmlspecialchars($sampai_tanggal) ?>" required>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold">Filter Laporan</button>
                <a href="laporan.php" class="btn btn-outline-secondary rounded-pill px-3">Reset</a>
            </div>
        </form>

        <!-- Kartu Ringkasan Statistik Periode -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="p-3 border rounded-4 bg-success-subtle text-success d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small fw-semibold d-block">Total Omzet Periode Ini</span>
                        <h4 class="fw-bold mb-0">Rp <?= number_format($stat['total_omzet'] ?? 0) ?></h4>
                    </div>
                    <i class="fas fa-wallet fa-2x opacity-50"></i>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 border rounded-4 bg-primary-subtle text-primary d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small fw-semibold d-block">Jumlah Transaksi Berhasil</span>
                        <h4 class="fw-bold mb-0"><?= number_format($stat['total_transaksi'] ?? 0) ?> Transaksi</h4>
                    </div>
                    <i class="fas fa-shopping-cart fa-2x opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Tabel Detail Transaksi -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No Invoice</th>
                        <th>Tanggal & Waktu</th>
                        <th>Metode Bayar</th>
                        <th>Total Belanja</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(mysqli_num_rows($result) > 0):
                        while($row = mysqli_fetch_assoc($result)): 
                    ?>
                    <tr>
                        <td class="fw-semibold font-monospace"><?= htmlspecialchars($row['invoice_no']) ?></td>
                        <td class="text-muted small"><?= htmlspecialchars($row['date']) ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($row['payment_method'] ?? 'Tunai') ?></span></td>
                        <td class="fw-bold text-success">Rp <?= number_format($row['total']) ?></td>
                        <td class="text-center">
                            <a href="cetak.php?inv=<?= urlencode($row['invoice_no']) ?>" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-3 py-1">
                                <i class="fas fa-print me-1"></i> Struk
                            </a>
                        </td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Tidak ada data transaksi pada rentang tanggal tersebut.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>