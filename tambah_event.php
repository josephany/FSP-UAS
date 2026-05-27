<?php
session_start();

if (!isset($_SESSION['iduser'])) {
    die("Akses ditolak. <a href='login.php'>Login</a>");
}

$idgrup = isset($_GET['idgrup']) ? $_GET['idgrup'] : '';
$idevent = isset($_GET['idevent']) ? $_GET['idevent'] : '';

if ($idgrup == "") {
    die("ID Group tidak ditemukan.");
}

$judulHalaman = ($idevent == "") ? "Tambah Event Baru" : "Edit Event";
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $judulHalaman ?> - Dosen</title>

    <link rel="stylesheet" href="css/tambah_event_style.css">

    <script src="jquery/jquery-3.7.1.js"></script>
</head>

<body>

    <div class="container">
        <h2><?= $judulHalaman ?></h2>

        <form id="formEvent" enctype="multipart/form-data">
            <input type="hidden" name="action" value="simpan_event">
            <input type="hidden" name="idgrup" value="<?= $idgrup ?>">
            <input type="hidden" name="idevent" id="idevent" value="<?= $idevent ?>">

            <label>Judul Event</label>
            <input type="text" name="judul" id="judul" required placeholder="Contoh: Ujian Akhir Semester">

            <label>Tanggal & Waktu</label>
            <input type="datetime-local" name="tanggal" id="tanggal" required>

            <label>Jenis Event</label>
            <select name="jenis" id="jenis">
                <option value="Publik">Publik</option>
                <option value="Privat">Privat</option>
            </select>

            <label>Poster (Opsional)</label>
            <input type="file" name="poster" id="poster" accept=".jpg, .jpeg, .png">

            <div id="previewContainer"></div>

            <small>Format: JPG, PNG. Biarkan kosong jika tidak ingin mengubah gambar.</small>

            <label>Keterangan</label>
            <textarea name="keterangan" id="keterangan" rows="4" placeholder="Detail informasi event..."></textarea>

            <div class="btn-action">
                <a href="detail_group_dosen.php?id=<?= $idgrup ?>" class="btn btn-abu">Batal</a>
                <button type="submit" class="btn btn-biru">Simpan</button>
            </div>
        </form>
    </div>

    <script>
        const urlAPI = "detail_group_dosen_process.php";
        const idevent = "<?= $idevent ?>";

        $(document).ready(function() {
            if (idevent !== "") {
                loadDataEdit();
            }

            $('#formEvent').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);

                $('button[type="submit"]').text('Menyimpan...').prop('disabled', true);

                $.ajax({
                    url: urlAPI,
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        alert(res.pesan);
                        if (res.status == 'success') {
                            window.location.href = "detail_group_dosen.php?id=<?= $idgrup ?>";
                        } else {
                            $('button[type="submit"]').text('Simpan').prop('disabled', false);
                        }
                    },
                    error: function() {
                        alert('Gagal menghubungi server.');
                        $('button[type="submit"]').text('Simpan').prop('disabled', false);
                    }
                });
            });
        });

        function loadDataEdit() {
            $.ajax({
                url: urlAPI,
                type: 'GET',
                data: {
                    action: 'get_event_detail',
                    idevent: idevent
                },
                dataType: 'json',
                success: function(data) {
                    if (data) {
                        $('#judul').val(data.judul);

                        let tglHTML = data.tanggal.replace(' ', 'T');
                        $('#tanggal').val(tglHTML);

                        $('#jenis').val(data.jenis);
                        $('#keterangan').val(data.keterangan);

                        if (data.poster_extension) {
                            let imgPath = `image/event/${data.idevent}.${data.poster_extension}`;
                            let timestamp = new Date().getTime();
                            $('#previewContainer').html(`<img src="${imgPath}?t=${timestamp}" class="preview-img" style="display:block;">`);
                        }
                    }
                }
            });
        }
    </script>
</body>

</html>