<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') { header("Location: index.php"); exit; }
include 'config/koneksi.php';

$error = ''; $sukses = '';
if (isset($_POST['tambah'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $role     = $_POST['role'];

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Username sudah digunakan!";
    } else {
        if (mysqli_query($conn, "INSERT INTO users (username, password, fullname, role) VALUES ('$username', '$password', '$fullname', '$role')")) {
            $sukses = "Akun berhasil ditambahkan!";
        } else {
            $error = "Gagal: " . mysqli_error($conn);
        }
    }
}

if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    if ($id !== $_SESSION['user_id']) { mysqli_query($conn, "DELETE FROM users WHERE id = $id"); }
    header("Location: manajemen_user.php"); exit;
}

$page_title = "Manajemen Pengguna - POS Kasir";
include 'includes/header.php';
?>

<div class="container py-4" style="max-width: 900px;">
    <div class="bg-white p-4 rounded-4 shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="fas fa-users text-dark me-2"></i>Kelola Pengguna</h4>
            <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Kembali</a>
        </div>

        <?php if($error): ?><div class="alert alert-danger rounded-4 py-2 small"><?= $error ?></div><?php endif; ?>
        <?php if($sukses): ?><div class="alert alert-success rounded-4 py-2 small"><?= $sukses ?></div><?php endif; ?>

        <div class="card p-4 border-0 bg-light rounded-4 mb-4">
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
                    <select name="role" class="form-select rounded-pill">
                        <option value="kasir">Kasir</option>
                        <option value="owner">Owner</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="tambah" class="btn btn-dark rounded-pill w-100 fw-bold">Tambah</button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Hak Akses</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; $res = mysqli_query($conn, "SELECT * FROM users"); while($u = mysqli_fetch_assoc($res)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($u['fullname']) ?></td>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                        <td><span class="badge bg-<?= $u['role'] == 'owner' ? 'danger' : 'primary' ?> px-2 py-1"><?= strtoupper($u['role']) ?></span></td>
                        <td>
                            <?php if($u['id'] != $_SESSION['user_id']): ?>
                                <a href="manajemen_user.php?hapus=<?= $u['id'] ?>" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1" onclick="return confirm('Hapus akun ini?')">Hapus</a>
                            <?php else: ?>
                                <small class="text-muted">Akun Anda</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>