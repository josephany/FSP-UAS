<?php
session_start();
if (!isset($_SESSION['iduser']) || $_SESSION['isadmin'] != 1) {
    header("location: login.php");
    exit();
}

require_once("class/Mahasiswa.php");
require_once("class/Users.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nrp = $_POST['nrp'];
    $nama = $_POST['nama'];
    $gender = $_POST['gender'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $angkatan = $_POST['angkatan'];

    $username = $_POST['username'];
    $password = $_POST['password'];
    $foto_extension = null;

    $hanyaAngka = true;
    $i = 0;
    while (isset($nrp[$i])) {
        $ch = $nrp[$i];
        if ($ch < '0' || $ch > '9') {
            $hanyaAngka = false;
            break;
        }
        $i++;
    }

    if ($hanyaAngka == false || $i == 0) {
        $_SESSION['insert_status'] = 'nrpsalah';
        header("Location: mahasiswainsert.php");
        exit();
    }

    $len_nrp = 0;
    while (isset($nrp[$len_nrp])) {
        $len_nrp++;
    }
    if ($len_nrp > 9) {
        $_SESSION['insert_status'] = 'nrppanjang';
        header("Location: mahasiswainsert.php");
        exit();
    }

    $len_nama = 0;
    while (isset($nama[$len_nama])) {
        $len_nama++;
    }
    if ($len_nama > 45) {
        $_SESSION['insert_status'] = 'namapanjang';
        header("Location: mahasiswainsert.php");
        exit();
    }

    $len_username = 0;
    while (isset($username[$len_username])) {
        $len_username++;
    }
    if ($len_username > 20) {
        $_SESSION['insert_status'] = 'userpanjang';
        header("Location: mahasiswainsert.php");
        exit();
    }

    $len_password = 0;
    while (isset($password[$len_password])) {
        $len_password++;
    }
    if ($len_password > 100) {
        $_SESSION['insert_status'] = 'passpanjang';
        header("Location: mahasiswainsert.php");
        exit();
    }

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0 && $_FILES['foto']['size'] > 0) {
        $foto_extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);

        $len_ext = 0;
        while (isset($foto_extension[$len_ext])) {
            $len_ext++;
        }
        if ($len_ext > 4) {
            $_SESSION['insert_status'] = 'fotopanjang';
            header("Location: mahasiswainsert.php");
            exit();
        }

        $target_file = 'image/' . $nrp . '.' . $foto_extension;
        move_uploaded_file($_FILES['foto']['tmp_name'], $target_file);
    }

    $mahasiswa = new Mahasiswa();
    $users = new Users();

    $result_nrp = $mahasiswa->getMahasiswaByNrp($nrp);
    if ($result_nrp->num_rows > 0) {
        $_SESSION['insert_status'] = 'nrpada';
        header("Location: mahasiswainsert.php");
        exit();
    }

    if ($users->isUsernameExist($username)) {
        $_SESSION['insert_status'] = 'userada';
        header("Location: mahasiswainsert.php");
        exit();
    }

    $insert_result = $mahasiswa->insertMahasiswa($nrp, $nama, $gender, $tanggal_lahir, $angkatan, $foto_extension, $username, $password);

    if ($insert_result === true) {
        header("Location: mahasiswa_tampilan.php");
    } else {
        $_SESSION['insert_status'] = $insert_result;
        header("Location: mahasiswainsert.php");
    }
    exit();
}
