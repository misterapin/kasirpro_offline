<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';

if (isset($_POST['verifikasi'])) {
    $owner_username = mysqli_real_escape_string($conn, $_POST['username']);
    $owner_password = md5($_POST['password']);

    // Cek apakah akun tersebut adalah owner
    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username = '$owner_username' AND password = '$owner_password' AND role = 'owner'");
    
    if (mysqli_num_rows($cek) > 0) {
        // Berikan izin akses sementara lewat session
        $_SESSION['izin_close_shift'] = true;
        header("Location: close_shift.php");
        exit;
    } else {
        $error = "Otorisasi ditolak! Username atau Password Owner salah.";
    }
}

$page_title = "Otorisasi Close Shift - POS Kasir";
include 'includes/header.php';
?>

<div class="container py-5" style="max-width: 450px;">
    <div class="bg-white p-4 rounded-4 shadow-sm text-center">
        <div class="bg-danger bg-opacity-15 d-inline-flex p-3 rounded-circle mb-3 text-danger" style="width: 70px; height: 70px; align-items: center; justify-content: center;">
            <i class="fas fa-user-shield fa-2x"></i>
        </div>
        <h4 class="fw-bold mb-1">Izin Close Shift</h4>
        <p class="text-muted small mb-4">Masukkan kredensial Owner untuk membuka halaman Close Shift.</p>

        <?php if($error): ?>
            <div class="alert alert-danger rounded-4 py-2 small mb-3"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3 text-start">
                <label class="form-label small fw-bold">Username Owner</label>
                <input type="text" name="username" class="form-control rounded-pill" placeholder="Username owner" required autofocus>
            </div>
            <div class="mb-4 text-start">
                <label class="form-label small fw-bold">Password Owner</label>
                <input type="password" name="password" class="form-control rounded-pill" placeholder="••••••••" required>
            </div>
            <button type="submit" name="verifikasi" class="btn btn-primary w-100 rounded-pill fw-bold py-2 mb-2 shadow-sm">
                <i class="fas fa-key me-1"></i> Berikan Izin Akses
            </button>
        </form>

        <a href="index.php" class="btn btn-outline-secondary w-100 rounded-pill py-2 small mt-2">Batal / Kembali</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>