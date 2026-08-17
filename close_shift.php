<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Cek apakah sudah diizinkan oleh Owner
if (!isset($_SESSION['izin_close_shift']) || $_SESSION['izin_close_shift'] !== true) {
    header("Location: verifikasi_shift.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil info shift aktif kasir
$shift = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM shifts WHERE user_id = $user_id AND status = 'active' ORDER BY id DESC LIMIT 1"));
$start_time = $shift['start_time'] ?? date('Y-m-d 00:00:00');
$shift_id = $shift['id'] ?? 0;

// Hitung total omzet shift ini
$q_trans = mysqli_query($conn, "SELECT SUM(total) as total_omzet, COUNT(*) as jumlah_transaksi FROM transactions WHERE date >= '$start_time'");
$data = mysqli_fetch_assoc($q_trans);
$total_omzet = $data['total_omzet'] ?? 0;
$jumlah_transaksi = $data['jumlah_transaksi'] ?? 0;

$page_title = "Close Shift - POS Kasir";
include 'includes/header.php';
?>

<div class="container py-4" style="max-width: 450px;">
    <div class="bg-white p-4 rounded-4 shadow-sm text-center">
        <div class="bg-success bg-opacity-15 d-inline-flex p-3 rounded-circle mb-3 text-success" style="width: 70px; height: 70px; align-items: center; justify-content: center;">
            <i class="fas fa-cash-register fa-2x"></i>
        </div>
        <h4 class="fw-bold mb-1">Rekap Shift Kasir</h4>
        <p class="text-muted small mb-4">Akses diizinkan. Silakan cetak rekap dan tutup shift.</p>
        
        <div class="bg-light p-3 rounded-4 mb-4 text-start">
            <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Mulai Shift:</span>
                <span class="fw-semibold"><?= $start_time ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Total Transaksi:</span>
                <span class="fw-semibold"><?= $jumlah_transaksi ?> Struk</span>
            </div>
            <hr class="text-muted my-2">
            <div class="text-center">
                <span class="text-muted small d-block mb-1">Total Omzet Terkumpul</span>
                <h3 class="fw-bold text-success mb-0">Rp <?= number_format($total_omzet) ?></h3>
            </div>
        </div>

        <!-- Tombol Cetak & Tutup Shift -->
        <button onclick="cetakDanTutup(<?= $shift_id ?>, <?= $total_omzet ?>, <?= $jumlah_transaksi ?>)" class="btn btn-danger w-100 rounded-pill fw-bold py-2 mb-2 shadow-sm">
            <i class="fas fa-print me-2"></i> Cetak Struk Thermal & Tutup Shift
        </button>
        <a href="index.php" class="btn btn-outline-secondary w-100 rounded-pill py-2 small">Kembali ke Dashboard</a>
    </div>
</div>

<script>
function cetakDanTutup(shiftId, omzet, jumlah) {
    const printWindow = window.open('cetak_close_shift.php?id=' + shiftId + '&omzet=' + omzet + '&jumlah=' + jumlah, '_blank', 'width=350,height=600');
    
    printWindow.onload = function() {
        setTimeout(function() { window.location.href = 'logout.php'; }, 1000);
    };
    setTimeout(function() { window.location.href = 'logout.php'; }, 2000);
}
</script>

<?php include 'includes/footer.php'; ?>