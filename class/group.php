<?php
require_once("parent.php");

class GroupModel extends OrangTua
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getGroupsByCreator($username_pembuat)
    {
        $sql = "SELECT idgrup, nama, deskripsi, tanggal_pembentukan, jenis, kode_pendaftaran 
                FROM grup 
                WHERE username_pembuat = ? 
                ORDER BY tanggal_pembentukan DESC";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("s", $username_pembuat);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : false;
    }

    public function insertGroup($data, $username_pembuat)
    {
        $nama = $data['nama'];
        $deskripsi = $data['deskripsi'];
        $jenis = $data['jenis'];
        $kode_pendaftaran = $data['kode_pendaftaran'];
        $tanggal = date('Y-m-d H:i:s');

        $sql = "INSERT INTO grup (username_pembuat, nama, deskripsi, tanggal_pembentukan, jenis, kode_pendaftaran) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("ssssss", $username_pembuat, $nama, $deskripsi, $tanggal, $jenis, $kode_pendaftaran);

        $success = $stmt->execute();

        if ($success) {
            $idgrup_baru = $this->mysqli->insert_id;
            $this->addMemberToGroup($idgrup_baru, $username_pembuat);
        }
        return $success;
    }

    public function updateGroup($idgrup, $data)
    {
        $sql = "UPDATE grup SET nama=?, deskripsi=?, jenis=?, kode_pendaftaran=? WHERE idgrup=?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("ssssi", $data['nama'], $data['deskripsi'], $data['jenis'], $data['kode_pendaftaran'], $idgrup);
        return $stmt->execute();
    }

    public function deleteGroup($idgrup)
    {
        $sql = "DELETE FROM grup WHERE idgrup = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("i", $idgrup);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function addMemberToGroup($idgrup, $username)
    {
        $sql = "INSERT INTO member_grup (idgrup, username) VALUES (?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("is", $idgrup, $username);
        $stmt->execute();
    }

    public function addDosenToGroup($idgrup, $username_dosen)
    {
        return $this->addMemberToGroup($idgrup, $username_dosen);
    }

    public function checkDuplicateError()
    {
        return ($this->mysqli->errno == 1062);
    }

    public function getGroupsByMember($username)
    {
        $sql = "SELECT DISTINCT g.idgrup, g.nama, g.deskripsi, g.jenis, g.username_pembuat
            FROM grup g
            LEFT JOIN member_grup m ON g.idgrup = m.idgrup
            WHERE g.username_pembuat = ?  
               OR m.username = ?          
            ORDER BY g.nama ASC";

        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }


    public function getUserRoleInGroup($idgrup, $username)
    {
        $stmt = $this->mysqli->prepare("SELECT username_pembuat FROM grup WHERE idgrup = ?");
        $stmt->bind_param("i", $idgrup);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        if ($res && $res['username_pembuat'] == $username) {
            return "creator";
        }

        $stmt = $this->mysqli->prepare("SELECT * FROM member_grup WHERE idgrup = ? AND username = ?");
        $stmt->bind_param("is", $idgrup, $username);
        $stmt->execute();

        if ($stmt->get_result()->num_rows > 0) {
            return "member";
        }

        return "none";
    }


    public function getGroupById($idgrup)
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM grup WHERE idgrup = ?");
        $stmt->bind_param("i", $idgrup);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_assoc() : false;
    }

    public function searchJoinableGroups($query, $username_mhs)
    {
        $searchTerm = "%" . $query . "%";
        $sql = "SELECT g.idgrup, g.nama, g.deskripsi, g.jenis, g.kode_pendaftaran
                FROM grup g
                LEFT JOIN member_grup m ON g.idgrup = m.idgrup AND m.username = ?
                WHERE m.username IS NULL 
                AND g.jenis = 'Publik' 
                AND (g.nama LIKE ? OR g.kode_pendaftaran = ?)";

        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("sss", $username_mhs, $searchTerm, $query);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : false;
    }

    public function joinGroup($idgrup, $username, $kode = null)
    {
        $stmt = $this->mysqli->prepare("SELECT jenis, kode_pendaftaran FROM grup WHERE idgrup = ?");
        $stmt->bind_param("i", $idgrup);
        $stmt->execute();
        $group = $stmt->get_result()->fetch_assoc();

        if ($group['jenis'] == 'Privat' && $group['kode_pendaftaran'] != $kode)
            return 'wrong_code';

        $stmt = $this->mysqli->prepare("INSERT INTO member_grup (idgrup, username) VALUES (?, ?)");
        $stmt->bind_param("is", $idgrup, $username);

        if ($stmt->execute()) return 'success';
        return $this->checkDuplicateError() ? 'duplicate' : 'error';
    }

    public function leaveGroup($idgrup, $username)
    {
        $stmt = $this->mysqli->prepare("DELETE FROM member_grup WHERE idgrup = ? AND username = ?");
        $stmt->bind_param("is", $idgrup, $username);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }
}
