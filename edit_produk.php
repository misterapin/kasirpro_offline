<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') { header("Location: index.php"); exit; }
include 'config/koneksi.php';

$id = intval($_GET['id'] ?? 0);

// Proses Simpan Perubahan
if (isset($_POST['update'])) {
    $barcode     = mysqli_real_escape_string($conn, $_POST['barcode']);
    $name        = mysqli_real_escape_string($conn, $_POST['name']);
    $category_id = intval($_POST['category_id']);
    $price       = intval($_POST['price']);
    $stock       = intval($_POST['stock']);

    mysqli_query($conn, "UPDATE products SET barcode='$barcode', name='$name', category_id='$category_id', price='$price', stock='$stock' WHERE id=$id");
    header("Location: produk.php"); 
    exit;
}

// Ambil data produk berdasarkan ID
$p = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$id"));
if (!$p) { 
    header("Location: produk.php"); 
    exit; 
}

$page_title = "Edit Produk - POS Kasir";
include 'includes/header.php';
?>

<div class="container py-4" style="max-width: 600px;">
    <div class="bg-white p-4 rounded-4 shadow-sm">
        <h4 class="fw-bold mb-4"><i class="fas fa-edit text-primary me-2"></i>Edit Produk</h4>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">Barcode</label>
                <input type="text" name="barcode" class="form-control rounded-pill" value="<?= htmlspecialchars($p['barcode'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Nama Barang</label>
                <input type="text" name="name" class="form-control rounded-pill" value="<?= htmlspecialchars($p['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Kategori</label>
                <select name="category_id" class="form-select rounded-pill">
                    <option value="">Pilih Kategori</option>
                    <?php 
                    $cats = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");
                    while($c = mysqli_fetch_assoc($cats)): 
                    ?>
                        <option value="<?= $c['id'] ?>" <?= ($p['category_id'] == $c['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold">Harga (Rp)</label>
                    <input type="number" name="price" class="form-control rounded-pill" value="<?= $p['price'] ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold">Stok</label>
                    <input type="number" name="stock" class="form-control rounded-pill" value="<?= $p['stock'] ?>" required>
                </div>
            </div>
            <button type="submit" name="update" class="btn btn-primary rounded-pill w-100 fw-bold py-2 mb-2">Simpan Perubahan</button>
            <a href="produk.php" class="btn btn-outline-secondary rounded-pill w-100 py-2">Batal</a>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>