<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'config/koneksi.php';

// Data Statistik
$hari_ini = date('Y-m-d');
$q_omzet = mysqli_query($conn, "SELECT SUM(total) as omzet FROM transactions WHERE DATE(date) = '$hari_ini'");
$omzet_hari_ini = mysqli_fetch_assoc($q_omzet)['omzet'] ?? 0;

$q_saldo = mysqli_query($conn, "SELECT (SELECT IFNULL(SUM(amount),0) FROM cash_flow WHERE type='masuk') - (SELECT IFNULL(SUM(amount),0) FROM cash_flow WHERE type='keluar') as saldo");
$saldo_kas = mysqli_fetch_assoc($q_saldo)['saldo'] ?? 0;

$page_title = "Dashboard - POS Kasir";
include 'includes/header.php';
?>

<div class="container py-4" style="max-width: 1000px;">
    <!-- Statistik Box -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="p-4 rounded-4 bg-success text-white shadow-sm">
                <span class="small fw-semibold">Omzet Hari Ini</span>
                <h3 class="fw-bold mb-0">Rp <?= number_format($omzet_hari_ini) ?></h3>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-4 rounded-4 bg-info text-white shadow-sm">
                <span class="small fw-semibold">Sisa Saldo Kas</span>
                <h3 class="fw-bold mb-0">Rp <?= number_format($saldo_kas) ?></h3>
            </div>
        </div>
    </div>

    <!-- Menu Utama -->
    <div class="row g-3">
        <!-- Menu Kasir -->
        <div class="col-md-3">
            <a href="kasir.php" class="text-decoration-none">
                <div class="card border-0 rounded-4 bg-primary text-white p-4 text-center shadow-sm h-100">
                    <i class="fas fa-shopping-cart fa-2x mb-3"></i>
                    <h5 class="fw-bold">Kasir</h5>
                </div>
            </a>
        </div>

        <?php if($_SESSION['role'] == 'owner'): ?>
        <div class="col-md-3">
            <a href="produk.php" class="text-decoration-none">
                <div class="card border-0 rounded-4 bg-success text-white p-4 text-center shadow-sm h-100">
                    <i class="fas fa-box fa-2x mb-3"></i>
                    <h5 class="fw-bold">Produk</h5>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="arus_kas.php" class="text-decoration-none">
                <div class="card border-0 rounded-4 bg-warning text-dark p-4 text-center shadow-sm h-100">
                    <i class="fas fa-wallet fa-2x mb-3"></i>
                    <h5 class="fw-bold">Arus Kas</h5>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="kategori.php" class="text-decoration-none">
                <div class="card border-0 rounded-4 bg-secondary text-white p-4 text-center shadow-sm h-100">
                    <i class="fas fa-tags fa-2x mb-3"></i>
                    <h5 class="fw-bold">Kategori</h5>
                </div>
            </a>
        </div>

        <!-- Baris Bawah -->
        <div class="col-md-8">
            <a href="laporan.php" class="text-decoration-none">
                <div class="card border-0 rounded-4 bg-dark text-white p-3 text-center shadow-sm h-100 d-flex align-items-center justify-content-center">
                    <h5 class="fw-bold mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Laporan Transaksi</h5>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="pengaturan_toko.php" class="text-decoration-none">
                <div class="card border-0 rounded-4 bg-white text-primary p-3 text-center shadow-sm h-100 d-flex align-items-center justify-content-center border">
                    <h5 class="fw-bold mb-0"><i class="fas fa-store me-2"></i>Profil Toko</h5>
                </div>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>