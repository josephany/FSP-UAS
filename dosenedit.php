<?php
session_start();

if (!isset($_SESSION['iduser']) || $_SESSION['isadmin'] != 1) {
    header("location: login.php");
    exit();
}

require_once("class/Dosen.php");
require_once("class/Users.php");

if (!isset($_GET['npk'])) {
    header("location: dosen_tampilan.php");
    exit();
}

$pesan = '';
if (isset($_SESSION['insert_status'])) {
    if ($_SESSION['insert_status'] == 'namapanjang') {
        $pesan = '<div class="alert alert-error">Nama terlalu panjang (maksimal 45 karakter)!</div>';
    } elseif ($_SESSION['insert_status'] == 'fotopanjang') {
        $pesan = '<div class="alert alert-error">Ekstensi foto tidak boleh lebih dari 4 karakter!</div>';
    }
    unset($_SESSION['insert_status']);
}

$dosen = new Dosen();
$users = new Users();

$npk = $_GET['npk'];

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

$result = $dosen->getDosenByNpk($npk);
$dataDosen = $result->fetch_assoc();

if (!$dataDosen) {
    header("location: dosen_tampilan.php");
    exit();
}

$tampilannUsername = '';
if (isset($dataDosen['username']) && $dataDosen['username']) {
    $tampilannUsername = $dataDosen['username'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Dosen</title>

    <link rel="stylesheet" href="css/dosen_edit_style.css">
</head>

<body>

    <div class="container">
        <h1>Form Edit Dosen</h1>

        <a href="dosen_tampilan.php" class="btn btn-secondary">&laquo; Kembali ke Daftar Dosen</a>

        <?php echo $pesan; ?>

        <h2 class="section-title first-title">1. Edit Data Profil Dosen</h2>

        <form action="dosenedit_process.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="npk" value="<?php echo $dataDosen['npk'] ?>">

            <div class="form-group">
                <label>NPK</label>
                <input type="text" value="<?php echo $dataDosen['npk'] ?>" disabled>
            </div>

            <div class="form-group">
                <label>Username Akun</label>
                <input type="text" value="<?php echo $tampilannUsername ?>" disabled>
            </div>

            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="nama" value="<?php echo $dataDosen['nama'] ?>" required>
            </div>

            <div class="form-group">
                <label>Foto Saat Ini</label>
                <?php
                if ($dataDosen['foto_extension']) {
                    $foto_path = 'image/' . $dataDosen['npk'] . '.' . $dataDosen['foto_extension'];
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

        <h2 class="section-title">2. Reset Password Dosen</h2>

        <?php echo $pesan_password; ?>

        <form action="dosenedit.php?npk=<?php echo $npk; ?>" method="post">
            <input type="hidden" name="npk" value="<?php echo $dataDosen['npk'] ?>">
            <input type="hidden" name="username_akun" value="<?php echo $dataDosen['username'] ?>">

            <p style="margin-bottom: 15px; color: var(--text-color);">
                Mereset password untuk username: <strong><?php echo $tampilannUsername ?></strong>
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

    </div>
</body>

</html>