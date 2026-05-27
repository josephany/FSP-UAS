<?php
session_start();
if (!isset($_SESSION['iduser']) || $_SESSION['isadmin'] != 1) {
    header("location: login.php");
    exit();
}

require_once("class/Mahasiswa.php");
require_once("class/Users.php");

if (!isset($_GET['nrp'])) {
    header("location: mahasiswa_tampilan.php");
    exit();
}

$nrp = $_GET['nrp'];

$pesan = '';
if (isset($_SESSION['insert_status'])) {
    if ($_SESSION['insert_status'] == 'namapanjang') {
        $pesan = '<div class="alert alert-error">Nama terlalu panjang (maksimal 45 karakter)!</div>';
    } elseif ($_SESSION['insert_status'] == 'fotopanjang') {
        $pesan = '<div class="alert alert-error">Ekstensi foto tidak boleh lebih dari 4 karakter!</div>';
    } elseif ($_SESSION['insert_status'] == 'angkatansalah') {
        $pesan = '<div class="alert alert-error">Angkatan harus berupa angka!</div>';
    } elseif ($_SESSION['insert_status'] == 'passpanjang') {
        $pesan = '<div class="alert alert-error">Password baru terlalu panjang (maksimal 100 karakter)!</div>';
    } elseif ($_SESSION['insert_status'] == 'angkatanlebihdari4') {
        $pesan = '<div class="alert alert-error">Angkatan harus kurang dari 5 karakter (maksimal 4 karakter)!</div>';
    }

    unset($_SESSION['insert_status']);
}

$mahasiswa = new Mahasiswa();
$users = new Users();

$pesan_password = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_pwd'])) {
    $username = $_POST['username_akun'];
    $new_pwd = $_POST['new_password'];
    $confirm_pwd = $_POST['confirm_password'];

    $len_pass = 0;
    while (isset($new_pwd[$len_pass])) $len_pass++;

    if ($new_pwd != $confirm_pwd) {
        $pesan_password = '<div class="alert alert-error">Gagal ganti password. Pastikan kolom ulangi password sama dengan kolom password baru!</div>';
    } elseif ($len_pass > 100) {
        $pesan_password = '<div class="alert alert-error">Password baru terlalu panjang (maksimal 100 karakter)!</div>';
    } elseif ($len_pass == 0) {
        $pesan_password = '<div class="alert alert-error">Password baru tidak boleh kosong!</div>';
    } else {
        if ($users->changePassword($username, $new_pwd)) {
            $pesan_password = '<div class="alert alert-success">Password berhasil diubah oleh Admin.</div>';
        } else {
            $pesan_password = '<div class="alert alert-error">Gagal mengubah password. Password baru tidak boleh sama dengan password lama!</div>';
        }
    }
}

$result = $mahasiswa->getMahasiswaByNrp($nrp);
$dataMhs = $result->fetch_assoc();

if (!$dataMhs) {
    header("location: mahasiswa_tampilan.php");
    exit();
}

$checkedPria = '';
$checkedWanita = '';
if ($dataMhs['gender'] == 'Pria') {
    $checkedPria = 'checked';
} else {
    $checkedWanita = 'checked';
}

$tampilanUsername = '';
if (isset($dataMhs['username']) && $dataMhs['username']) {
    $tampilanUsername = $dataMhs['username'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mahasiswa</title>

    <link rel="stylesheet" href="css/mahasiswa_edit_style.css">
</head>

<body>

    <div class="container">
        <h1>Form Edit Mahasiswa</h1>

        <a href="mahasiswa_tampilan.php" class="btn btn-secondary">&laquo; Kembali ke Daftar Mahasiswa</a>

        <?php echo $pesan; ?>

        <h2 class="section-title first-title">1. Edit Data Profil Mahasiswa</h2>

        <form action="mahasiswaedit_process.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="nrp" value="<?php echo $dataMhs['nrp'] ?>">

            <div class="form-group">
                <label>NRP</label>
                <input type="text" value="<?php echo $dataMhs['nrp'] ?>" disabled>
            </div>

            <div class="form-group">
                <label>Username Akun</label>
                <input type="text" value="<?php echo $tampilanUsername ?>" disabled>
            </div>

            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="nama" value="<?php echo ($dataMhs['nama']) ?>" required>
            </div>

            <div class="form-group">
                <label>Gender</label>
                <div class="radio-group">
                    <label><input type="radio" name="gender" value="Pria" <?php echo $checkedPria ?> required> Pria</label>
                    <label><input type="radio" name="gender" value="Wanita" <?php echo $checkedWanita ?>> Wanita</label>
                </div>
            </div>

            <div class="form-group">
                <label>Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" value="<?php echo $dataMhs['tanggal_lahir'] ?>" required>
            </div>

            <div class="form-group">
                <label>Angkatan</label>
                <input type="text" name="angkatan" value="<?php echo $dataMhs['angkatan'] ?>" required>
            </div>

            <div class="form-group">
                <label>Foto Saat Ini</label>
                <?php
                if ($dataMhs['foto_extention']) {
                    $foto_path = 'image/' . $dataMhs['nrp'] . '.' . $dataMhs['foto_extention'];
                    echo '<img src="' . $foto_path . '" class="current-photo">';
                } else {
                    echo '<p style="color:var(--text-muted); font-style:italic;">Tidak ada foto</p>';
                }
                ?>
            </div>

            <div class="form-group">
                <label>Ganti Foto (kosongkan jika tidak ingin ganti)</label>
                <input type="file" name="foto" accept="image/*">
            </div>

            <div class="form-group">
                <input type="submit" name="update_data" value="Update Data" class="btn btn-primary">
            </div>
        </form>

        <h2 class="section-title">2. Reset Password Mahasiswa</h2>

        <?php echo $pesan_password; ?>

        <?php if ($tampilanUsername) { ?>
            <form action="mahasiswaedit.php?nrp=<?php echo $nrp; ?>" method="post">
                <input type="hidden" name="nrp" value="<?php echo $dataMhs['nrp'] ?>">
                <input type="hidden" name="username_akun" value="<?php echo $dataMhs['username'] ?>">

                <p style="margin-bottom: 15px; color: var(--text-color);">
                    Mereset password untuk username: <strong><?php echo $tampilanUsername ?></strong>
                </p>

                <div class="form-group">
                    <label>Password Baru</label>
                    <input type="password" name="new_password" required>
                </div>

                <div class="form-group">
                    <label>Ulangi Password Baru</label>
                    <input type="password" name="confirm_password" required>
                </div>

                <div class="form-group">
                    <button type="submit" name="reset_pwd" value="reset_pwd" class="btn btn-primary">Reset Password</button>
                </div>
            </form>
        <?php } else { ?>
            <p style="color: var(--text-muted); font-style: italic;">Mahasiswa ini tidak memiliki akun, tidak bisa reset password.</p>
        <?php } ?>

    </div>
</body>

</html>