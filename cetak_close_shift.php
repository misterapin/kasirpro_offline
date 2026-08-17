<?php
session_start();
include 'config/koneksi.php';

$shift_id = intval($_GET['id'] ?? 0);
$omzet = intval($_GET['omzet'] ?? 0);
$jumlah = intval($_GET['jumlah'] ?? 0);

// Ambil data toko untuk header struk
$toko = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM settings WHERE id = 1"));
$nama_toko = $toko['store_name'] ?? 'POS Kasir';
$alamat_toko = $toko['address'] ?? '';

// Ambil info shift & kasir
$shift_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT s.*, u.fullname FROM shifts s JOIN users u ON s.user_id = u.id WHERE s.id = $shift_id"));
$nama_kasir = $shift_info['fullname'] ?? ($_SESSION['fullname'] ?? 'Kasir');
$start_time = $shift_info['start_time'] ?? '-';
$end_time = date('Y-m-d H:i:s');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Close Shift</title>
    <style>
        /* Format khusus printer thermal 58mm (lebar sekitar 48mm - 58mm) */
        body { 
            font-family: 'Courier New', Courier, monospace; 
            font-size: 12px; 
            width: 58mm; 
            margin: 0 auto; 
            padding: 5px; 
            color: #000;
        }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .d-flex { display: flex; justify-content: space-between; }
        hr { border: none; border-top: dashed 1px #000; margin: 8px 0; }
    </style>
</head>
<body onload="window.print();">
    <div class="text-center">
        <div class="fw-bold" style="font-size: 14px;"><?= htmlspecialchars($nama_toko) ?></div>
        <div><?= htmlspecialchars($alamat_toko) ?></div>
        <div style="font-size: 11px; margin-top: 5px;" class="fw-bold">LAPORAN CLOSE SHIFT</div>
    </div>
    <hr>
    <div>Kasir : <?= htmlspecialchars($nama_kasir) ?></div>
    <div>Mulai : <?= $start_time ?></div>
    <div>Tutup : <?= $end_time ?></div>
    <hr>
    <div class="d-flex">
        <span>Jml Transaksi:</span>
        <span class="fw-bold"><?= $jumlah ?> Struk</span>
    </div>
    <div class="d-flex" style="font-size: 14px; margin-top: 5px;">
        <span class="fw-bold">TOTAL OMZET:</span>
        <span class="fw-bold">Rp <?= number_format($omzet, 0, ',', '.') ?></span>
    </div>
    <hr>
    <div class="text-center" style="margin-top: 15px;">
        <p>Tanda Tangan,</p>
        <br><br>
        <p>( <?= htmlspecialchars($nama_kasir) ?> )</p>
        <p style="font-size: 10px; margin-top: 10px;">*** Terima Kasih ***</p>
    </div>
</body>
</html>