<?php
session_start();
include 'config/koneksi.php';

if (isset($_POST['submit'])) {
    $type = $_POST['type'];
    $amount = $_POST['amount'];
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    mysqli_query($conn, "INSERT INTO cash_flow (type, amount, description) VALUES ('$type', '$amount', '$desc')");
    header("Location: arus_kas.php");
    exit;
}

$page_title = "Arus Kas - POS Kasir";
include 'includes/header.php';
?>
<div class="container py-4" style="max-width: 1000px;">
    <div class="bg-white p-4 rounded-4 shadow-sm">
        <!-- Header dengan Tombol Kembali ke Dashboard -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="fas fa-wallet text-warning me-2"></i>Manajemen Arus Kas</h4>
            <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <!-- Form Tambah Arus Kas -->
        <form method="POST" class="row g-3 mb-4 bg-light p-4 rounded-4">
            <h6 class="fw-bold mb-1">Catat Arus Kas Baru</h6>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Jenis</label>
                <select name="type" class="form-select rounded-pill">
                    <option value="masuk">Uang Masuk</option>
                    <option value="keluar">Uang Keluar</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted">Jumlah (Rp)</label>
                <input type="number" name="amount" class="form-control rounded-pill" placeholder="0" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted">Keterangan</label>
                <input type="text" name="description" class="form-control rounded-pill" placeholder="Contoh: Belanja modal / Tambah kas" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" name="submit" class="btn btn-primary w-100 rounded-pill fw-bold">Simpan</button>
            </div>
        </form>

        <!-- Tabel Riwayat Arus Kas -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Jenis</th>
                        <th class="text-end">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $res = mysqli_query($conn, "SELECT * FROM cash_flow ORDER BY date DESC");
                    if(mysqli_num_rows($res) > 0):
                        while($row = mysqli_fetch_assoc($res)): 
                    ?>
                    <tr>
                        <td class="text-muted small"><?= $row['date'] ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($row['description']) ?></td>
                        <td>
                            <?php if($row['type'] == 'masuk'): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Uang Masuk</span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Uang Keluar</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end fw-bold <?= $row['type'] == 'masuk' ? 'text-success' : 'text-danger' ?>">
                            <?= $row['type'] == 'masuk' ? '+' : '-' ?> Rp <?= number_format($row['amount']) ?>
                        </td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Belum ada catatan arus kas.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>