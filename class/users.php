<?php
require_once("parent.php");

class Users extends OrangTua
{
    public function __construct()
    {
        parent::__construct();
    }

    public function doLogin($iduser, $plain_pwd)
    {
        $sql = "SELECT username, password, isadmin, nrp_mahasiswa, npk_dosen FROM akun WHERE username = ?";

        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("s", $iduser);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row['password'];

            if (password_verify($plain_pwd, $hashedPassword)) {
                if ($row['username'] === $iduser) {
                    return $row;
                }
            }
        }

        return false;
    }


    public function createAccount($username, $password, $isadmin, $nrp_mahasiswa = null, $npk_dosen = null)
    {
        $len_user = 0;
        while (isset($username[$len_user])) $len_user++;
        if ($len_user > 20) return false;

        $len_pass = 0;
        while (isset($password[$len_pass])) $len_pass++;
        if ($len_pass > 100) return false;

        if ($nrp_mahasiswa) {
            $len_nrp = 0;
            while (isset($nrp_mahasiswa[$len_nrp])) $len_nrp++;
            if ($len_nrp > 9) return false;
        }

        if ($npk_dosen) {
            $len_npk = 0;
            while (isset($npk_dosen[$len_npk])) $len_npk++;
            if ($len_npk > 6) return false;
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $isadmin = (int)$isadmin;

        $sql = "INSERT INTO akun (username, password, isadmin, nrp_mahasiswa, npk_dosen) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("ssiss", $username, $password_hash, $isadmin, $nrp_mahasiswa, $npk_dosen);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function deleteAccount($username)
    {
        $sql = "DELETE FROM akun WHERE username = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function isUsernameExist($username)
    {
        $sql = "SELECT username FROM akun WHERE username = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function verifyPassword($username, $passwordToVerify)
    {
        $sql = "SELECT password FROM akun WHERE username = ?";
        $stmt = $this->mysqli->prepare($sql);

        if ($stmt == false) return false;

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if (password_verify($passwordToVerify, $row['password'])) {
                return true;
            }
        }
        return false;
    }

    public function changePassword($username, $new_password)
    {
        $len_new = 0;
        while (isset($new_password[$len_new])) $len_new++;
        if ($len_new > 100) return false;

        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        $sql = "UPDATE akun SET password = ? WHERE username = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("ss", $password_hash, $username);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function searchAccountsByName($searchName)
    {
        $sql = "
            SELECT a.username, a.isadmin, a.nrp_mahasiswa, a.npk_dosen,
                   m.nama AS nama_mahasiswa, d.nama AS nama_dosen
            FROM akun a
            LEFT JOIN mahasiswa m ON a.nrp_mahasiswa = m.nrp_mahasiswa
            LEFT JOIN dosen d ON a.npk_dosen = d.npk_dosen
            WHERE m.nama LIKE ? OR d.nama LIKE ?";

        $search = "%" . $searchName . "%";
        $stmt = $this->mysqli->prepare($sql);

        if ($stmt == false) return false;

        $stmt->bind_param("ss", $search, $search);
        $stmt->execute();

        return $stmt->get_result();
    }
}
