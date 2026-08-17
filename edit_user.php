<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') { 
    header("Location: index.php"); 
    exit; 
}
include 'config/koneksi.php';

$id = intval($_GET['id'] ?? 0);
$error = ''; 

// Proses Update Data Pengguna
if (isset($_POST['update'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $role     = mysqli_real_escape_string($conn, $_POST['role']);
    
    // Jika password diisi, update password juga. Jika kosong, biarkan password lama.
    if (!empty($_POST['password'])) {
        $password = md5($_POST['password']);
        $query = "UPDATE users SET fullname='$fullname', username='$username', password='$password', role='$role' WHERE id=$id";
    } else {
        $query = "UPDATE users SET fullname='$fullname', username='$username', role='$role' WHERE id=$id";
    }

    if (mysqli_query($conn, $query)) {
        header("Location: manajemen_user.php");
        exit;
    } else {
        $error = "Gagal memperbarui pengguna.";
    }
}

// Ambil data pengguna berdasarkan ID
$result = mysqli_query($conn, "SELECT * FROM users WHERE id=$id");
if (!$result || mysqli_num_rows($result) === 0) {
    header("Location: manajemen_user.php");
    exit;
}
$user = mysqli_fetch_assoc($result);

$page_title = "Edit Pengguna - POS Kasir";
include 'includes/header.php';
?>

<div class="container py-4" style="max-width: 600px;">
    <div class="bg-white p-4 rounded-4 shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="fas fa-user-edit text-primary me-2"></i>Edit Pengguna</h4>
            <a href="manajemen_user.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Kembali</a>
        </div>

        <?php if($error): ?>
            <div class="alert alert-danger rounded-4 py-2 small mb-3"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">Nama Lengkap</label>
                <input type="text" name="fullname" class="form-control rounded-pill" value="<?= htmlspecialchars($user['fullname']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Username</label>
                <input type="text" name="username" class="form-control rounded-pill" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Password Baru <span class="text-muted fw-normal">(Kosongkan jika tidak ingin mengubah)</span></label>
                <input type="password" name="password" class="form-control rounded-pill" placeholder="••••••••">
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold">Role / Hak Akses</label>
                <select name="role" class="form-select rounded-pill" required>
                    <option value="owner" <?= $user['role'] == 'owner' ? 'selected' : '' ?>>Owner</option>
                    <option value="kasir" <?= $user['role'] == 'kasir' ? 'selected' : '' ?>>Kasir</option>
                </select>
            </div>
            <button type="submit" name="update" class="btn btn-primary rounded-pill w-100 fw-bold py-2 shadow-sm">Simpan Perubahan</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>