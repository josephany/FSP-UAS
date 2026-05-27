<?php
session_start();
if (!isset($_SESSION['iduser'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Grup</title>

    <link rel="stylesheet" href="css/mahasiswa_search_group_style.css">

    <script src="jquery/jquery-3.7.1.js"></script>
</head>

<body>

    <div class="container">
        <div style="margin-bottom: 20px;">
            <a href="group_home_mahasiswa.php" class="btn btn-abu">&laquo; Kembali ke Grup Saya</a>
        </div>

        <h2>Cari & Gabung Grup</h2>
        <p>Ketik nama grup atau kode grup untuk mencari.</p>

        <input type="text" id="keyword" class="search-box" placeholder="Ketik kata kunci pencarian..." autocomplete="off" autofocus>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Nama Grup</th>
                        <th>Deskripsi</th>
                        <th>Jenis</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="listCari">
                    <tr>
                        <td colspan="4" align="center" style="color:var(--text-muted);">Silakan mulai mengetik...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const urlProses = 'group_home_mahasiswa_process.php';
        let searchTimeout = null;

        $('#keyword').on('input', function() {
            let key = $(this).val();
            clearTimeout(searchTimeout);

            if (key === '') {
                $('#listCari').html('<tr><td colspan="4" align="center" style="color:var(--text-muted);">Silakan mulai mengetik...</td></tr>');
                return;
            }

            $('#listCari').html('<tr><td colspan="4" align="center">Mencari...</td></tr>');

            searchTimeout = setTimeout(function() {
                $.ajax({
                    url: urlProses,
                    type: 'GET',
                    data: {
                        action: 'search_groups',
                        keyword: key
                    },
                    dataType: 'json',
                    success: function(data) {
                        let html = '';
                        if (data.length > 0) {
                            $.each(data, function(i, item) {
                                html += `
                                <tr>
                                    <td><b>${item.nama}</b></td>
                                    <td>${item.deskripsi}</td>
                                    <td><span class="badge">${item.jenis}</span></td>
                                    <td>
                                        <a href="mahasiswa_form_join.php?id=${item.idgrup}" class="btn btn-biru">Gabung</a>
                                    </td>
                                </tr>`;
                            });
                        } else {
                            html = '<tr><td colspan="4" align="center">Grup tidak ditemukan.</td></tr>';
                        }
                        $('#listCari').html(html);
                    }
                });
            }, 500);
        });
    </script>

</body>

</html>