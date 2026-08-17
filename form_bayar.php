<?php
session_start();
$total_semua = 0;
if(isset($_SESSION['keranjang'])) {
    foreach($_SESSION['keranjang'] as $item) {
        $total_semua += $item['subtotal'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4" style="max-width: 500px; margin: auto;">
    <h3>Konfirmasi Pembayaran</h3>
    <form action="proses.php" method="POST">
        <input type="hidden" name="invoice" value="INV-<?= date('YmdHis') ?>">
        <input type="hidden" name="total" value="<?= $total_semua ?>">

        <div class="mb-3">
            <label class="form-label">Total Tagihan</label>
            <input type="text" class="form-control" value="Rp <?= number_format($total_semua) ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Metode Pembayaran</label>
            <select name="method" class="form-select">
                <option value="Tunai">Tunai</option>
                <option value="QRIS">QRIS</option>
                <option value="Transfer">Transfer</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Uang Tunai Diserahkan (Rp)</label>
            <input type="number" name="paid" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success w-100">Proses & Cetak Struk</button>
        <a href="kasir.php" class="btn btn-secondary w-100 mt-2">Kembali ke Kasir</a>
    </form>
</body>
</html>