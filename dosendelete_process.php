<?php
session_start();
if (!isset($_SESSION['iduser']) || $_SESSION['isadmin'] != 1) {
    header("location: login.php");
    exit();
}

require_once("class/Dosen.php");

if (isset($_GET['npk'])) {
    $npk = $_GET['npk'];
    $dosen = new Dosen();

    $dosen->deleteDosen($npk);

    header("Location: dosen_tampilan.php");
    exit();
} else {
    header("Location: dosen_tampilan.php");
    exit();
}
