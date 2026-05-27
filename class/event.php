<?php
require_once("parent.php");

class EventModel extends OrangTua
{
    public function __construct()
    {
        parent::__construct();
    }

    private function createSlug($judul)
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $judul)));
    }

    public function getEventById($idevent)
    {
        $sql = "SELECT * FROM event WHERE idevent = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("i", $idevent);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            return $result->fetch_assoc();
        }
        return false;
    }

    public function getEventsByGroup($idgrup)
    {
        $sql = "SELECT * FROM event WHERE idgrup = ? ORDER BY tanggal DESC";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("i", $idgrup);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    public function getEventsForMahasiswa($idgrup, $username_mhs)
    {
        $sql = "SELECT e.* FROM event e
                JOIN grup g ON e.idgrup = g.idgrup
                LEFT JOIN member_grup m ON g.idgrup = m.idgrup AND m.username = ?
                WHERE e.idgrup = ? 
                AND (g.jenis = 'Publik' OR m.username IS NOT NULL)
                AND e.jenis = 'Publik'
                ORDER BY e.tanggal DESC";

        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("si", $username_mhs, $idgrup);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function insertEvent($idgrup, $data)
    {
        $judul      = $data['judul'];
        $tanggal    = $data['tanggal'];
        $keterangan = $data['keterangan'];
        $jenis      = $data['jenis'];
        $poster_extension = $data['poster_extension'];

        $judul_slug = $this->createSlug($judul);

        $sql = "INSERT INTO event (idgrup, judul, `judul-slug`, tanggal, keterangan, jenis, poster_extension) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->mysqli->prepare($sql);
        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param(
            "issssss",
            $idgrup,
            $judul,
            $judul_slug,
            $tanggal,
            $keterangan,
            $jenis,
            $poster_extension
        );

        $success = $stmt->execute();
        if ($success) {
            return $this->mysqli->insert_id;
        }
        return false;
    }

    public function updateEvent($idevent, $idgrup, $data)
    {
        $judul = $data['judul'];
        $tanggal = $data['tanggal'];
        $keterangan = $data['keterangan'];
        $jenis = $data['jenis'];
        $poster_extension = $data['poster_extension'];
        $judul_slug = $this->createSlug($judul);

        if ($poster_extension != "") {
            $sql = "UPDATE event SET judul=?, `judul-slug`=?, tanggal=?, keterangan=?, jenis=?, poster_extension=? 
                    WHERE idevent=? AND idgrup=?";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param("ssssssii", $judul, $judul_slug, $tanggal, $keterangan, $jenis, $poster_extension, $idevent, $idgrup);
        } else {
            $sql = "UPDATE event SET judul=?, `judul-slug`=?, tanggal=?, keterangan=?, jenis=? 
                    WHERE idevent=? AND idgrup=?";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param("sssssii", $judul, $judul_slug, $tanggal, $keterangan, $jenis, $idevent, $idgrup);
        }

        return $stmt->execute();
    }

    public function deleteEvent($idevent, $idgrup)
    {
        $sql = "DELETE FROM event WHERE idevent = ? AND idgrup = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("ii", $idevent, $idgrup);
        $stmt->execute();

        return $stmt->affected_rows;
    }
}
