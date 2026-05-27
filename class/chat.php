<?php
require_once("parent.php");

class ChatModel extends OrangTua
{
    public function __construct()
    {
        parent::__construct();
    }

    public function createThread($idgrup, $username, $judul_input)
    {
        $sql = "INSERT INTO thread (idgrup, username_pembuat, tanggal_pembuatan, status) VALUES (?, ?, NOW(), 'Open')";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("is", $idgrup, $username);

        if ($stmt->execute()) {
            $idthread_baru = $stmt->insert_id;
            $isi_chat = "Topik Diskusi: " . $judul_input;
            $this->sendChat($idthread_baru, $username, $isi_chat);

            return true;
        }
        return false;
    }

    public function getThreadsByGroup($idgrup)
    {
        $sql = "SELECT t.*, 
                   m.nama as nama_mhs, 
                   d.nama as nama_dosen, 
                   t.username_pembuat,
                   (SELECT c.isi FROM chat c WHERE c.idthread = t.idthread ORDER BY c.idchat ASC LIMIT 1) as judul_dari_chat,
                   DATE_FORMAT(t.tanggal_pembuatan, '%d-%m-%Y %H:%i') as tgl_format
            FROM thread t
            LEFT JOIN akun a ON t.username_pembuat = a.username
            LEFT JOIN mahasiswa m ON a.nrp_mahasiswa = m.nrp
            LEFT JOIN dosen d ON a.npk_dosen = d.npk
            WHERE t.idgrup = ?
            ORDER BY t.tanggal_pembuatan DESC";

        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("i", $idgrup);
        $stmt->execute();
        $result = $stmt->get_result();

        $threads = [];
        while ($row = $result->fetch_assoc()) {
            $row['nama_pembuat'] = $row['nama_mhs'] ?? $row['nama_dosen'] ?? $row['username_pembuat'];

            if (!empty($row['judul_dari_chat'])) {
                $judul_bersih = str_replace("Topik Diskusi: ", "", $row['judul_dari_chat']);
                $row['judul'] = mb_strimwidth($judul_bersih, 0, 50, "...");
            } else {
                $row['judul'] = "Diskusi Tanggal " . $row['tgl_format'];
            }

            $threads[] = $row;
        }

        return $threads;
    }

    public function getThreadDetail($idthread)
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM thread WHERE idthread = ?");
        $stmt->bind_param("i", $idthread);
        $stmt->execute();
        $thread = $stmt->get_result()->fetch_assoc();

        if ($thread) {
            $thread['judul'] = "Diskusi #" . $thread['idthread'];
        }
        return $thread;
    }

    public function closeThread($idthread, $username_peminta)
    {
        $stmt = $this->mysqli->prepare("UPDATE thread SET status = 'Close' WHERE idthread = ? AND username_pembuat = ?");
        $stmt->bind_param("is", $idthread, $username_peminta);
        return $stmt->execute();
    }

    public function sendChat($idthread,$username,$isi,$tipe_chat='text',$file_path=null)
    {
        $thread = $this->getThreadDetail($idthread);

        if (!$thread || $thread['status'] == 'Close') {
            return false;
        }

        $sql = "INSERT INTO chat(idthread,username_pembuat,isi,tipe_chat,file_path,tanggal_pembuatan) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param( "issss",$idthread,$username,$isi,$tipe_chat,$file_path);

        if ($stmt->execute()) {

            $idchat = $stmt->insert_id;
            preg_match_all('/@(\w+)/', $isi, $matches);
            if (!empty($matches[1])) {

                foreach ($matches[1] as $usernameMention) {

                    $q = $this->mysqli->prepare("
                        INSERT INTO mention_chat
                        (idchat, username_mentioned, is_read)
                        VALUES (?, ?, 0)
                    ");

                    $q->bind_param(
                        "is",
                        $idchat,
                        $usernameMention
                    );

                    $q->execute();
                }

            }

            return true;
        }

        return false;
    }

    public function getNewChats($idthread, $last_id_chat)
    {
        $sql = "SELECT c.*, 
                       m.nama as nama_mhs, 
                       d.nama as nama_dosen, 
                       c.username_pembuat,
                       DATE_FORMAT(c.tanggal_pembuatan, '%d-%m-%Y %H:%i') as waktu_format
                FROM chat c
                LEFT JOIN akun a ON c.username_pembuat = a.username
                LEFT JOIN mahasiswa m ON a.nrp_mahasiswa = m.nrp
                LEFT JOIN dosen d ON a.npk_dosen = d.npk
                WHERE c.idthread = ? AND c.idchat > ?
                ORDER BY c.idchat ASC";

        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("ii", $idthread, $last_id_chat);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($result as &$c) {
            $c['nama_pengirim'] = $c['nama_mhs'] ?? $c['nama_dosen'] ?? $c['username_pembuat'];
        }

        return $result;
    }

    public function getGroupMembers($idthread)
    {
        $sql = "
            SELECT a.username,
                   COALESCE(m.nama, d.nama) as nama

            FROM thread t

            JOIN member_grup gm
                ON gm.idgrup = t.idgrup

            JOIN akun a
                ON a.username = gm.username

            LEFT JOIN mahasiswa m
                ON m.nrp = a.nrp_mahasiswa

            LEFT JOIN dosen d
                ON d.npk = a.npk_dosen

            WHERE t.idthread = ?
        ";

        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("i", $idthread);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getMentionCount($username, $idgrup)
    {
        $sql = "
            SELECT COUNT(*) as total

            FROM mention_chat mc

            JOIN chat c
                ON c.idchat = mc.idchat

            JOIN thread t
                ON t.idthread = c.idthread

            WHERE mc.username_mentioned = ?
            AND mc.is_read = 0
            AND t.idgrup = ?
        ";

        $stmt = $this->mysqli->prepare($sql);

        $stmt->bind_param("si", $username, $idgrup);

        $stmt->execute();

        return $stmt->get_result()->fetch_assoc()['total'];
    }
    public function readMention($idthread, $username)
    {
        $sql = "
            UPDATE mention_chat mc

            JOIN chat c
                ON c.idchat = mc.idchat

            SET mc.is_read = 1

            WHERE c.idthread = ?
            AND mc.username_mentioned = ?
        ";

        $stmt = $this->mysqli->prepare($sql);

        $stmt->bind_param("is", $idthread, $username);

        return $stmt->execute();
    }
}
