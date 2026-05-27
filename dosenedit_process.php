<?php
session_start();

if (!isset($_SESSION['iduser']) || $_SESSION['isadmin'] != 1) {
    header("location: login.php");
    exit();
}

require_once("class/Dosen.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $npk = $_POST['npk'];
    $nama = $_POST['nama'];

    $len_nama = 0;
    while (isset($nama[$len_nama])) $len_nama++;
    if ($len_nama > 45) {
        $_SESSION['insert_status'] = 'namapanjang';
        header("Location: dosenedit.php?npk=" . $npk);
        exit();
    }

    $dosen = new Dosen();

    $foto_extension = null;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0 && $_FILES['foto']['size'] > 0) {

        $foto_extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);

        $len_ext = 0;
        while (isset($foto_extension[$len_ext])) $len_ext++;
        if ($len_ext > 4) {
            $_SESSION['insert_status'] = 'fotopanjang';
            header("Location: dosenedit.php?npk=" . $npk);
            exit();
        }

        $target_file = 'image/' . $npk . '.' . $foto_extension;
        move_uploaded_file($_FILES['foto']['tmp_name'], $target_file);

        $dosen->updateDosen($npk, $nama, $foto_extension);
    } else {
        $dosen->updateDosen($npk, $nama);
    }

    header("Location: dosen_tampilan.php");
    exit();
}
