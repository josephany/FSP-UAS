<?php
require_once("parent.php");

class MemberModel extends OrangTua
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getMembersByGroup($idgrup)
    {
        $sql = "
            SELECT 
                m.username, 
                mhs.nrp, 
                d.npk, 
                a.username AS username_akun,
                mhs.nama AS nama_mhs, 
                d.nama AS nama_dosen,
                CASE 
                    WHEN a.nrp_mahasiswa IS NOT NULL THEN 'Mahasiswa'
                    WHEN a.npk_dosen IS NOT NULL THEN 'Dosen'
                    ELSE 'Admin/User'
                END AS role
            FROM member_grup m
            JOIN akun a ON m.username = a.username
            LEFT JOIN mahasiswa mhs ON a.nrp_mahasiswa = mhs.nrp
            LEFT JOIN dosen d ON a.npk_dosen = d.npk
            WHERE m.idgrup = ?
            ORDER BY (CASE WHEN mhs.nama IS NOT NULL THEN mhs.nama ELSE d.nama END) ASC
        ";

        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("i", $idgrup);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($result as &$row) {
            $row['id_nomor_induk'] = $row['nrp'] ?? $row['npk'] ?? $row['username_akun'];
            $row['nama'] = $row['nama_mhs'] ?? $row['nama_dosen'] ?? $row['username'];
        }

        return $result;
    }

    public function searchNonMembers($idgrup, $query)
    {
        $search = "%" . $query . "%";

        $sql = "
            (
                SELECT 
                    a.username, 
                    d.npk AS id_nomor_induk,
                    d.nama AS nama,
                    'Dosen' AS role
                FROM akun a
                JOIN dosen d ON a.npk_dosen = d.npk
                WHERE a.username NOT IN (SELECT username FROM member_grup WHERE idgrup = ?)
                  AND (? = '' OR d.nama LIKE ? OR d.npk LIKE ? OR a.username LIKE ?)
            )
            UNION ALL
            (
                SELECT 
                    a.username, 
                    mhs.nrp AS id_nomor_induk,
                    mhs.nama AS nama,
                    'Mahasiswa' AS role
                FROM akun a
                JOIN mahasiswa mhs ON a.nrp_mahasiswa = mhs.nrp
                WHERE a.username NOT IN (SELECT username FROM member_grup WHERE idgrup = ?)
                  AND (? = '' OR mhs.nama LIKE ? OR mhs.nrp LIKE ? OR a.username LIKE ?)
            )
            LIMIT 20
        ";

        $stmt = $this->mysqli->prepare($sql);

        $stmt->bind_param(
            "issssissss",
            $idgrup,
            $query,
            $search,
            $search,
            $search,
            $idgrup,
            $query,
            $search,
            $search,
            $search
        );

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    public function addMember($idgrup, $username)
    {
        $sql = "INSERT INTO member_grup (idgrup, username) VALUES (?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("is", $idgrup, $username);
        return $stmt->execute();
    }

    public function deleteMember($idgrup, $username)
    {
        $sql = "DELETE FROM member_grup WHERE idgrup = ? AND username = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("is", $idgrup, $username);
        $stmt->execute();
        return $stmt->affected_rows;
    }

    public function checkDuplicateError()
    {
        if ($this->mysqli->errno == 1062) {
            return true;
        }
        return false;
    }
}
