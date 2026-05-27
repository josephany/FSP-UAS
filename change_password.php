<?php
session_start();

if (!isset($_SESSION['iduser'])) {
    header("location: login.php");
    exit();
}

$pesan = '';

if (isset($_SESSION['password_status'])) {
    if ($_SESSION['password_status'] == 'SUCCESS') {
        $pesan = '<div class="alert success">Password berhasil diubah!</div>';
    } elseif ($_SESSION['password_status'] == 'OLD_PWD_SALAH') {
        $pesan = '<div class="alert error">Password lama salah.</div>';
    } elseif ($_SESSION['password_status'] == 'PWD_TIDAKCOCOK') {
        $pesan = '<div class="alert error">Gagal ganti password, Pastikan kolom ulangi password sama dengan kolom password baru!!</div>';
    } elseif ($_SESSION['password_status'] == 'FAILED') {
        $pesan = '<div class="alert error">Gagal mengubah password.</div>';
    } elseif ($_SESSION['password_status'] == 'PWD_SAMA_DENGAN_YANG_LAMA') {
        $pesan = '<div class="alert error">Password baru sama dengan password lama.</div>';
    } elseif ($_SESSION['password_status'] == 'PWD_TERLALU_PANJANG') {
        $pesan = '<div class="alert error">Password baru terlalu panjang (maksimal 100 karakter)!</div>';
    }
    unset($_SESSION['password_status']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>

    <link rel="stylesheet" href="css/change_password_style.css">
</head>

<body>

    <div class="container">
        <h1>Change Password</h1>
        <p><a href="index.php">&larr; Kembali ke Menu Utama</a></p>

        <?php echo $pesan; ?>

        <form action="changepassword_process.php" method="post">

            <div class="form-group">
                <label>Password Lama</label>
                <input type="password" name="oldPassword" required>
            </div>

            <div class="row">
                <div class="form-group col-half">
                    <label>Password Baru</label>
                    <input type="password" name="newPassword" required>
                </div>

                <div class="form-group col-half">
                    <label>Ulangi Password Baru</label>
                    <input type="password" name="confirmPassword" required>
                </div>
            </div>

            <button type="submit">Change Password</button>
        </form>
    </div>

</body>

</html>