<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') { header("Location: index.php"); exit; }
include 'config/koneksi.php';

if (isset($_POST['tambah'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    mysqli_query($conn, "INSERT INTO categories (name) VALUES ('$name')");
    header("Location: kategori.php");
    exit;
}

if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM categories WHERE id = $id");
    header("Location: kategori.php");
    exit;
}

$page_title = "Kategori Produk - POS Kasir";
include 'includes/header.php';
?>
<div class="container py-4" style="max-width: 800px;">
    <div class="bg-white p-4 rounded-4 shadow-sm">
        <!-- Header dengan Tombol Kembali ke Dashboard -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="fas fa-tags text-secondary me-2"></i>Kelola Kategori Produk</h4>
            <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <!-- Form Tambah Kategori (Lebar & Proporsional) -->
        <div class="card border-0 bg-light p-4 rounded-4 mb-4">
            <h6 class="fw-bold mb-3">Tambah Kategori Baru</h6>
            <form method="POST" class="row g-3">
                <div class="col-md-9">
                    <input type="text" name="name" class="form-control form-control-lg rounded-pill fs-6" placeholder="Masukkan nama kategori (contoh: Makanan, Minuman)..." required autofocus>
                </div>
                <div class="col-md-3">
                    <button type="submit" name="tambah" class="btn btn-primary btn-lg rounded-pill w-100 fw-bold fs-6">Simpan</button>
                </div>
            </form>
        </div>

        <!-- Tabel Daftar Kategori -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nama Kategori</th>
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $res = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC"); 
                    if(mysqli_num_rows($res) > 0):
                        while($c = mysqli_fetch_assoc($res)): 
                    ?>
                    <tr>
                        <td class="fw-semibold fs-6"><?= htmlspecialchars($c['name']) ?></td>
                        <td class="text-center">
                            <a href="kategori.php?hapus=<?= $c['id'] ?>" class="btn btn-sm text-danger bg-danger-subtle rounded-circle" style="width: 35px; height: 35px; line-height: 25px;" title="Hapus Kategori" onclick="return confirm('Hapus kategori ini?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="2" class="text-center text-muted py-4">Belum ada kategori yang ditambahkan.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>