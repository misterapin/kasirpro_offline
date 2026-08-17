<?php 
session_start();
include 'config/koneksi.php'; 

$keyword = isset($_GET['cari']) ? mysqli_real_escape_string($conn, $_GET['cari']) : '';
$where = $keyword ? "WHERE name LIKE '%$keyword%'" : "";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir - POS Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f6f9; color: #333; }
        .product-list-container { max-height: calc(100vh - 250px); overflow-y: auto; }
        .cart-container { background: #ffffff; border-radius: 12px; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); display: flex; flex-direction: column; height: calc(100vh - 120px); }
        .cart-items { flex-grow: 1; overflow-y: auto; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="d-flex flex-column vh-100 overflow-hidden">

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold fs-5" href="index.php">
                <i class="fas fa-cash-register text-primary me-2"></i>POS Kasir <span class="badge bg-primary fs-6 ms-2">Pro</span>
            </a>
            <div class="d-flex align-items-center">
                <span class="text-light me-3 small"><i class="fas fa-user-circle me-1"></i> <?= htmlspecialchars($_SESSION['fullname'] ?? 'Kasir') ?></span>
                <a href="index.php" class="btn btn-outline-light btn-sm px-3 rounded-pill"><i class="fas fa-home me-1"></i> Dashboard</a>
            </div>
        </div>
    </nav>

    <!-- Main Content Layout -->
    <div class="container-fluid flex-grow-1 p-3 overflow-hidden">
        <div class="row h-100 g-3">
            
            <!-- KOLOM KIRI: Scan Barcode & Daftar Produk -->
            <div class="col-lg-7 d-flex flex-column h-100">
                
                <!-- Kotak Khusus Scan Barcode (Otomatis Fokus) -->
                <div class="card border-0 shadow-sm p-3 mb-2 rounded-4 bg-primary text-white">
                    <form action="scan_barcode.php" method="POST" class="input-group">
                        <span class="input-group-text bg-white border-0 ps-3 text-primary"><i class="fas fa-barcode fa-lg"></i></span>
                        <input type="text" name="barcode" class="form-control bg-white border-0 py-2 fs-6 fw-semibold" placeholder="Klik di sini lalu Scan Barcode produk..." autofocus autocomplete="off" required>
                        <button class="btn btn-dark px-4 fw-semibold" type="submit">Enter</button>
                    </form>
                </div>

                <!-- Bar Pencarian Manual -->
                <div class="card border-0 shadow-sm p-2 mb-3 rounded-4 bg-white">
                    <form method="GET" class="input-group">
                        <span class="input-group-text bg-light border-0 ps-3"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="cari" class="form-control bg-light border-0 py-1 small" placeholder="Atau cari nama produk manual..." value="<?= htmlspecialchars($keyword) ?>">
                        <?php if($keyword): ?>
                            <a href="kasir.php" class="btn btn-light border-0 d-flex align-items-center text-muted"><i class="fas fa-times"></i></a>
                        <?php endif; ?>
                        <button class="btn btn-outline-secondary btn-sm px-3" type="submit">Cari</button>
                    </form>
                </div>

                <!-- Tabel Baris Produk -->
                <div class="product-list-container flex-grow-1 bg-white rounded-4 shadow-sm p-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Produk</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $res = mysqli_query($conn, "SELECT * FROM products $where ORDER BY name ASC");
                                if(mysqli_num_rows($res) > 0):
                                    while($p = mysqli_fetch_assoc($res)):
                                ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($p['name']) ?></div>
                                        <small class="text-muted" style="font-size: 11px;">Code: <?= htmlspecialchars($p['barcode'] ?? '-') ?></small>
                                    </td>
                                    <td class="text-primary fw-bold">Rp <?= number_format($p['price']) ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1"><?= $p['stock'] ?></span>
                                    </td>
                                    <td class="text-end">
                                        <?php if($p['stock'] > 0): ?>
                                        <form action="tambah_keranjang.php" method="POST" class="d-inline-flex align-items-center gap-1">
                                            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                            <input type="hidden" name="qty" value="1">
                                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 py-1 fw-semibold">
                                                <i class="fas fa-plus me-1"></i> Tambah
                                            </button>
                                        </form>
                                        <?php else: ?>
                                        <span class="badge bg-secondary">Habis</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Produk tidak ditemukan.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: Keranjang Belanja -->
            <div class="col-lg-5 h-100">
                <div class="cart-container p-3">
                    <div class="d-flex justify-content-between align-items-center pb-3 border-bottom mb-2">
                        <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-shopping-cart text-primary me-2"></i>Keranjang</h5>
                        <?php if(isset($_SESSION['keranjang']) && !empty($_SESSION['keranjang'])): ?>
                            <a href="hapus_item.php?kosongkan=semua" class="text-danger small text-decoration-none fw-semibold"><i class="fas fa-trash-alt me-1"></i> Kosongkan</a>
                        <?php endif; ?>
                    </div>

                    <!-- Daftar Item dalam Keranjang -->
                    <div class="cart-items pe-1">
                        <?php 
                        $total_semua = 0;
                        if(isset($_SESSION['keranjang']) && !empty($_SESSION['keranjang'])):
                            foreach($_SESSION['keranjang'] as $index => $item):
                                $total_semua += $item['subtotal'];
                        ?>
                        <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded-3 mb-2">
                            <div style="max-width: 60%;">
                                <h6 class="fw-bold mb-0 small text-truncate"><?= htmlspecialchars($item['nama']) ?></h6>
                                <small class="text-muted"><?= $item['qty'] ?> x Rp <?= number_format($item['harga']) ?></small>
                            </div>
                            <div class="text-end d-flex align-items-center gap-2">
                                <span class="fw-bold small text-primary">Rp <?= number_format($item['subtotal']) ?></span>
                                <a href="hapus_item.php?index=<?= $index ?>" class="text-danger bg-white px-2 py-1 rounded shadow-sm text-decoration-none small">&times;</a>
                            </div>
                        </div>
                        <?php 
                            endforeach;
                        else:
                        ?>
                        <div class="text-center text-muted py-5 mt-4">
                            <i class="fas fa-shopping-basket fa-3x mb-3 text-secondary opacity-25"></i>
                            <p class="small">Belum ada item dipilih.</p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Bagian Total & Tombol Pembayaran -->
                    <div class="pt-3 border-top mt-auto">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted fw-semibold">Total Tagihan</span>
                            <h4 class="fw-bold text-primary mb-0">Rp <?= number_format($total_semua) ?></h4>
                        </div>
                        
                        <?php if(isset($_SESSION['keranjang']) && !empty($_SESSION['keranjang'])): ?>
                            <a href="form_bayar.php" class="btn btn-success w-100 py-2 rounded-pill fw-bold shadow-sm">
                                <i class="fas fa-credit-card me-2"></i> Proses Pembayaran
                            </a>
                        <?php else: ?>
                            <button class="btn btn-secondary w-100 py-2 rounded-pill fw-bold shadow-sm" disabled>
                                Proses Pembayaran
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>