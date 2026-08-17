<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') { header("Location: index.php"); exit; }
include 'config/koneksi.php';

$pesan = ''; $error = '';
if (isset($_POST['simpan'])) {
    $store_name         = mysqli_real_escape_string($conn, $_POST['store_name']);
    $address            = mysqli_real_escape_string($conn, $_POST['address']);
    $phone              = mysqli_real_escape_string($conn, $_POST['phone']);
    $receipt_footer     = mysqli_real_escape_string($conn, $_POST['receipt_footer']);
    $thermal_print_mode = mysqli_real_escape_string($conn, $_POST['thermal_print_mode']);

    $query = "UPDATE settings SET store_name = '$store_name', address = '$address', phone = '$phone', receipt_footer = '$receipt_footer', thermal_print_mode = '$thermal_print_mode' WHERE id = 1";
    if (mysqli_query($conn, $query)) { 
        $pesan = "Pengaturan printer & toko berhasil diperbarui!"; 
    } else { 
        $error = "Gagal memperbarui."; 
    }
}
$toko = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM settings WHERE id = 1"));

$page_title = "Pengaturan Toko - POS Kasir";
include 'includes/header.php';
?>

<div class="container py-4" style="max-width: 1000px;">
    <div class="bg-white p-4 rounded-4 shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="fas fa-store text-primary me-2"></i>Profil & Pengaturan Toko</h4>
            <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Kembali</a>
        </div>
        
        <?php if($pesan): ?><div class="alert alert-success rounded-4 py-2 small mb-3"><?= $pesan ?></div><?php endif; ?>
        <?php if($error): ?><div class="alert alert-danger rounded-4 py-2 small mb-3"><?= $error ?></div><?php endif; ?>

        <form method="POST">
            <div class="row g-4">
                <!-- KOLOM KIRI: Informasi Utama Toko -->
                <div class="col-md-6">
                    <div class="p-3 border rounded-4 bg-light h-100 d-flex flex-column">
                        <h6 class="fw-bold text-dark mb-3"><i class="fas fa-info-circle me-1 text-primary"></i> Informasi Identitas Toko</h6>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Toko</label>
                            <input type="text" name="store_name" class="form-control rounded-pill" value="<?= htmlspecialchars($toko['store_name'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Alamat Toko</label>
                            <textarea name="address" class="form-control rounded-4" rows="3" required><?= htmlspecialchars($toko['address'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nomor Telepon</label>
                            <input type="text" name="phone" class="form-control rounded-pill" value="<?= htmlspecialchars($toko['phone'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Pesan Penutup Struk (Footer)</label>
                            <input type="text" name="receipt_footer" class="form-control rounded-pill" value="<?= htmlspecialchars($toko['receipt_footer'] ?? '') ?>">
                        </div>
                                                <!-- Tombol Simpan di Bagian Bawah Kolom Kanan -->
                        <div class="mt-4">
                            <button type="submit" name="simpan" class="btn btn-primary rounded-pill w-100 fw-bold py-2 shadow-sm">Simpan Perubahan</button>
                        </div>

                    </div>
                </div>

                <!-- KOLOM KANAN: Pengaturan Printer & Backup Database -->
                <div class="col-md-6">
                    <div class="p-3 border rounded-4 bg-light h-100 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-bold text-dark mb-3"><i class="fas fa-print me-1 text-success"></i> Pengaturan Perangkat & Data</h6>
                            
                            <div class="mb-4">
                                <label class="form-label small fw-bold">Mode Cetak Printer Thermal 58mm</label>
                                <select name="thermal_print_mode" class="form-select rounded-pill">
                                    <option value="auto" <?= (isset($toko['thermal_print_mode']) && $toko['thermal_print_mode'] == 'auto') ? 'selected' : '' ?>>Otomatis (Langsung Cetak)</option>
                                    <option value="dialog" <?= (isset($toko['thermal_print_mode']) && $toko['thermal_print_mode'] == 'dialog') ? 'selected' : '' ?>>Manual (Tampilkan Dialog Preview)</option>
                                </select>
                                <div class="form-text small text-muted mt-1">Pilih "Otomatis" agar struk langsung dikirim ke printer thermal.</div>
                            </div>

                            <hr class="text-muted my-3">

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Keamanan Data (Database)</label>
                                <p class="small text-muted mb-2">Unduh salinan data transaksi, produk, dan toko untuk cadangan.</p>
                                <a href="backup_db.php" class="btn btn-outline-dark rounded-pill w-100 py-2">
                                    <i class="fas fa-database me-2"></i> Download Backup Database
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>