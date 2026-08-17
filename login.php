<?php
session_start();
include 'config/koneksi.php';

// Ambil logo pada query login.php
$q_toko = mysqli_query($conn, "SELECT store_name, address, logo FROM settings WHERE id = 1");
$toko = mysqli_fetch_assoc($q_toko);
$nama_toko = $toko['store_name'] ?? 'POS Kasir Pro';
$alamat_toko = $toko['address'] ?? 'Sistem Kasir Modern';
$logo_toko = $toko['logo'] ?? '';
$error = '';
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' AND password = '$password'");
    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['role'] = $user['role'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= htmlspecialchars($nama_toko) ?></title>
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f6f9; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { border-radius: 24px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 420px; }
        .btn-login { border-radius: 50px; font-weight: 600; padding: 12px; }
        .form-control { border-radius: 12px; padding: 12px; background-color: #f8fafc; border: 1px solid #e2e8f0; }
        .form-control:focus { box-shadow: none; border-color: #0d6efd; }
    </style>
</head>
<body>

    <div class="card login-card p-4 p-md-5">
<div class="text-center mb-4">
    <?php if(!empty($logo_toko) && file_exists('uploads/' . $logo_toko)): ?>
        <img src="uploads/<?= $logo_toko ?>" alt="Logo" class="rounded-circle border shadow-sm mb-3" style="width: 75px; height: 75px; object-fit: cover;">
    <?php else: ?>
        <div class="bg-primary bg-opacity-10 d-inline-block p-3 rounded-circle mb-3 text-primary" style="width: 75px; height: 75px; display: inline-flex; align-items: center; justify-content: center;">
            <i class="fas fa-store fa-2x"></i>
        </div>
    <?php endif; ?>
    <h4 class="fw-bold text-dark mb-1"><?= htmlspecialchars($nama_toko) ?></h4>
    <p class="text-muted small mb-0"><i class="fas fa-map-marker-alt text-danger me-1"></i> <?= htmlspecialchars($alamat_toko) ?></p>
</div>
        <?php if($error): ?>
            <div class="alert alert-danger rounded-4 py-2 small text-center"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold text-muted">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100 btn-login shadow-sm">Masuk Sistem</button>
        </form>
        
        <div class="text-center mt-4">
            <small class="text-muted">© 2026 POS Kasir Pro</small>
        </div>
    </div>

    <!-- FontAwesome untuk Ikon -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>