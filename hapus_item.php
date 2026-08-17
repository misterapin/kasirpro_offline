<?php
session_start();

// Mengambil index barang yang ingin dihapus dari URL
if (isset($_GET['index'])) {
    $index = $_GET['index'];
    // Hapus item dari array session keranjang
    unset($_SESSION['keranjang'][$index]);
    // Rapikan kembali urutan index array
    $_SESSION['keranjang'] = array_values($_SESSION['keranjang']);
}

header("Location: kasir.php");
exit;
?>