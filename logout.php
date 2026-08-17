<?php
session_start();
include 'config/koneksi.php';

// Tutup shift aktif
mysqli_query($conn, "UPDATE shifts SET status = 'closed', end_time = NOW() WHERE user_id = " . $_SESSION['user_id'] . " AND status = 'active'");

session_destroy();
header("Location: login.php");
?>