<?php
session_start();
include 'config/koneksi.php';

if (isset($_POST['barcode'])) {
    $barcode = mysqli_real_escape_string($conn, trim($_POST['barcode']));

    // Cari produk berdasarkan barcode
    $query = mysqli_query($conn, "SELECT * FROM products WHERE barcode = '$barcode'");
    $product = mysqli_fetch_assoc($query);

    if ($product) {
        $product_id = $product['id'];
        $nama = $product['name'];
        $harga = $product['price'];
        $stok_db = $product['stock'];
        $qty_tambah = 1;

        if (!isset($_SESSION['keranjang'])) {
            $_SESSION['keranjang'] = [];
        }

        $found = false;
        foreach ($_SESSION['keranjang'] as $index => $item) {
            if ($item['product_id'] == $product_id) {
                $qty_baru = $item['qty'] + 1;
                if ($qty_baru <= $stok_db) {
                    $_SESSION['keranjang'][$index]['qty'] = $qty_baru;
                    $_SESSION['keranjang'][$index]['subtotal'] = $qty_baru * $harga;
                }
                $found = true;
                break;
            }
        }

        if (!$found) {
            if ($stok_db > 0) {
                $_SESSION['keranjang'][] = [
                    'product_id' => $product_id,
                    'nama'       => $nama,
                    'harga'      => $harga,
                    'qty'        => 1,
                    'subtotal'   => $harga
                ];
            }
        }
    }
}

header("Location: kasir.php");
exit;
?>