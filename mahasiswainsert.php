<?php
session_start();
if (!isset($_SESSION['iduser']) || $_SESSION['isadmin'] != 1) {
    header("location: login.php");
    exit();
}

$pesan = '';
if (isset($_SESSION['insert_status'])) {
    if ($_SESSION['insert_status'] == 'nrpada') {
        $pesan = "Maaf, NRP yang Anda masukkan sudah terdaftar. Silakan gunakan NRP yang lain.";
    } elseif ($_SESSION['insert_status'] == 'userada') {
        $pesan = "Maaf, Username yang Anda masukkan sudah terdaftar. Silakan gunakan Username yang lain.";
    } elseif ($_SESSION['insert_status'] == 'gagalinsert') {
        $pesan = "Gagal menyimpan data mahasiswa dan akun. Silakan coba lagi.";
    } elseif ($_SESSION['insert_status'] == 'nrpsalah') {
        $pesan = "NRP harus berupa angka!";
    } elseif ($_SESSION['insert_status'] == 'nrppanjang') {
        $pesan = "NRP tidak boleh lebih dari 9 digit!";
    } elseif ($_SESSION['insert_status'] == 'namapanjang') {
        $pesan = "Nama Anda terlalu panjang (maksimal 45 karakter)!";
    } elseif ($_SESSION['insert_status'] == 'userpanjang') {
        $pesan = "Username yang Anda masukkan terlalu panjang (maksimal 20 karakter)!";
    } elseif ($_SESSION['insert_status'] == 'passpanjang') {
        $pesan = "Password yang Anda masukkan terlalu panjang (maksimal 100 karakter)!";
    } elseif ($_SESSION['insert_status'] == 'fotopanjang') {
        $pesan = "Ekstensi foto tidak boleh lebih dari 4 karakter!";
    } elseif ($_SESSION['insert_status'] == 'angkatanpanjang') {
        $pesan = "Angkatan tidak boleh lebih dari 4 karakter!";
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
    <title>Tambah Mahasiswa</title>

    <link rel="stylesheet" href="css/mahasiswa_insert_style.css">
</head>

<body>

    <div class="container">
        <h1>Form Tambah Mahasiswa</h1>

        <a href="mahasiswa_tampilan.php" class="btn btn-secondary">&laquo; Kembali ke Daftar Mahasiswa</a>

        <?php echo $pesan; ?>

        <form action="mahasiswainsert_process.php" method="post" enctype="multipart/form-data">

            <h2 class="section-title first-title">Detail Mahasiswa</h2>

            <div class="form-group">
                <label>NRP</label>
                <input type="text" name="nrp" placeholder="Contoh: 160420000" required>
            </div>

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" placeholder="Nama mahasiswa" required>
            </div>

            <div class="form-group">
                <label>Gender</label>
                <div class="radio-group">
                    <label><input type="radio" name="gender" value="Pria" required> Pria</label>
                    <label><input type="radio" name="gender" value="Wanita"> Wanita</label>
                </div>
            </div>

            <div class="form-group">
                <label>Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" required>
            </div>

            <div class="form-group">
                <label>Angkatan</label>
                <input type="number" name="angkatan" placeholder="Contoh: 2023" required>
            </div>

            <h2 class="section-title">Detail Akun</h2>

            <div class="form-group">
                <label>Username Akun</label>
                <input type="text" name="username" required>
            </div>

            <div class="form-group">
                <label>Password Akun</label>
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