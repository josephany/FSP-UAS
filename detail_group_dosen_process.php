<?php
session_start();

require_once 'class/event.php';
require_once 'class/member.php';
require_once 'class/group.php';

if (!isset($_SESSION['iduser'])) {
    echo json_encode(['status' => 'error', 'pesan' => 'Sesi habis.']);
    exit();
}

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
    $data = $eventModel->getEventsByGroup($idgrup);
    if (!$data) {
        $data = [];
    }
    echo json_encode($data);
    exit();
}

if ($action == 'get_event_detail') {
    $idevent = $_GET['idevent'];
    $data = $eventModel->getEventById($idevent);
    if (!$data) {
        $data = [];
    }
    echo json_encode($data);
    exit();
}

if ($action == 'simpan_event') {
    $idgrup = $_POST['idgrup'];
    $idevent = $_POST['idevent'];

    $extension = "";
    $uploadDir = "image/event/";

    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['poster']['tmp_name'];
        $fileName = $_FILES['poster']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = array('jpg', 'jpeg', 'png');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $extension = $fileExtension;
        } else {
            echo json_encode(['status' => 'error', 'pesan' => 'Format file salah. Hanya JPG/PNG.']);
            exit();
        }
    }

    $dataInput = [
        'judul' => $_POST['judul'],
        'tanggal' => $_POST['tanggal'],
        'keterangan' => $_POST['keterangan'],
        'jenis' => $_POST['jenis'],
        'poster_extension' => $extension
    ];

    if ($idevent == "") {
        $newId = $eventModel->insertEvent($idgrup, $dataInput);
        if ($newId) {
            if ($extension != "") {
                move_uploaded_file($_FILES['poster']['tmp_name'], $uploadDir . $newId . "." . $extension);
            }
            echo json_encode(['status' => 'success', 'pesan' => 'Event berhasil dibuat']);
        } else {
            echo json_encode(['status' => 'error', 'pesan' => 'Gagal membuat event']);
        }
    } else {
        if ($eventModel->updateEvent($idevent, $idgrup, $dataInput)) {
            if ($extension != "") {
                move_uploaded_file($_FILES['poster']['tmp_name'], $uploadDir . $idevent . "." . $extension);
            }
            echo json_encode(['status' => 'success', 'pesan' => 'Event berhasil diupdate']);
        } else {
            echo json_encode(['status' => 'error', 'pesan' => 'Gagal update event']);
        }
    }
    exit();
}

if ($action == 'hapus_event') {
    $idevent = $_POST['idevent'];
    $idgrup = $_POST['idgrup'];
    if ($eventModel->deleteEvent($idevent, $idgrup) > 0) {
        echo json_encode(['status' => 'success', 'pesan' => 'Event dihapus']);
    } else {
        echo json_encode(['status' => 'error', 'pesan' => 'Gagal menghapus event']);
    }
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
if ($action == 'cari_mahasiswa') {
    $idgrup = $_GET['idgrup'];
    $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
    $data = $memberModel->searchNonMembers($idgrup, $keyword);
    if (!$data) {
        $data = [];
    }
    echo json_encode($data);
    exit();
}
if ($action == 'tambah_member') {
    $idgrup = $_POST['idgrup'];
    $username = $_POST['username'];
    if ($memberModel->addMember($idgrup, $username)) {
        echo json_encode(['status' => 'success', 'pesan' => 'Member berhasil ditambahkan']);
    } else {
        if ($memberModel->checkDuplicateError()) {
            echo json_encode(['status' => 'error', 'pesan' => 'User sudah ada']);
        } else {
            echo json_encode(['status' => 'error', 'pesan' => 'Gagal tambah member']);
        }
    }
    exit();
}
if ($action == 'hapus_member') {
    $idgrup = $_POST['idgrup'];
    $username = $_POST['username'];
    if ($memberModel->deleteMember($idgrup, $username) > 0) {
        echo json_encode(['status' => 'success', 'pesan' => 'Member dikeluarkan']);
    } else {
        echo json_encode(['status' => 'error', 'pesan' => 'Gagal hapus member']);
    }
    exit();
}
