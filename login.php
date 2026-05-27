<?php
session_start();
require_once("class/users.php");

if (isset($_SESSION['iduser'])) {
    header("Location: index.php");
    exit();
}

$pesan = "";
$warna_pesan = "";

if (isset($_GET['logout'])) {
    if ($_GET['logout'] == 'success') {
        $pesan = "Anda telah berhasil keluar (Logout).";
        $warna_pesan = "#28a745";
    }
}

if (isset($_POST['btn_login'])) {

    $iduser = $_POST['iduser'];
    $password = $_POST['password'];

    if (empty($iduser) || empty($password)) {
        $pesan = "User ID dan Password harus diisi.";
        $warna_pesan = "#dc3545";
    } else {
        $users = new Users();
        $login_result = $users->doLogin($iduser, $password);

        if ($login_result == false) {
            $pesan = "User ID atau Password salah.";
            $warna_pesan = "#dc3545";
        } else {
            $_SESSION['iduser'] = $iduser;
            $_SESSION['isadmin'] = $login_result['isadmin'];

            $role_detected = "user";

            if ($login_result['isadmin'] == 1) {
                $role_detected = "admin";
            } else if (isset($login_result['nrp_mahasiswa']) && !empty($login_result['nrp_mahasiswa'])) {
                $role_detected = "mahasiswa";
            } else if (isset($login_result['npk_dosen']) && !empty($login_result['npk_dosen'])) {
                $role_detected = "dosen";
            }

            $_SESSION['role'] = $role_detected;

            header("Location: index.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Grouping System</title>

    <link rel="stylesheet" href="css/login_style.css">
</head>

<body>

    <div class="login-card">
        <h2>Login Aplikasi</h2>

        <div id="login_message">
            <?php
            if ($pesan != "") {
                echo '<span style="color: ' . $warna_pesan . ';">' . $pesan . '</span>';
            }
            ?>
        </div>

        <form method="POST" action="">
            <label for="iduser">Username</label>
            <input type="text" name="iduser" id="iduser" placeholder="Masukkan ID Anda" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Masukkan Password" required>

            <button type="submit" name="btn_login">Masuk</button>
        </form>
    </div>

</body>

</html>