<?php
session_start();
if (!isset($_SESSION['iduser'])) {
    header("Location: login.php");
    exit;
}
$username_mhs = $_SESSION['iduser'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Mahasiswa</title>

    <link rel="stylesheet" href="css/group_home_mahasiswa_style.css">

    <script src="jquery/jquery-3.7.1.js"></script>
</head>

<body>

    <div class="container">
        <div style="margin-bottom: 15px;">
            <a href="index.php" class="btn btn-abu">&laquo; Back</a>
        </div>

        <h2>Grup Saya (<?php echo $username_mhs; ?>)</h2>

        <a href="mahasiswa_search_group.php" class="btn btn-biru btn-large">+ Cari & Gabung Grup Baru</a>

        <div class="tampilan-laptop">
            <h3>Daftar Grup yang Diikuti</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nama Grup</th>
                        <th>Deskripsi</th>
                        <th>Dosen Pembuat</th>
                        <th style="width: 250px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="listGrupSaya">
                    <tr>
                        <td colspan="4" align="center">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        const urlProses = 'group_home_mahasiswa_process.php';

        $(document).ready(function() {
            loadGrupSaya();
        });

        function loadGrupSaya() {
            $.ajax({
                url: urlProses,
                type: 'GET',
                data: {
                    action: 'list_my_groups'
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
                                    <td>${item.username_pembuat}</td>
                                    <td>
                                        <a href="detail_group_mahasiswa.php?id=${item.idgrup}" class="btn btn-hijau">Detail</a>
                                        
                                        <a href="list_thread.php?idgrup=${item.idgrup}" class="btn btn-biru">Forum</a>
                                        
                                        <button class="btn btn-merah" onclick="keluarGrup('${item.idgrup}')">Keluar</button>
                                    </td>
                                </tr>`;
                        });
                    } else {
                        html = '<tr><td colspan="4" align="center">Anda belum mengikuti grup apapun.</td></tr>';
                    }
                    $('#listGrupSaya').html(html);
                }
            });
        }

        function keluarGrup(id) {
            if (confirm('Yakin ingin keluar dari grup ini?')) {
                $.ajax({
                    url: urlProses,
                    type: 'POST',
                    data: {
                        action: 'leave_group',
                        idgrup: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        alert(response.pesan);
                        if (response.status == 'success') loadGrupSaya();
                    }
                });
            }
        }
    </script>
</body>

</html>