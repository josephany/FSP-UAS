<?php
session_start();
require_once("class/chat.php");

header('Content-Type: application/json');

if (!isset($_SESSION['iduser'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$chatModel = new ChatModel();
$currentUser = $_SESSION['iduser'];
$action = $_REQUEST['action'] ?? '';

try {
    if ($action == 'list_threads') {
        $idgrup = $_GET['idgrup'];
        $data = $chatModel->getThreadsByGroup($idgrup);
        echo json_encode($data);
    } elseif ($action == 'create_thread') {
        $idgrup = $_POST['idgrup'];
        $judul = $_POST['judul'];

        if (empty($judul)) {
            echo json_encode(['status' => 'error', 'message' => 'Judul tidak boleh kosong']);
            exit;
        }

        if ($chatModel->createThread($idgrup, $currentUser, $judul)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal membuat thread']);
        }
    } elseif ($action == 'close_thread') {
        $idthread = $_POST['idthread'];
        if ($chatModel->closeThread($idthread, $currentUser)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menutup thread']);
        }
    } elseif ($action == 'send_chat') {
        $idthread = $_POST['idthread'];
        $isi = $_POST['isi'];

        if (empty($isi)) {
            echo json_encode(['status' => 'error', 'message' => 'Pesan kosong']);
            exit;
        }

        if ($chatModel->sendChat($idthread, $currentUser, $isi)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Thread sudah ditutup']);
        }
    } elseif ($action == 'get_new_chats') {
        $idthread = $_GET['idthread'];
        $last_id = $_GET['last_id'] ?? 0;

        $chats = $chatModel->getNewChats($idthread, $last_id);

        foreach ($chats as &$c) {
            $c['is_me'] = ($c['username_pembuat'] === $currentUser);
        }

        echo json_encode($chats);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
