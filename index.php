<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'config/koneksi.php';

// Data Statistik Hari Ini
$hari_ini = date('Y-m-d');
$q_omzet = mysqli_query($conn, "SELECT SUM(total) as omzet FROM transactions WHERE DATE(date) = '$hari_ini'");
$omzet_hari_ini = mysqli_fetch_assoc($q_omzet)['omzet'] ?? 0;

$q_saldo = mysqli_query($conn, "SELECT (SELECT IFNULL(SUM(amount),0) FROM cash_flow WHERE type='masuk') - (SELECT IFNULL(SUM(amount),0) FROM cash_flow WHERE type='keluar') as saldo");
$saldo_kas = mysqli_fetch_assoc($q_saldo)['saldo'] ?? 0;

// Hitung total produk
$q_produk = mysqli_query($conn, "SELECT COUNT(*) as total FROM products");
$total_produk = mysqli_fetch_assoc($q_produk)['total'] ?? 0;

// Pastikan variabel logo diambil di query pengaturan toko:
$q_toko = mysqli_query($conn, "SELECT store_name, address, logo FROM settings WHERE id = 1");
$toko = mysqli_fetch_assoc($q_toko);
$nama_toko = $toko['store_name'] ?? 'POS Kasir Pro';
$alamat_toko = $toko['address'] ?? 'Alamat toko belum diatur';
$logo_toko = $toko['logo'] ?? '';

$page_title = "Dashboard - POS Kasir Pro";
include 'includes/header.php';
?>

<div class="container py-4" style="max-width: 1100px;">
    
<div class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 rounded-4 shadow-sm">
    <div class="d-flex align-items-center gap-3">
        <?php if(!empty($logo_toko) && file_exists('uploads/' . $logo_toko)): ?>
            <img src="uploads/<?= $logo_toko ?>" alt="Logo" class="rounded-circle border shadow-sm" style="width: 60px; height: 60px; object-fit: cover;">
        <?php else: ?>
            <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                <i class="fas fa-store fa-lg"></i>
            </div>
        <?php endif; ?>
        <div>
            <h4 class="fw-bold text-dark mb-1"><?= htmlspecialchars($nama_toko) ?></h4>
            <p class="text-muted small mb-0"><i class="fas fa-map-marker-alt text-danger me-1"></i> <?= htmlspecialchars($alamat_toko) ?></p>
        </div>
    </div>
    <div class="text-end">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold mb-1 d-block">
            <i class="fas fa-shield-alt me-1"></i> <?= ucfirst($_SESSION['role']) ?> Mode
        </span>
        <span class="text-muted small">Login: <?= htmlspecialchars($_SESSION['fullname']) ?></span>
    </div>
