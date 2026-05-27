<?php
require_once("parent.php");
require_once("Users.php");

class Mahasiswa extends OrangTua
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
        $panjang = 0;

        while (isset($input_string[$panjang])) {
            $panjang++;
        }

        if ($panjang == 0) {
            return false;
        }

        for ($i = 0; $i < $panjang; $i++) {
            $karakter = $input_string[$i];
            if ($karakter < '0' || $karakter > '9') {
                return false;
            }
        }
        return true;
    }

    public function getTotalMahasiswa($cari = "")
    {
        if ($cari === "") {
            $sql = "SELECT COUNT(*) AS total FROM mahasiswa";
            $stmt = $this->mysqli->prepare($sql);
        } else {
            $sql = "SELECT COUNT(*) AS total FROM mahasiswa WHERE nama LIKE ?";
            $stmt = $this->mysqli->prepare($sql);
            $search = "%" . $cari . "%";
            $stmt->bind_param("s", $search);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['total'];
        }
        return 0;
    }

    public function getMahasiswaWithPaging($offset, $limit, $cari = "")
    {
        $offset = (int)$offset;
        $limit = (int)$limit;

        if ($cari === "") {
            $sql = "SELECT m.*, a.username 
                    FROM mahasiswa m 
                    LEFT JOIN akun a ON m.nrp = a.nrp_mahasiswa 
                    ORDER BY m.nrp ASC 
                    LIMIT ?, ?";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param("ii", $offset, $limit);
        } else {
            $sql = "SELECT m.*, a.username 
                    FROM mahasiswa m 
                    LEFT JOIN akun a ON m.nrp = a.nrp_mahasiswa 
                    WHERE m.nama LIKE ? 
                    ORDER BY m.nrp ASC 
                    LIMIT ?, ?";
            $stmt = $this->mysqli->prepare($sql);
            $search = "%" . $cari . "%";
            $stmt->bind_param("sii", $search, $offset, $limit);
        }

        $stmt->execute();
        return $stmt->get_result();
    }

    public function getMahasiswaByNrp($nrp)
    {
        $sql = "SELECT m.*, a.username 
                FROM mahasiswa m 
                LEFT JOIN akun a ON m.nrp = a.nrp_mahasiswa 
                WHERE m.nrp = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("s", $nrp);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function insertMahasiswa($nrp, $nama, $gender, $tanggal_lahir, $angkatan, $foto_extension, $username, $password)
    {
        $count_nrp = 0;
        while (isset($nrp[$count_nrp])) $count_nrp++;
        if ($count_nrp > 9) {
            return 'nrppanjang';
        }

        $count_nama = 0;
        while (isset($nama[$count_nama])) $count_nama++;
        if ($count_nama > 45) {
            return 'namapanjang';
        }

        $count_angkatan = 0;
        while (isset($angkatan[$count_angkatan])) $count_angkatan++;
        if ($count_angkatan > 4) {
            return 'angkatanpanjang';
        }

        if ($foto_extension) {
            $count_ext = 0;
            while (isset($foto_extension[$count_ext])) $count_ext++;
            if ($count_ext > 4) {
                return 'fotopanjang';
            }
        }

        $sql_mhs = "INSERT INTO mahasiswa (nrp, nama, gender, tanggal_lahir, angkatan, foto_extention)
                    VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_mhs = $this->mysqli->prepare($sql_mhs);
        $stmt_mhs->bind_param("ssssss", $nrp, $nama, $gender, $tanggal_lahir, $angkatan, $foto_extension);
        $mhs_success = $stmt_mhs->execute();

        if ($mhs_success) {
            return $this->users->createAccount($username, $password, 0, $nrp, null);
        }

        return 'gagalinsert';
    }

    public function updateMahasiswa($nrp, $nama, $gender, $tanggal_lahir, $angkatan, $foto_extension = null)
    {
        if (!$this->isHanyaAngka($nrp)) {
            return false;
        }

        $len_nrp = 0;
        while (isset($nrp[$len_nrp])) $len_nrp++;
        if ($len_nrp > 9) {
            return false;
        }

        $len_nama = 0;
        while (isset($nama[$len_nama])) $len_nama++;
        if ($len_nama > 45) {
            return false;
        }

        if ($foto_extension) {
            $len_foto = 0;
            while (isset($foto_extension[$len_foto])) $len_foto++;
            if ($len_foto > 4) {
                return false;
            }

            $sql = "UPDATE mahasiswa 
                    SET nama = ?, gender = ?, tanggal_lahir = ?, angkatan = ?, foto_extention = ? 
                    WHERE nrp = ?";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param("ssssss", $nama, $gender, $tanggal_lahir, $angkatan, $foto_extension, $nrp);
        } else {
            $sql = "UPDATE mahasiswa 
                    SET nama = ?, gender = ?, tanggal_lahir = ?, angkatan = ? 
                    WHERE nrp = ?";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param("sssss", $nama, $gender, $tanggal_lahir, $angkatan, $nrp);
        }

        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function deleteMahasiswa($nrp)
    {
        $dataMhs = $this->getMahasiswaByNrp($nrp)->fetch_assoc();
        if (isset($dataMhs['username'])) {
            $username = $dataMhs['username'];
        } else {
            $username = null;
        }

        if ($username) {
            $this->users->deleteAccount($username);
        }

        $sql = "DELETE FROM mahasiswa WHERE nrp = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("s", $nrp);
        $stmt->execute();
        return $stmt->affected_rows;
    }
}
