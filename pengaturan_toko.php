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

    // Logika Upload Logo
    $logo_sql = "";
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['logo']['tmp_name'];
        $file_name = $_FILES['logo']['name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($file_ext, $allowed_ext)) {
            $new_logo_name = 'logo_' . time() . '.' . $file_ext;
            $upload_dir = 'uploads/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            if (move_uploaded_file($file_tmp, $upload_dir . $new_logo_name)) {
                // Hapus logo lama jika ada
                $old_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT logo FROM settings WHERE id = 1"));
                if (!empty($old_data['logo']) && file_exists($upload_dir . $old_data['logo'])) {
                    unlink($upload_dir . $old_data['logo']);
                }
                $logo_sql = ", logo = '$new_logo_name'";
            }
        } else {
            $error = "Format logo harus berjenis JPG, JPEG, PNG, atau WEBP.";
        }
    }

    if (!$error) {
        $query = "UPDATE settings SET store_name = '$store_name', address = '$address', phone = '$phone', receipt_footer = '$receipt_footer', thermal_print_mode = '$thermal_print_mode' $logo_sql WHERE id = 1";
        if (mysqli_query($conn, $query)) { 
            $pesan = "Pengaturan toko & logo berhasil diperbarui!"; 
        } else { 
            $error = "Gagal memperbarui database."; 
        }
    }
}
$toko = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM settings WHERE id = 1"));

$page_title = "Pengaturan Toko - POS Kasir";
include 'includes/header.php';
?>

<div class="main-container py-3">
    <div class="bg-white p-4 rounded-4 shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="fas fa-store text-primary me-2"></i>Profil & Pengaturan Toko</h4>
            <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Kembali</a>
        </div>
        
        <?php if($pesan): ?><div class="alert alert-success rounded-4 py-2 small mb-3"><?= $pesan ?></div><?php endif; ?>
        <?php if($error): ?><div class="alert alert-danger rounded-4 py-2 small mb-3"><?= $error ?></div><?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row g-4">
                <!-- KOLOM KIRI: Informasi Utama Toko & Logo -->
                <div class="col-md-6">
                    <div class="p-3 border rounded-4 bg-light h-100 d-flex flex-column">
                        <h6 class="fw-bold text-dark mb-3"><i class="fas fa-info-circle me-1 text-primary"></i> Identitas & Logo Toko</h6>
                        
                        <div class="mb-3 text-center">
                            <?php if(!empty($toko['logo']) && file_exists('uploads/' . $toko['logo'])): ?>
                                <img src="uploads/<?= $toko['logo'] ?>" alt="Logo Toko" class="rounded-circle mb-2 border shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-secondary-subtle rounded-circle d-inline-flex align-items-center justify-content-center mb-2 text-secondary" style="width: 80px; height: 80px;">
                                    <i class="fas fa-store fa-2x"></i>
                                </div>
                            <?php endif; ?>
                            <div>
                                <label class="form-label small fw-bold text-muted d-block">Upload Logo Baru (JPG/PNG)</label>
                                <input type="file" name="logo" class="form-control form-control-sm rounded-pill">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Toko</label>
                            <input type="text" name="store_name" class="form-control rounded-pill" value="<?= htmlspecialchars($toko['store_name'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Alamat Toko</label>
                            <textarea name="address" class="form-control rounded-4" rows="2" required><?= htmlspecialchars($toko['address'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nomor Telepon</label>
                            <input type="text" name="phone" class="form-control rounded-pill" value="<?= htmlspecialchars($toko['phone'] ?? '') ?>">
                        </div>
                                                <div class="mt-4">
                            <button type="submit" name="simpan" class="btn btn-primary rounded-pill w-100 fw-bold py-2 shadow-sm">Simpan Perubahan</button>
                        </div>

                    </div>
                </div>

                <!-- KOLOM KANAN: Pengaturan Printer & Backup Database -->
                <div class="col-md-6">
                    <div class="p-3 border rounded-4 bg-light h-100 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-bold text-dark mb-3"><i class="fas fa-print me-1 text-success"></i> Perangkat & Data</h6>
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Pesan Penutup Struk (Footer)</label>
                                <input type="text" name="receipt_footer" class="form-control rounded-pill" value="<?= htmlspecialchars($toko['receipt_footer'] ?? '') ?>">
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold">Mode Cetak Printer Thermal 58mm</label>
                                <select name="thermal_print_mode" class="form-select rounded-pill">
                                    <option value="auto" <?= (isset($toko['thermal_print_mode']) && $toko['thermal_print_mode'] == 'auto') ? 'selected' : '' ?>>Otomatis (Langsung Cetak)</option>
                                    <option value="dialog" <?= (isset($toko['thermal_print_mode']) && $toko['thermal_print_mode'] == 'dialog') ? 'selected' : '' ?>>Manual (Preview Dialog)</option>
                                </select>
                            </div>

                            <hr class="text-muted my-3">

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Keamanan Data (Database)</label>
                                <a href="backup_db.php" class="btn btn-outline-dark rounded-pill w-100 py-2">
                                    <i class="fas fa-database me-2"></i> Download Backup Database[cite: 1]
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