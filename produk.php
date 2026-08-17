<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') { header("Location: index.php"); exit; }
include 'config/koneksi.php';

if (isset($_POST['tambah'])) {
    $barcode = mysqli_real_escape_string($conn, $_POST['barcode']);
    $name    = mysqli_real_escape_string($conn, $_POST['name']);
    $cat_id  = intval($_POST['category_id']);
    $price   = intval($_POST['price']);
    $stock   = intval($_POST['stock']);

    mysqli_query($conn, "INSERT INTO products (barcode, name, category_id, price, stock) VALUES ('$barcode', '$name', '$cat_id', '$price', '$stock')");
    header("Location: produk.php"); exit;
}

if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM products WHERE id = $id");
    header("Location: produk.php"); exit;
}

$page_title = "Kelola Produk - POS Kasir";
include 'includes/header.php';
?>

<div class="container py-4" style="max-width: 1100px;">
    <div class="bg-white p-4 rounded-4 shadow-sm">
        <!-- Header dengan Tombol Kembali ke Dashboard -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="fas fa-box text-success me-2"></i>Kelola Produk</h4>
            <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="card border-0 bg-light p-4 rounded-4 mb-4">
            <h6 class="fw-bold mb-3">Tambah Produk Baru</h6>
            <form action="" method="POST" class="row g-3">
                <div class="col-md-2"><input type="text" name="barcode" class="form-control rounded-pill" placeholder="Barcode" autofocus></div>
                <div class="col-md-3"><input type="text" name="name" class="form-control rounded-pill" placeholder="Nama Barang" required></div>
                <div class="col-md-2">
                    <select name="category_id" class="form-select rounded-pill">
                        <option value="">Pilih Kategori</option>
                        <?php $cats = mysqli_query($conn, "SELECT * FROM categories");
                        while($c = mysqli_fetch_assoc($cats)): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2"><input type="number" name="price" class="form-control rounded-pill" placeholder="Harga" required></div>
                <div class="col-md-1"><input type="number" name="stock" class="form-control rounded-pill" placeholder="Stok" required></div>
                <div class="col-md-2"><button type="submit" name="tambah" class="btn btn-success rounded-pill w-100 fw-bold">Simpan</button></div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Barcode</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th style="width: 140px; text-align: center;">Stok</th>
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $res = mysqli_query($conn, "SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.name ASC"); 
                    while($row = mysqli_fetch_assoc($res)): 
                    ?>
                    <tr>
                        <td><code><?= htmlspecialchars($row['barcode'] ?? '-') ?></code></td>
                        <td class="fw-semibold"><?= htmlspecialchars($row['name']) ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($row['cat_name'] ?? 'Tanpa Kategori') ?></span></td>
                        <td>Rp <?= number_format($row['price']) ?></td>
                        <td style="width: 140px; text-align: center;">
                            <span class="badge bg-light text-dark border px-3 py-2"><?= $row['stock'] ?></span>
                        </td>
                        <td class="text-center">
                            <a href="edit_produk.php?id=<?= $row['id'] ?>" class="btn btn-sm text-primary bg-primary-subtle rounded-circle me-1" style="width: 35px; height: 35px; line-height: 25px;" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="produk.php?hapus=<?= $row['id'] ?>" class="btn btn-sm text-danger bg-danger-subtle rounded-circle" style="width: 35px; height: 35px; line-height: 25px;" title="Hapus" onclick="return confirm('Hapus produk ini?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>