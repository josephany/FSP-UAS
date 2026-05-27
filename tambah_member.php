<?php
session_start();

if (!isset($_SESSION['iduser'])) {
    die("Akses ditolak. <a href='login.php'>Login</a>");
}

$idgrup = "";
if (isset($_GET['id'])) {
    $idgrup = $_GET['id'];
}

if ($idgrup == "") {
    die("ID Group tidak ditemukan. <a href='group_home_dosen.php'>Kembali</a>");
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Anggota - Dosen</title>

    <link rel="stylesheet" href="css/tambah_member_style.css">

    <script src="jquery/jquery-3.7.1.js"></script>
</head>

<body>

    <div class="container">
        <a href="detail_group_dosen.php?id=<?= $idgrup ?>" class="btn btn-abu">&laquo; Kembali ke Detail Group</a>

        <h2 style="margin-top: 20px;">Tambah Anggota ke: <span id="namaGroupLoading">Loading...</span></h2>
        <p>Cari mahasiswa atau dosen, lalu klik tombol <b>Tambah</b>.</p>

        <div class="search-box">
            <input type="text" id="keywordCari" placeholder="Ketik Nama, Username, atau NRP/NPK..." autofocus>
        </div>

        <div class="table-responsive">
            <table id="tabelHasil">
                <thead>
                    <tr>
                        <th>ID (NRP/NPK)</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Role</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbodyHasil">
                    <tr>
                        <td colspan="5" align="center" style="color:var(--text-muted);">Silakan ketik untuk mencari...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const idgrup = "<?php echo $idgrup ?>";
        const urlAPI = "detail_group_dosen_process.php";
        let searchTimeout = null;

        $(document).ready(function() {
            loadInfoGroup();
            cariUser();

            $('#keywordCari').on('input', function() {
                clearTimeout(searchTimeout);
                $('#tbodyHasil').html('<tr><td colspan="5" align="center" style="color:var(--text-muted);">Sedang mencari...</td></tr>');

                searchTimeout = setTimeout(function() {
                    cariUser();
                }, 500);
            });
        });

        function loadInfoGroup() {
            $.ajax({
                url: urlAPI,
                type: 'GET',
                data: {
                    action: 'get_group_info',
                    idgrup: idgrup
                },
                dataType: 'json',
                success: function(data) {
                    if (data.nama) {
                        $('#namaGroupLoading').text(data.nama);
                    }
                }
            });
        }

        function cariUser() {
            let key = $('#keywordCari').val();

            $.ajax({
                url: urlAPI,
                type: 'GET',
                data: {
                    action: 'cari_mahasiswa',
                    idgrup: idgrup,
                    keyword: key
                },
                dataType: 'json',
                success: function(data) {
                    let html = '';
                    if (data.length > 0) {
                        $.each(data, function(i, item) {
                            html += `
                                <tr>
                                    <td>${item.id_nomor_induk}</td>
                                    <td>${item.username}</td>
                                    <td><b>${item.nama}</b></td>
                                    <td>${item.role}</td>
                                    <td>
                                        <button class="btn btn-hijau" onclick="addMember(this, '${item.username}', '${item.nama}')">Tambah</button>
                                    </td>
                                </tr>`;
                        });
                    } else {
                        html = '<tr><td colspan="5" align="center" style="color:red;">Tidak ditemukan / Semua sudah bergabung.</td></tr>';
                    }
                    $('#tbodyHasil').html(html);
                }
            });
        }

        function addMember(btnElement, username, nama) {
            $(btnElement).prop('disabled', true).text('Menambahkan...');

            $.ajax({
                url: urlAPI,
                type: 'POST',
                data: {
                    action: 'tambah_member',
                    idgrup: idgrup,
                    username: username
                },
                dataType: 'json',
                success: function(res) {
                    alert(res.pesan);
                    if (res.status == 'success') {
                        cariUser();
                    } else {
                        $(btnElement).prop('disabled', false).text('Tambah');
                    }
                }
            });
        }
    </script>
</body>

</html>