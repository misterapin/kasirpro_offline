<?php
session_start();
include 'config/koneksi.php';

$invoice = $_POST['invoice'];
$total   = $_POST['total'];
$paid    = $_POST['paid'];
$method  = $_POST['method'];
$change  = $paid - $total;
$date    = date('Y-m-d H:i:s');

// 1. Simpan transaksi utama
$query_trx = "INSERT INTO transactions (invoice_no, total, payment_method, cash_paid, cash_change, date) 
              VALUES ('$invoice', '$total', '$method', '$paid', '$change', '$date')";

if (mysqli_query($conn, $query_trx)) {
    // 2. Simpan item keranjang jika ada tabel detail (opsional/bisa disesuaikan)
    if(isset($_SESSION['keranjang'])) {
        foreach($_SESSION['keranjang'] as $item) {
            $id_prod = $item['id'];
            $qty     = $item['qty'];
            $sub     = $item['subtotal'];

            // Kurangi stok produk
            mysqli_query($conn, "UPDATE products SET stock = stock - $qty WHERE id = '$id_prod'");
        }
    }

    // 3. Kosongkan keranjang belanja setelah sukses
    unset($_SESSION['keranjang']);

    // 4. Arahkan ke halaman cetak struk
    header("Location: cetak.php?inv=$invoice");
    exit;
} else {
    echo "Gagal menyimpan transaksi: " . mysqli_error($conn);
}
?>