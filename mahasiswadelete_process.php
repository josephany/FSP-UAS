<?php
session_start();

if (!isset($_SESSION['iduser']) || $_SESSION['isadmin'] != 1) {
    header("location: login.php");
    exit();
}

require_once("class/Mahasiswa.php");

if (isset($_GET['nrp'])) {
    $nrp = $_GET['nrp'];
    $mahasiswa = new Mahasiswa();

    $mahasiswa->deleteMahasiswa($nrp);

    header("Location: mahasiswa_tampilan.php");
    exit();
} else {
    header("Location: mahasiswa_tampilan.php");
    exit();
}
