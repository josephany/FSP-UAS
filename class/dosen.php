<?php
require_once("parent.php");
require_once("Users.php");

class Dosen extends OrangTua
{
    private $users;

    public function __construct()
    {
        parent::__construct();
        $this->users = new Users();
    }

    public function isHanyaAngka($input)
    {
        $input_string = (string)$input;
        $i = 0;
        while (isset($input_string[$i])) {
            $kar = $input_string[$i];
            if ($kar < '0' || $kar > '9') {
                return false;
            }
            $i++;
        }
        return $i > 0;
    }

    public function getTotalDosen($cari = "")
    {
        if (empty($cari)) {
            $sql = "SELECT COUNT(*) AS total FROM dosen";
            $stmt = $this->mysqli->prepare($sql);
        } else {
            $sql = "SELECT COUNT(*) AS total FROM dosen WHERE nama LIKE ?";
            $stmt = $this->mysqli->prepare($sql);
            $search = "%" . $cari . "%";
            $stmt->bind_param("s", $search);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            return $result->fetch_assoc()['total'];
        }
        return 0;
    }

    public function getDosenWithPaging($offset, $limit, $cari = "")
    {
        $offset = (int)$offset;
        $limit = (int)$limit;

        if (empty($cari)) {
            $sql = "SELECT d.*, a.username FROM dosen d 
                    LEFT JOIN akun a ON d.npk = a.npk_dosen 
                    ORDER BY d.npk ASC LIMIT ?, ?";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param("ii", $offset, $limit);
        } else {
            $sql = "SELECT d.*, a.username FROM dosen d 
                    LEFT JOIN akun a ON d.npk = a.npk_dosen 
                    WHERE d.nama LIKE ? ORDER BY d.npk ASC LIMIT ?, ?";
            $stmt = $this->mysqli->prepare($sql);
            $search = "%" . $cari . "%";
            $stmt->bind_param("sii", $search, $offset, $limit);
        }

        $stmt->execute();
        return $stmt->get_result();
    }

    public function getDosenByNpk($npk)
    {
        $sql = "SELECT d.*, a.username FROM dosen d 
                LEFT JOIN akun a ON d.npk = a.npk_dosen 
                WHERE d.npk = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("s", $npk);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function insertDosen($npk, $nama, $foto_extension, $username, $password)
    {
        $count_npk = 0;
        while (isset($npk[$count_npk])) $count_npk++;
        if ($count_npk > 6) {
            return 'npkpanjang';
        }

        $count_nama = 0;
        while (isset($nama[$count_nama])) $count_nama++;
        if ($count_nama > 45) {
            return 'namapanjang';
        }

        if ($foto_extension) {
            $count_ext = 0;
            while (isset($foto_extension[$count_ext])) $count_ext++;
            if ($count_ext > 4) {
                return 'fotopanjang';
            }
        }

        $sql_dosen = "INSERT INTO dosen (npk, nama, foto_extension) VALUES (?, ?, ?)";
        $stmt_dosen = $this->mysqli->prepare($sql_dosen);
        $stmt_dosen->bind_param("sss", $npk, $nama, $foto_extension);
        $success_dosen = $stmt_dosen->execute();

        if ($success_dosen) {
            return $this->users->createAccount($username, $password, 0, null, $npk);
        }

        return 'gagalinsert';
    }

    public function updateDosen($npk, $nama, $foto_extension = null)
    {
        $count_npk = 0;
        while (isset($npk[$count_npk])) $count_npk++;
        if ($count_npk > 6) return false;

        $count_nama = 0;
        while (isset($nama[$count_nama])) $count_nama++;
        if ($count_nama > 45) return false;

        if ($foto_extension) {
            $count_ext = 0;
            while (isset($foto_extension[$count_ext])) $count_ext++;
            if ($count_ext > 4) return false;
        }

        if ($foto_extension != null) {
            $sql = "UPDATE dosen SET nama = ?, foto_extension = ? WHERE npk = ?";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param("sss", $nama, $foto_extension, $npk);
        } else {
            $sql = "UPDATE dosen SET nama = ? WHERE npk = ?";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param("ss", $nama, $npk);
        }

        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function deleteDosen($npk)
    {
        $dataAkun = $this->mysqli->query("SELECT username FROM akun WHERE npk_dosen = '{$npk}'");
        if ($dataAkun && $dataAkun->num_rows > 0) {
            $username = $dataAkun->fetch_assoc()['username'];
            $this->users->deleteAccount($username);
        }

        $sql = "DELETE FROM dosen WHERE npk = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("s", $npk);
        $stmt->execute();
        return $stmt->affected_rows;
    }
}
