<?php
session_start();
require_once("class/data.php");
require_once("class/parent.php");

if (!isset($_SESSION['iduser'])) {
    header("Location: login.php");
    exit;
}

$idgrup = isset($_GET['id']) ? $_GET['id'] : '';
$username_mhs = $_SESSION['iduser'];
$pesan_error = "";

if (isset($_POST['tombol_gabung'])) {
    $post_idgrup = $_POST['idgrup'];
    $post_kode = $_POST['kode_pendaftaran'];

    $db = new mysqli(SERVER_NAME, USER_NAME, PASSWORD, DB_NAME);

    $stmt = $db->prepare("SELECT jenis, kode_pendaftaran FROM grup WHERE idgrup = ?");
    $stmt->bind_param("i", $post_idgrup);
    $stmt->execute();
    $grupInfo = $stmt->get_result()->fetch_assoc();

    if ($grupInfo) {

        if ($grupInfo['jenis'] == 'Privat' && $grupInfo['kode_pendaftaran'] != $post_kode) {
            $pesan_error = "Kode pendaftaran salah!";
        } else {
            $stmtInsert = $db->prepare("INSERT INTO member_grup (idgrup, username) VALUES (?, ?)");
            $stmtInsert->bind_param("is", $post_idgrup, $username_mhs);

            try {
                if ($stmtInsert->execute()) {
                    header("Location: group_home_mahasiswa.php");
                    exit;
                }
            } catch (mysqli_sql_exception $e) {
                if ($e->getCode() == 1062) {
                    $pesan_error = "Anda sudah bergabung di grup ini sebelumnya.";
                } else {
                    $pesan_error = "Gagal bergabung: " . $e->getMessage();
                }
            }
        }
    } else {
        $pesan_error = "Grup tidak ditemukan.";
    }
}

$nama_group = "Grup Tidak Ditemukan";
$deskripsi = "";
$jenis = "";

if (!empty($idgrup)) {
    $conn = new mysqli(SERVER_NAME, USER_NAME, PASSWORD, DB_NAME);
    $stmt = $conn->prepare("SELECT nama, deskripsi, jenis FROM grup WHERE idgrup = ?");
    $stmt->bind_param("i", $idgrup);
    $stmt->execute();
    $info = $stmt->get_result()->fetch_assoc();

    if ($info) {
        $nama_group = $info['nama'];
        $deskripsi = $info['deskripsi'];
        $jenis = $info['jenis'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gabung Grup</title>

    <link rel="stylesheet" href="css/mahasiswa_form_join_style.css">
</head>

<body>

    <div class="card">
        <h2>Konfirmasi Gabung</h2>
        <hr>

        <?php if ($pesan_error != ""): ?>
            <div class="alert-error">
                <?php echo $pesan_error; ?>
            </div>
        <?php endif; ?>

        <p>Anda akan bergabung ke dalam grup:</p>
        <h3><?php echo ($nama_group); ?></h3>
        <p class="text-deskripsi"><?php echo ($deskripsi); ?></p>

        <div class="badge-jenis">
            Jenis: <?php echo $jenis; ?>
        </div>

        <form method="POST" action="">
            <input type="hidden" name="idgrup" value="<?php echo $idgrup; ?>">

            <label class="label">Masukkan Kode Pendaftaran</label>
            <input type="text" name="kode_pendaftaran" placeholder="Ketik kode dosen di sini..." required autocomplete="off">

            <button type="submit" name="tombol_gabung" class="btn btn-biru">Gabung Sekarang</button>
            <a href="mahasiswa_search_group.php" class="btn btn-abu">Batal / Kembali</a>
        </form>
    </div>

</body>

</html>