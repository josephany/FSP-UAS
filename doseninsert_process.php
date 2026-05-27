<?php
session_start();
if (!isset($_SESSION['iduser']) || $_SESSION['isadmin'] != 1) {
    header("location: login.php");
    exit();
}

require_once("class/Dosen.php");
require_once("class/Users.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $npk = $_POST['npk'];
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $foto_extension = null;

    $hanyaAngka = true;
    $i = 0;
    while (isset($npk[$i])) {
        $ch = $npk[$i];
        if ($ch < '0' || $ch > '9') {
            $hanyaAngka = false;
            break;
        }
        $i++;
    }
    if ($hanyaAngka == false || $i == 0) {
        $_SESSION['insert_status'] = 'npksalah';
        header("Location: doseninsert.php");
        exit();
    }

    $len_npk = 0;
    while (isset($npk[$len_npk])) $len_npk++;
    if ($len_npk > 6) {
        $_SESSION['insert_status'] = 'npkpanjang';
        header("Location: doseninsert.php");
        exit();
    }

    $len_nama = 0;
    while (isset($nama[$len_nama])) $len_nama++;
    if ($len_nama > 45) {
        $_SESSION['insert_status'] = 'namapanjang';
        header("Location: doseninsert.php");
        exit();
    }

    $len_username = 0;
    while (isset($username[$len_username])) $len_username++;
    if ($len_username > 20) {
        $_SESSION['insert_status'] = 'userpanjang';
        header("Location: doseninsert.php");
        exit();
    }

    $len_password = 0;
    while (isset($password[$len_password])) $len_password++;
    if ($len_password > 100) {
        $_SESSION['insert_status'] = 'passpanjang';
        header("Location: doseninsert.php");
        exit();
    }

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0 && $_FILES['foto']['size'] > 0) {
        $foto_extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $len_ext = 0;
        while (isset($foto_extension[$len_ext])) $len_ext++;
        if ($len_ext > 4) {
            $_SESSION['insert_status'] = 'fotopanjang';
            header("Location: doseninsert.php");
            exit();
        }
        $target_file = 'image/' . $npk . '.' . $foto_extension;
        move_uploaded_file($_FILES['foto']['tmp_name'], $target_file);
    }

    $dosen = new Dosen();
    $users = new Users();

    $result_npk = $dosen->getDosenByNpk($npk);
    if ($result_npk->num_rows > 0) {
        $_SESSION['insert_status'] = 'npkada';
        header("Location: doseninsert.php");
        exit();
    }

    if ($users->isUsernameExist($username)) {
        $_SESSION['insert_status'] = 'userada';
        header("Location: doseninsert.php");
        exit();
    }

    $insert_result = $dosen->insertDosen($npk, $nama, $foto_extension, $username, $password);

    if ($insert_result === true) {
        header("Location: dosen_tampilan.php");
    } else {
        $_SESSION['insert_status'] = $insert_result;
        header("Location: doseninsert.php");
    }
    exit();
}
