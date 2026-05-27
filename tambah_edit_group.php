<?php
session_start();
require_once("class/data.php");
require_once("class/parent.php");

if (!isset($_SESSION['iduser'])) {
    header("Location: login.php");
    exit;
}

$id_dosen = $_SESSION['iduser'];
$idgrup = isset($_GET['id']) ? $_GET['id'] : '';

$nama = '';
$deskripsi = '';
$jenis = 'Privat';
$kode_pendaftaran = '';
$mode = 'Tambah';

if (!empty($idgrup)) {
    $mode = 'Edit';

    class FormLoader extends OrangTua
    {
        public function getGroupData($id, $dosen)
        {
            $sql = "SELECT * FROM grup WHERE idgrup = ? AND username_pembuat = ?";
            $stmt = $this->mysqli->prepare($sql);

            if (!$stmt) {
                die("Error SQL: " . $this->mysqli->error);
            }

            $stmt->bind_param("is", $id, $dosen);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        }
    }

    $loader = new FormLoader();
    $data = $loader->getGroupData($idgrup, $id_dosen);

    if ($data) {
        $nama = $data['nama'];
        $deskripsi = $data['deskripsi'];
        $jenis = $data['jenis'];
        $kode_pendaftaran = $data['kode_pendaftaran'];
    } else {
        echo "Data tidak ditemukan atau Anda tidak memiliki akses.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $mode; ?> Group</title>

    <link rel="stylesheet" href="css/tambah_edit_group_style.css">

    <script src="jquery/jquery-3.7.1.js"></script>
</head>

<body>

    <div class="container">
        <h2><?php echo $mode; ?> Group</h2>
        <hr>

        <form id="formGroup">
            <input type="hidden" name="action" value="simpan">
            <input type="hidden" name="idgrup" value="<?php echo $idgrup; ?>">

            <label>Nama Group</label>
            <input type="text" name="nama" required value="<?php echo ($nama); ?>" placeholder="Contoh: Pemrograman Web A">

            <label>Deskripsi</label>
            <textarea name="deskripsi" rows="4" placeholder="Deskripsi singkat..."><?php echo ($deskripsi); ?></textarea>

            <label>Jenis</label>
            <select name="jenis">
                <option value="Privat" <?php if ($jenis == 'Privat') echo 'selected'; ?>>Privat</option>
                <option value="Publik" <?php if ($jenis == 'Publik') echo 'selected'; ?>>Publik</option>
            </select>

            <?php if ($mode == 'Edit') { ?>
                <label>Kode Pendaftaran</label>
                <input type="text"
                    name="kode_pendaftaran"
                    value="<?php echo $kode_pendaftaran; ?>"
                    readonly
                    class="input-readonly">
                <small>Kode pendaftaran bersifat permanen dan tidak dapat diubah.</small>
            <?php } else { ?>
                <div class="info-box">
                    &#9432; Kode pendaftaran akan dibuat otomatis oleh sistem setelah disimpan.
                </div>
            <?php } ?>

            <div class="btn-action">
                <a href="group_home_dosen.php" class="btn btn-abu">Batal</a>
                <button type="submit" class="btn btn-biru">Simpan Data</button>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $('#formGroup').on('submit', function(e) {
                e.preventDefault();

                let btn = $(this).find('button[type="submit"]');
                let textAwal = btn.text();
                btn.text('Menyimpan...').prop('disabled', true);

                $.ajax({
                    url: 'group_home_dosen_process.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {
                            alert(response.pesan);
                            window.location.href = 'group_home_dosen.php';
                        } else {
                            alert(response.pesan);
                            btn.text(textAwal).prop('disabled', false);
                        }
                    },
                    error: function() {
                        alert("Gagal menghubungi server database.");
                        btn.text(textAwal).prop('disabled', false);
                    }
                });
            });
        });
    </script>

</body>

</html>