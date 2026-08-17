<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'POS Kasir' ?></title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f6f9; color: #333; }
    </style>
</head>
<body class="bg-light">

    <!-- Top Navbar Universal dengan Jam Real-Time -->
    <nav class="navbar navbar-dark bg-dark px-4 shadow-sm mb-4">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-cash-register text-primary me-2"></i>POS Kasir <span class="badge bg-primary fs-6 ms-2">Pro</span>
            </a>
            
            <!-- Elemen Jam Digital Real-Time -->
            <div class="text-light small d-none d-md-block">
                <i class="fas fa-clock text-warning me-1"></i> <span id="realtime-clock" class="fw-semibold"></span>
            </div>

            <div class="d-flex align-items-center">
                <span class="text-light me-3 small"><i class="fas fa-user-circle me-1"></i> <?= htmlspecialchars($_SESSION['fullname']) ?> (<span class="text-uppercase text-warning fw-bold"><?= $_SESSION['role'] ?></span>)</span>
                <a href="manajemen_user.php" class="btn btn-outline-light btn-sm rounded-pill px-3 me-2"><i class="fas fa-user-cog me-1"></i> Profil</a>
                <a href="verifikasi_shift.php" class="btn btn-warning btn-sm rounded-pill px-3 me-2" title="Tutup Shift"> <i class="fas fa-cash-register me-1"></i> Close Shift</a>
                <a href="logout.php" class="btn btn-danger btn-sm rounded-pill px-3"><i class="fas fa-sign-out-alt me-1"></i> Logout</a>
            </div>
        </div>
    </nav>

    <!-- Script JavaScript untuk Jam Berjalan -->
    <script>
        function updateClock() {
            const now = new Date();
            const options = { 
                weekday: 'short', 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric', 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit',
                hour12: false 
            };
            document.getElementById('realtime-clock').innerText = now.toLocaleDateString('id-ID', options);
        }
        setInterval(updateClock, 1000);
        updateClock(); // Panggil langsung agar tidak ada jeda 1 detik saat halaman dimuat
    </script>