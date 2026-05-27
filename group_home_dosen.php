<?php
session_start();
if (!isset($_SESSION['iduser'])) {
    header("Location: login.php");
    exit;
}
$username_dosen = $_SESSION['iduser'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Group Dosen</title>

    <link rel="stylesheet" href="css/group_home_dosen_style.css">

    <script src="jquery/jquery-3.7.1.js"></script>
</head>

<body>

    <div class="container">
        <div style="margin-bottom: 20px;">
            <a href="index.php" class="btn btn-abu">&laquo; Back</a>
        </div>

        <h2>Kelola Group (Dosen: <?php echo $username_dosen; ?>)</h2>

        <a href="tambah_edit_group.php" class="btn btn-biru">+ Tambah Group Baru</a>
        <br><br>

        <div class="tampilan-laptop">
            <table>
                <thead>
                    <tr>
                        <th>Nama Group</th>
                        <th>Deskripsi</th>
                        <th>Jenis</th>
                        <th style="width: 250px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="isiTabel">
                    <tr>
                        <td colspan="4" align="center">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        const urlProses = 'group_home_dosen_process.php';
        const currentUsername = "<?php echo $username_dosen; ?>";

        $(document).ready(function() {
            loadGroups();
        });

        function loadGroups() {
            $.ajax({
                url: urlProses,
                type: 'GET',
                data: {
                    action: 'list'
                },
                dataType: 'json',
                success: function(data) {
                    let html = '';
                    if (data.length > 0) {
                        $.each(data, function(index, item) {
                            let isOwner = (item.username_pembuat == currentUsername);

                            let detailLink = isOwner ?
                                `detail_group_dosen.php?id=${item.idgrup}` :
                                `detail_group_mahasiswa.php?id=${item.idgrup}&mode=dosen`;


                            html += `
                            <tr>
                            <td><b>${item.nama}</b></td>
                            <td>${item.deskripsi}</td>
                            <td>${item.jenis}</td>
                            <td>
                                <a href="${detailLink}" class="btn btn-hijau">Detail</a>
                                
                                <a href="list_thread.php?idgrup=${item.idgrup}" class="btn btn-ungu">Forum</a>
                            `;

                            if (isOwner) {
                                html += `
                                <a href="tambah_edit_group.php?id=${item.idgrup}" class="btn btn-biru">Edit</a>
                                <button class="btn btn-merah" onclick="hapusGroup('${item.idgrup}')">Hapus</button>
                                `;
                            } else {
                                html += `
                                <button class="btn btn-merah" onclick="keluarGroup('${item.idgrup}')">Keluar</button>
                                `;
                            }

                            html += `</td></tr>`;
                        });
                    } else {
                        html = '<tr><td colspan="4" align="center">Belum ada group.</td></tr>';
                    }
                    $('#isiTabel').html(html);
                },
                error: function() {
                    $('#isiTabel').html('<tr><td colspan="4" align="center">Gagal memuat data.</td></tr>');
                }
            });
        }

        function hapusGroup(id) {
            if (confirm('Yakin menghapus group ini?')) {
                $.ajax({
                    url: urlProses,
                    type: 'POST',
                    data: {
                        action: 'hapus',
                        idgrup: id
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status == 'success') loadGroups();
                        else alert(res.pesan);
                    }
                });
            }
        }

        function keluarGroup(idgrup) {
            if (!confirm('Anda yakin ingin keluar dari grup ini?')) return;

            $.ajax({
                url: 'detail_group_dosen_process.php',
                type: 'POST',
                data: {
                    action: 'hapus_member',
                    idgrup: idgrup,
                    username: currentUsername
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status == 'success') {
                        alert('Anda telah keluar dari grup.');
                        loadGroups();
                    } else {
                        alert(res.pesan || 'Gagal keluar grup.');
                    }
                },
                error: function() {
                    alert('Gagal menghubungi server.');
                }
            });
        }
    </script>
</body>

</html>