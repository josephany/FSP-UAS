<?php
session_start();

if (!isset($_SESSION['iduser']) || $_SESSION['isadmin'] != 1) {
    header("location: login.php");
    exit();
}

require_once("class/Mahasiswa.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nrp = $_POST['nrp'];
    $nama = $_POST['nama'];
    $gender = $_POST['gender'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $angkatan = $_POST['angkatan'];

    $len_nama = 0;
    while (isset($nama[$len_nama])) $len_nama++;
    if ($len_nama > 45) {
        $_SESSION['insert_status'] = 'namapanjang';
        header("Location: mahasiswaedit.php?nrp=" . $nrp);
        exit();
    }

    $hanyaAngka = true;
    $i = 0;
    while (isset($angkatan[$i])) {
        $ch = $angkatan[$i];
        if ($ch < '0' || $ch > '9') {
            $_SESSION['insert_status'] = 'angkatansalah';
            break;
        }
        $i++;
    }
    if ($i != 4) {
        $_SESSION['insert_status'] = 'angkatanlebihdari4';
        header("Location: mahasiswaedit.php?nrp=" . $nrp);
        exit();
    }


    $mahasiswa = new Mahasiswa();

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0 && $_FILES['foto']['size'] > 0) {

        $foto_extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);

        $len_ext = 0;
        while (isset($foto_extension[$len_ext])) $len_ext++;
        if ($len_ext > 4) {
            $_SESSION['insert_status'] = 'fotopanjang';
            header("Location: mahasiswaedit.php?nrp=" . $nrp);
            exit();
        }

        $target_file = 'image/' . $nrp . '.' . $foto_extension;
        move_uploaded_file($_FILES['foto']['tmp_name'], $target_file);

        $mahasiswa->updateMahasiswa($nrp, $nama, $gender, $tanggal_lahir, $angkatan, $foto_extension);
    } else {
        $mahasiswa->updateMahasiswa($nrp, $nama, $gender, $tanggal_lahir, $angkatan);
    }

    header("Location: mahasiswa_tampilan.php");
    exit();
}
