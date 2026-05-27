<?php
session_start();
require_once 'class/group.php';

if (!isset($_SESSION['iduser'])) {
    echo json_encode(['status' => 'error', 'pesan' => 'Sesi habis. Silakan login ulang.']);
    exit();
}

$username_dosen = $_SESSION['iduser'];
$groupModel = new GroupModel();

$action = "";
if (isset($_POST['action'])) {
    $action = $_POST['action'];
} else if (isset($_GET['action'])) {
    $action = $_GET['action'];
}

$response = [];

if ($action == 'list') {

    $data = $groupModel->getGroupsByMember($username_dosen);
    if (!$data)
        $data = [];

    echo json_encode($data);
    exit();
}

if ($action == 'simpan') {
    if (isset($_POST['idgrup']) && $_POST['idgrup'] != "") {

        $role = $groupModel->getUserRoleInGroup($_POST['idgrup'], $username_dosen);

        if ($role != "creator") {
            echo json_encode(['status' => 'error', 'pesan' => 'Akses ditolak! Anda bukan pemilik grup.']);
            exit();
        }
    }

    $idgrup = $_POST['idgrup'];

    $dataForm = [
        'nama' => $_POST['nama'],
        'deskripsi' => $_POST['deskripsi'],
        'jenis' => $_POST['jenis']
    ];

    if ($idgrup == "") {
        $kodeOtomatis = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
        $dataForm['kode_pendaftaran'] = $kodeOtomatis;

        if ($groupModel->insertGroup($dataForm, $username_dosen)) {
            $response = [
                'status' => 'success',
                'pesan' => "Group BERHASIL dibuat!\n\nKode Pendaftaran: $kodeOtomatis\n\nBagikan kode ini ke mahasiswa."
            ];
        } else {
            $response = ['status' => 'error', 'pesan' => 'Gagal membuat group.'];
        }
    } else {
        if (isset($_POST['kode_pendaftaran'])) {
            $dataForm['kode_pendaftaran'] = $_POST['kode_pendaftaran'];
        }

        if ($groupModel->updateGroup($idgrup, $dataForm)) {
            $response = ['status' => 'success', 'pesan' => 'Berhasil memperbarui data group.'];
        } else {
            $response = ['status' => 'error', 'pesan' => 'Gagal memperbarui data group.'];
        }
    }

    echo json_encode($response);
    exit();
}

if ($action == 'hapus') {

    $role = $groupModel->getUserRoleInGroup($_POST['idgrup'], $username_dosen);

    if ($role != "creator") {
        echo json_encode(['status' => 'error', 'pesan' => 'Akses ditolak! Anda tidak bisa menghapus grup.']);
        exit();
    }

    if ($groupModel->deleteGroup($_POST['idgrup']) > 0) {
        echo json_encode(['status' => 'success', 'pesan' => 'Group berhasil dihapus.']);
    } else {
        echo json_encode(['status' => 'error', 'pesan' => 'Gagal menghapus group.']);
    }
    exit();
}
