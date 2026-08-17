<?php
session_start();
include 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = intval($_POST['product_id']);
    $qty_tambah = intval($_POST['qty'] ?? 1);

    // Ambil data produk dari database untuk memastikan stok dan harga valid
    $query = mysqli_query($conn, "SELECT * FROM products WHERE id = $product_id");
    $product = mysqli_fetch_assoc($query);

    if ($product) {
        $nama = $product['name'];
        $harga = $product['price'];
        $stok_db = $product['stock'];

        // Inisialisasi keranjang jika belum ada
        if (!isset($_SESSION['keranjang'])) {
            $_SESSION['keranjang'] = [];
        }

        // Cek apakah produk sudah ada di dalam keranjang
        $found = false;
        foreach ($_SESSION['keranjang'] as $index => $item) {
            if ($item['product_id'] == $product_id) {
                // Jika sudah ada, tambahkan qty-nya
                $qty_baru = $item['qty'] + $qty_tambah;

                // Validasi agar total qty di keranjang tidak melebihi stok database
                if ($qty_baru > $stok_db) {
                    $qty_baru = $stok_db; // Batasi maksimal sejumlah stok
                }

                $_SESSION['keranjang'][$index]['qty'] = $qty_baru;
                $_SESSION['keranjang'][$index]['subtotal'] = $qty_baru * $harga;
                $found = true;
                break;
            }
        }

        // Jika produk belum ada di keranjang, masukkan sebagai item baru
        if (!$found) {
            if ($qty_tambah > $stok_db) {
                $qty_tambah = $stok_db;
            }

            if ($qty_tambah > 0) {
                $_SESSION['keranjang'][] = [
                    'product_id' => $product_id,
                    'nama'       => $nama,
                    'harga'      => $harga,
                    'qty'        => $qty_tambah,
                    'subtotal'   => $qty_tambah * $harga
                ];
            }
        }
    }
}

// Kembali ke halaman kasir setelah produk dimasukkan
header("Location: kasir.php");
exit;
?>