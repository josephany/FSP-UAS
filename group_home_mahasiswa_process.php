<?php
session_start();
require_once 'class/group.php';

if (!isset($_SESSION['iduser'])) {
    echo json_encode(['status' => 'error', 'pesan' => 'Sesi habis. Silakan login ulang.']);
    exit();
}

$username_mhs = $_SESSION['iduser'];
$groupModel = new GroupModel();

$action = "";
if (isset($_POST['action'])) {
    $action = $_POST['action'];
} else if (isset($_GET['action'])) {
    $action = $_GET['action'];
}


if ($action == 'list_my_groups') {
    $data = $groupModel->getGroupsByMember($username_mhs);
    if (!$data) {
        $data = [];
    }
    echo json_encode($data);
    exit();
}

if ($action == 'search_groups') {
    $keyword = "";
    if (isset($_GET['keyword'])) {
        $keyword = $_GET['keyword'];
    }

    $data = $groupModel->searchJoinableGroups($keyword, $username_mhs);
    if (!$data) {
        $data = [];
    }
    echo json_encode($data);
    exit();
}

if ($action == 'join_group') {
    $idgrup = $_POST['idgrup'];
    $kode_input = $_POST['kode_pendaftaran'];

    $hasil = $groupModel->joinGroup($idgrup, $username_mhs, $kode_input);

    if ($hasil == 'success') {
        echo json_encode(['status' => 'success', 'pesan' => 'Berhasil bergabung ke grup!']);
    } else if ($hasil == 'wrong_code') {
        echo json_encode(['status' => 'error', 'pesan' => 'Kode pendaftaran SALAH. Silakan tanya Dosen Anda.']);
    } else if ($hasil == 'duplicate') {
        echo json_encode(['status' => 'error', 'pesan' => 'Anda sudah bergabung di grup ini.']);
    } else {
        echo json_encode(['status' => 'error', 'pesan' => 'Gagal bergabung. Terjadi kesalahan sistem.']);
    }
    exit();
}

if ($action == 'leave_group') {
    $idgrup = $_POST['idgrup'];

    if ($groupModel->leaveGroup($idgrup, $username_mhs)) {
        echo json_encode(['status' => 'success', 'pesan' => 'Anda telah keluar dari grup.']);
    } else {
        echo json_encode(['status' => 'error', 'pesan' => 'Gagal keluar grup.']);
    }
    exit();
}