</div>
    <!-- Statistik Box Modern (Omzet, Saldo Kas, Total Produk) -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 rounded-4 bg-success text-white shadow-sm p-3 h-100 position-relative overflow-hidden">
                <div class="card-body">
                    <div class="text-white-50 small fw-bold text-uppercase mb-1">Omzet Hari Ini</div>
                    <h3 class="fw-bold mb-0">Rp <?= number_format($omzet_hari_ini) ?></h3>
                    <div class="mt-3 small text-white-50"><i class="fas fa-chart-line me-1"></i> Penjualan langsung hari ini</div>
                </div>
                <i class="fas fa-wallet position-absolute text-white opacity-10" style="right: -15px; bottom: -15px; font-size: 6rem;"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 rounded-4 bg-info text-white shadow-sm p-3 h-100 position-relative overflow-hidden">
                <div class="card-body">
                    <div class="text-white-50 small fw-bold text-uppercase mb-1">Sisa Saldo Kas</div>
                    <h3 class="fw-bold mb-0">Rp <?= number_format($saldo_kas) ?></h3>
                    <div class="mt-3 small text-white-50"><i class="fas fa-coins me-1"></i> Total uang kas bersih</div>
                </div>
                <i class="fas fa-cash-register position-absolute text-white opacity-10" style="right: -15px; bottom: -15px; font-size: 6rem;"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 rounded-4 bg-dark text-white shadow-sm p-3 h-100 position-relative overflow-hidden">
                <div class="card-body">
                    <div class="text-white-50 small fw-bold text-uppercase mb-1">Katalog Produk</div>
                    <h3 class="fw-bold mb-0"><?= number_format($total_produk) ?> <span class="fs-6 fw-normal">Item</span></h3>
                    <div class="mt-3 small text-white-50"><i class="fas fa-box me-1"></i> Barang terdaftar di sistem</div>
                </div>
                <i class="fas fa-boxes position-absolute text-white opacity-10" style="right: -15px; bottom: -15px; font-size: 6rem;"></i>
            </div>
        </div>
    </div>

    <!-- Menu Navigasi Utama -->
    <h6 class="fw-bold text-secondary text-uppercase small mb-3 tracking-wide"><i class="fas fa-th-large me-1"></i> Menu Navigasi Cepat</h6>
    
    <div class="row g-3">
        <!-- Kasir (Selalu Ada) -->
        <div class="col-md-3 col-6">
            <a href="kasir.php" class="text-decoration-none">
                <div class="card border-0 rounded-4 bg-white text-dark p-4 text-center shadow-sm h-100 hover-card transition-all">
                    <div class="bg-primary-subtle text-primary d-inline-flex p-3 rounded-circle mx-auto mb-3" style="width: 65px; height: 65px; align-items: center; justify-content: center;">
                        <i class="fas fa-shopping-cart fa-lg"></i>
                    </div>
                    <h6 class="fw-bold mb-1 text-dark">Kasir / POS</h6>
                    <p class="text-muted small mb-0">Buka mesin kasir</p>
                </div>
            </a>
        </div>

        <?php if($_SESSION['role'] == 'owner'): ?>
        <!-- Produk -->
        <div class="col-md-3 col-6">
            <a href="produk.php" class="text-decoration-none">
                <div class="card border-0 rounded-4 bg-white text-dark p-4 text-center shadow-sm h-100 hover-card transition-all">
                    <div class="bg-success-subtle text-success d-inline-flex p-3 rounded-circle mx-auto mb-3" style="width: 65px; height: 65px; align-items: center; justify-content: center;">
                        <i class="fas fa-box fa-lg"></i>
                    </div>
                    <h6 class="fw-bold mb-1 text-dark">Kelola Produk</h6>
                    <p class="text-muted small mb-0">Tambah & edit barang</p>
                </div>
            </a>
        </div>

        <!-- Arus Kas -->
        <div class="col-md-3 col-6">
            <a href="arus_kas.php" class="text-decoration-none">
                <div class="card border-0 rounded-4 bg-white text-dark p-4 text-center shadow-sm h-100 hover-card transition-all">
                    <div class="bg-warning-subtle text-warning d-inline-flex p-3 rounded-circle mx-auto mb-3" style="width: 65px; height: 65px; align-items: center; justify-content: center;">
                        <i class="fas fa-wallet fa-lg"></i>
                    </div>
                    <h6 class="fw-bold mb-1 text-dark">Arus Kas</h6>
                    <p class="text-muted small mb-0">Uang masuk & keluar</p>
                </div>
            </a>
        </div>

        <!-- Kategori -->
        <div class="col-md-3 col-6">
            <a href="kategori.php" class="text-decoration-none">
                <div class="card border-0 rounded-4 bg-white text-dark p-4 text-center shadow-sm h-100 hover-card transition-all">
                    <div class="bg-secondary-subtle text-secondary d-inline-flex p-3 rounded-circle mx-auto mb-3" style="width: 65px; height: 65px; align-items: center; justify-content: center;">
                        <i class="fas fa-tags fa-lg"></i>
                    </div>
                    <h6 class="fw-bold mb-1 text-dark">Kategori</h6>
                    <p class="text-muted small mb-0">Pengelompokan item</p>
                </div>
            </a>
        </div>

        <!-- Laporan Penjualan -->
        <div class="col-md-4">
            <a href="laporan.php" class="text-decoration-none">
                <div class="card border-0 rounded-4 bg-white text-dark p-4 shadow-sm h-100 hover-card transition-all d-flex flex-row align-items-center">
                    <div class="bg-danger-subtle text-danger p-3 rounded-circle me-3" style="width: 55px; height: 55px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-file-invoice-dollar fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">Laporan Penjualan</h6>
                        <p class="text-muted small mb-0">Filter omzet berkala</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Manajemen User -->
        <div class="col-md-4">
            <a href="manajemen_user.php" class="text-decoration-none">
                <div class="card border-0 rounded-4 bg-white text-dark p-4 shadow-sm h-100 hover-card transition-all d-flex flex-row align-items-center">
                    <div class="bg-info-subtle text-info p-3 rounded-circle me-3" style="width: 55px; height: 55px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">Manajemen Pengguna</h6>
                        <p class="text-muted small mb-0">Kelola kasir & owner</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Profil Toko -->
        <div class="col-md-4">
            <a href="pengaturan_toko.php" class="text-decoration-none">
                <div class="card border-0 rounded-4 bg-white text-dark p-4 shadow-sm h-100 hover-card transition-all d-flex flex-row align-items-center">
                    <div class="bg-primary-subtle text-primary p-3 rounded-circle me-3" style="width: 55px; height: 55px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-store fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark">Profil & Pengaturan</h6>
                        <p class="text-muted small mb-0">Toko & backup database</p>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Tambahan CSS Kecil untuk Efek Hover Kartu Menu -->
<style>
    .hover-card {
        transition: all 0.25s ease-in-out;
    }
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
</style>

<?php include 'includes/footer.php'; ?>