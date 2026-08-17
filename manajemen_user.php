<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') { header("Location: index.php"); exit; }
include 'config/koneksi.php';

$error = ''; $success = '';

// Proses Tambah Pengguna Baru
if (isset($_POST['tambah'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);
    $role     = mysqli_real_escape_string($conn, $_POST['role']);

    // Cek apakah username sudah ada
    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Username sudah digunakan!";
    } else {
        $query = "INSERT INTO users (fullname, username, password, role) VALUES ('$fullname', '$username', '$password', '$role')";
        if (mysqli_query($conn, $query)) {
            $success = "Pengguna baru berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan pengguna.";
        }
    }
}

// Proses Hapus Pengguna
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    // Jangan biarkan owner menghapus akunnya sendiri yang sedang aktif
    if ($id == $_SESSION['user_id']) {
        $error = "Anda tidak dapat menghapus akun yang sedang aktif digunakan!";
    } else {
        mysqli_query($conn, "DELETE FROM users WHERE id = $id");
        header("Location: manajemen_user.php");
        exit;
    }
}

$page_title = "Manajemen Pengguna - POS Kasir";
include 'includes/header.php';
?>

<div class="container py-4" style="max-width: 1000px;">
    <div class="bg-white p-4 rounded-4 shadow-sm">
        <!-- Header dengan Tombol Kembali ke Dashboard -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="fas fa-users text-primary me-2"></i>Manajemen Pengguna (Kasir & Owner)</h4>
            <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <?php if($error): ?><div class="alert alert-danger rounded-4 py-2 small mb-3"><?= $error ?></div><?php endif; ?>
        <?php if($success): ?><div class="alert alert-success rounded-4 py-2 small mb-3"><?= $success ?></div><?php endif; ?>

        <!-- Form Tambah Pengguna Baru -->
        <div class="card border-0 bg-light p-4 rounded-4 mb-4">
            <h6 class="fw-bold mb-3">Tambah Pengguna Baru</h6>
            <form method="POST" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="fullname" class="form-control rounded-pill" placeholder="Nama Lengkap" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="username" class="form-control rounded-pill" placeholder="Username" required>
                </div>
                <div class="col-md-2">
                    <input type="password" name="password" class="form-control rounded-pill" placeholder="Password" required>
                </div>
                <div class="col-md-2">
                    <select name="role" class="form-select rounded-pill" required>
                        <option value="kasir">Kasir</option>
                        <option value="owner">Owner</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="tambah" class="btn btn-primary rounded-pill w-100 fw-bold">Simpan</button>
                </div>
            </form>
        </div>

        <!-- Tabel Daftar Pengguna -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $res = mysqli_query($conn, "SELECT * FROM users ORDER BY fullname ASC");
                    while($row = mysqli_fetch_assoc($res)): 
                    ?>
                    <tr>
                        <td class="fw-semibold"><?= htmlspecialchars($row['fullname']) ?></td>
                        <td><code><?= htmlspecialchars($row['username']) ?></code></td>
                        <td>
                            <span class="badge bg-<?= $row['role'] == 'owner' ? 'success' : 'secondary' ?> text-uppercase">
                                <?= $row['role'] ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <!-- Tombol Edit -->
                            <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-sm text-primary bg-primary-subtle rounded-circle me-1" style="width: 35px; height: 35px; line-height: 25px;" title="Edit Pengguna">
                                <i class="fas fa-edit"></i>
                            </a>
                            <!-- Tombol Hapus -->
                            <a href="manajemen_user.php?hapus=<?= $row['id'] ?>" class="btn btn-sm text-danger bg-danger-subtle rounded-circle" style="width: 35px; height: 35px; line-height: 25px;" title="Hapus Pengguna" onclick="return confirm('Hapus pengguna ini?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>