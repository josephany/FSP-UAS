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

        $isi = $_POST['isi'] ?? '';

        $tipe_chat = 'text';
        $file_path = null;

        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {

            $folder = "uploads/chat/";

            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }

            $nama_file = time() . "_" . $_FILES['file']['name'];

            move_uploaded_file(
                $_FILES['file']['tmp_name'],
                $folder . $nama_file
            );

            $file_path = $folder . $nama_file;

            $ext = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

            if (in_array($ext, ['jpg','jpeg','png','gif'])) {
                $tipe_chat = 'image';
            } else {
                $tipe_chat = 'file';
            }
        }
        

        if (empty($isi) && empty($file_path)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Pesan kosong'
            ]);
            exit;
        }

        if ($chatModel->sendChat($idthread,$currentUser,$isi,$tipe_chat,$file_path)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Thread sudah ditutup']);
        }
    } elseif ($action == 'get_members') {

        $idthread = $_GET['idthread'];

        $members = $chatModel->getGroupMembers($idthread);


        // echo json_encode($members);
        echo json_encode([
        'idthread' => $idthread,
        'members' => $members
        ]);
    } elseif ($action == 'get_mention_count') {

        $idgrup = $_GET['idgrup'];

        $total = $chatModel->getMentionCount(
            $currentUser,
            $idgrup
        );

        echo json_encode([
            'total' => $total
        ]);
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
