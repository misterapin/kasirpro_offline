<?php
include 'config/koneksi.php';

$invoice = $_GET['inv'] ?? '';

// Ambil data transaksi utama
$query = mysqli_query($conn, "SELECT * FROM transactions WHERE invoice_no = '$invoice'");
$trx = mysqli_fetch_assoc($query);

// Ambil pengaturan toko
$toko = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM settings WHERE id = 1"));
$print_mode = $toko['thermal_print_mode'] ?? 'auto';
$onload_script = ($print_mode == 'auto') ? 'window.print()' : '';

// Ambil detail barang jika tabel transaction_details ada
$q_detail = false;
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'transaction_details'");
if (mysqli_num_rows($check_table) > 0) {
    $q_detail = mysqli_query($conn, "SELECT td.*, p.name FROM transaction_details td JOIN products p ON td.product_id = p.id WHERE td.invoice_no = '$invoice'");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk - <?= htmlspecialchars($invoice) ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; font-size: 11px; width: 58mm; margin: 0; padding: 5px; background: #fff; color: #000; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .line { border-bottom: 1px dashed #000; margin: 5px 0; }
        .flex { display: flex; justify-content: space-between; }
        table { width: 100%; font-size: 10px; border-collapse: collapse; }
        th, td { padding: 2px 0; }
        @media print {
            body { width: 58mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="<?= $onload_script ?>">
    <div class="text-center">
        <h3 style="margin: 0; font-size: 13px;"><?= htmlspecialchars($toko['store_name'] ?? 'Toko Kasir') ?></h3>
        <p style="margin: 2px 0; font-size: 9px;"><?= htmlspecialchars($toko['address'] ?? '-') ?></p>
        <?php if(!empty($toko['phone'])): ?>
            <p style="margin: 2px 0; font-size: 9px;">Telp: <?= htmlspecialchars($toko['phone']) ?></p>
        <?php endif; ?>
    </div>
    
    <div class="line"></div>
    <div>
        <div>No Inv : <?= htmlspecialchars($trx['invoice_no'] ?? '-') ?></div>
        <div>Tanggal: <?= htmlspecialchars($trx['date'] ?? '-') ?></div>
    </div>
    
    <div class="line"></div>
    <table>
        <?php 
        if ($q_detail && mysqli_num_rows($q_detail) > 0) {
            while($item = mysqli_fetch_assoc($q_detail)) {
                echo "<tr><td colspan='2' class='fw-bold'>".htmlspecialchars($item['name'])."</td></tr>";
                echo "<tr><td>{$item['quantity']} x Rp ".number_format($item['price'] ?? 0)."</td><td class='text-end'>Rp ".number_format($item['subtotal'])."</td></tr>";
            }
        } else {
            // Tampilan standar jika detail item tidak disimpan terpisah di database
            echo "<tr><td>Belanjaan Toko</td><td class='text-end'>Rp ".number_format($trx['total'] ?? 0)."</td></tr>";
        }
        ?>
    </table>

    <div class="line"></div>
    <div>
        <div class="flex"><span>Total     :</span> <span class="fw-bold">Rp <?= number_format($trx['total'] ?? 0) ?></span></div>
        <div class="flex"><span>Metode    :</span> <span><?= htmlspecialchars($trx['payment_method'] ?? 'Tunai') ?></span></div>
        <div class="flex"><span>Bayar     :</span> <span>Rp <?= number_format($trx['cash_paid'] ?? 0) ?></span></div>
        <div class="flex"><span>Kembalian :</span> <span>Rp <?= number_format($trx['cash_change'] ?? 0) ?></span></div>
    </div>

    <div class="line"></div>
    <div class="text-center" style="font-size: 10px; margin-top: 5px;">
        <p style="margin: 0;"><?= htmlspecialchars($toko['receipt_footer'] ?? 'Terima Kasih Atas Kunjungan Anda') ?></p>
    </div>

    <!-- Tombol navigasi / cetak ulang (tidak ikut tercetak) -->
    <div class="text-center no-print" style="margin-top: 15px;">
        <button onclick="window.print()" style="padding: 4px 8px; cursor: pointer;">Cetak Ulang</button>
        <br><br>
        <a href="kasir.php" style="text-decoration: none; color: blue;">Kembali ke Kasir</a>
    </div>
</body>
</html>