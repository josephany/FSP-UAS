<?php
session_start();
if (!isset($_SESSION['iduser']) || $_SESSION['isadmin'] != 1) {
    header("location: login.php");
    exit();
}

$pesan = '';
if (isset($_SESSION['insert_status'])) {
    if ($_SESSION['insert_status'] == 'npkada') {
        $pesan = "Maaf, NPK yang Anda masukkan sudah terdaftar.";
    } elseif ($_SESSION['insert_status'] == 'userada') {
        $pesan = "Maaf, Username sudah digunakan.";
    } elseif ($_SESSION['insert_status'] == 'gagalinsert') {
        $pesan = "Gagal menyimpan data dosen.";
    } elseif ($_SESSION['insert_status'] == 'npksalah') {
        $pesan = "NPK harus berupa angka!";
    } elseif ($_SESSION['insert_status'] == 'npkpanjang') {
        $pesan = "NPK tidak boleh lebih dari 6 digit!";
    } elseif ($_SESSION['insert_status'] == 'namapanjang') {
        $pesan = "Nama terlalu panjang (maksimal 45 karakter)!";
    } elseif ($_SESSION['insert_status'] == 'userpanjang') {
        $pesan = "Username terlalu panjang (maksimal 20 karakter)!";
    } elseif ($_SESSION['insert_status'] == 'passpanjang') {
        $pesan = "Password terlalu panjang (maksimal 100 karakter)!";
    } elseif ($_SESSION['insert_status'] == 'fotopanjang') {
        $pesan = "Ekstensi foto tidak boleh lebih dari 4 karakter!";
    }

    if ($pesan) {
        $pesan = '<div class="alert alert-error">' . $pesan . '</div>';
    }

    unset($_SESSION['insert_status']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Dosen</title>

    <link rel="stylesheet" href="css/dosen_insert_style.css">
</head>

<body>

    <div class="container">
        <h1>Form Tambah Dosen</h1>

        <a href="dosen_tampilan.php" class="btn btn-secondary">&laquo; Kembali ke Daftar Dosen</a>

        <?php echo $pesan; ?>

        <form action="doseninsert_process.php" method="post" enctype="multipart/form-data">

            <h2 class="section-title first-title">1. Detail Dosen</h2>

            <div class="form-group">
                <label>NPK</label>
                <input type="text" name="npk" placeholder="Contoh: 123456" required>
            </div>

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" placeholder="Masukkan nama dosen" required>
            </div>

            <h2 class="section-title">2. Detail Akun</h2>

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Foto Profil</label>
                <input type="file" name="foto" accept="image/*" required>
            </div>

            <div class="form-group">
                <input type="submit" value="Simpan Data" class="btn btn-primary">
            </div>

        </form>
    </div>

</body>

</html>