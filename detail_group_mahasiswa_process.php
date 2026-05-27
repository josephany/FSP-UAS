<?php
session_start();

require_once 'class/event.php';
require_once 'class/member.php';
require_once 'class/group.php';

if (!isset($_SESSION['iduser'])) {
    echo json_encode(['status' => 'error', 'pesan' => 'Sesi habis.']);
    exit();
}

$username_mhs = $_SESSION['iduser'];

$eventModel = new EventModel();
$memberModel = new MemberModel();
$groupModel = new GroupModel();

$action = "";
if (isset($_POST['action'])) {
    $action = $_POST['action'];
} else if (isset($_GET['action'])) {
    $action = $_GET['action'];
}

if ($action == 'get_group_info') {
    $id = $_GET['idgrup'];
    $data = $groupModel->getGroupById($id);
    if (!$data) {
        $data = [];
    }
    echo json_encode($data);
    exit();
}

if ($action == 'list_events') {
    $idgrup = $_GET['idgrup'];

    $data = $eventModel->getEventsForMahasiswa($idgrup, $username_mhs);

    if (!$data) {
        $data = [];
    }
    echo json_encode($data);
    exit();
}

if ($action == 'list_members') {
    $idgrup = $_GET['idgrup'];
    $data = $memberModel->getMembersByGroup($idgrup);
    if (!$data) {
        $data = [];
    }
    echo json_encode($data);
    exit();
}

if ($action == 'leave_group') {
    $idgrup = $_POST['idgrup'];

    if ($memberModel->deleteMember($idgrup, $username_mhs) > 0) {
        echo json_encode(['status' => 'success', 'pesan' => 'Anda telah keluar dari grup.']);
    } else {
        echo json_encode(['status' => 'error', 'pesan' => 'Gagal keluar grup.']);
    }
    exit();
}

echo json_encode(['status' => 'error', 'pesan' => 'Aksi tidak valid']);
