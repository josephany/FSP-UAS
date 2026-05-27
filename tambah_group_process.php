<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['iduser'])) {
    echo json_encode([
        'status' => 'error',
        'pesan' => 'Session expired.'
    ]);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action == 'simpan') {

    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $jenis = $_POST['jenis'];

    $kode = strtoupper(substr(md5(uniqid()), 0, 6));

    $id_dosen = $_SESSION['iduser'];

    $sql = "INSERT INTO tgroup (nama, deskripsi, jenis, kode_pendaftaran, iddosen, tanggal_pembentukan)
            VALUES ('$nama', '$deskripsi', '$jenis', '$kode', '$id_dosen', NOW())";

    $q = mysqli_query($conn, $sql);

    if ($q) {
        echo json_encode([
            'status' => 'success',
            'pesan' => "Group berhasil dibuat!\nKode Pendaftaran: $kode"
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'pesan' => 'Gagal menambah group.'
        ]);
    }
}
