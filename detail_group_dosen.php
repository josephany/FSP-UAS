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

$loggedInUser = $_SESSION['iduser'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Group - Dosen</title>

    <link rel="stylesheet" href="css/detail_group_dosen_style.css">

    <script src="jquery/jquery-3.7.1.js"></script>
</head>

<body>

    <a href="group_home_dosen.php" class="btn btn-abu">&laquo; Kembali ke Daftar Group</a>
    <br><br>

    <div class="header-box">
        <h2 id="judulHeader">Loading...</h2>
        <p id="deskripsiHeader">...</p>
        <div style="margin-top: 10px;">
            Kode Pendaftaran: <span id="kodeHeader" class="kode-badge">...</span>
        </div>
    </div>

    <div class="tab-nav">
        <button class="tab-btn active" onclick="bukaTab('tabEvent', this)">Kelola Event</button>
        <button class="tab-btn" onclick="bukaTab('tabMember', this)">Kelola Member</button>
    </div>

    <div id="tabEvent" class="tab-content active">
        <a href="tambah_event.php?idgrup=<?= $idgrup ?>" class="btn btn-biru">+ Tambah Event Baru</a>
        <br><br>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 150px;">Poster</th>
                        <th>Tanggal</th>
                        <th>Judul</th>
                        <th>Jenis</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="listEvent">
                    <tr>
                        <td colspan="6" align="center">Memuat data event...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="tabMember" class="tab-content">
        <a href="tambah_member.php?id=<?= $idgrup ?>" class="btn btn-hijau">+ Tambah Member Baru</a>
        <br><br>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID User (NRP/NPK)</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Role</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="listMember">
                    <tr>
                        <td colspan="5" align="center">Memuat data member...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const idgrup = "<?php echo $idgrup; ?>";
        const urlAPI = "detail_group_dosen_process.php";
        const currentUser = "<?php echo $loggedInUser; ?>";

        $(document).ready(function() {
            loadInfoGroup();
            loadEvents();
            loadMembers();
        });

        function bukaTab(idTab, elem) {
            $('.tab-content').removeClass('active');
            $('.tab-btn').removeClass('active');
            $('#' + idTab).addClass('active');
            $(elem).addClass('active');
        }

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
                        $('#judulHeader').text(data.nama);
                        $('#deskripsiHeader').text(data.deskripsi);
                        $('#kodeHeader').text(data.kode_pendaftaran);
                    } else {
                        $('#judulHeader').text("Data Group Tidak Ditemukan");
                    }
                }
            });
        }

        function loadEvents() {
            $.ajax({
                url: urlAPI,
                type: 'GET',
                data: {
                    action: 'list_events',
                    idgrup: idgrup
                },
                dataType: 'json',
                success: function(data) {
                    let html = '';
                    if (data.length > 0) {
                        $.each(data, function(i, item) {
                            let posterImg = '<span style="color:var(--text-secondary); font-size:12px;">No Image</span>';
                            if (item.poster_extension && item.poster_extension !== '') {
                                let imgPath = `image/event/${item.idevent}.${item.poster_extension}`;
                                posterImg = `<img src="${imgPath}?t=${new Date().getTime()}" class="poster-thumb">`;
                            }

                            let linkEdit = `tambah_event.php?idgrup=${idgrup}&idevent=${item.idevent}`;

                            html += `
                                <tr>
                                    <td align="center">${posterImg}</td>
                                    <td>${item.tanggal}</td>
                                    <td><b>${item.judul}</b></td>
                                    <td>${item.jenis}</td>
                                    <td>${item.keterangan}</td>
                                    <td>
                                        <a href="${linkEdit}" class="btn btn-sm btn-biru">Edit</a>
                                        <button class="btn btn-sm btn-merah" onclick="hapusEvent('${item.idevent}')">Hapus</button>
                                    </td>
                                </tr>`;
                        });
                    } else {
                        html = '<tr><td colspan="6" align="center">Belum ada event.</td></tr>';
                    }
                    $('#listEvent').html(html);
                }
            });
        }

        function hapusEvent(id) {
            if (confirm('Yakin hapus event ini?')) {
                $.ajax({
                    url: urlAPI,
                    type: 'POST',
                    data: {
                        action: 'hapus_event',
                        idevent: id,
                        idgrup: idgrup
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status == 'success') loadEvents();
                        else alert(res.pesan);
                    }
                });
            }
        }

        function loadMembers() {
            $.ajax({
                url: urlAPI,
                type: 'GET',
                data: {
                    action: 'list_members',
                    idgrup: idgrup
                },
                dataType: 'json',
                success: function(data) {
                    let html = '';
                    if (data.length > 0) {
                        $.each(data, function(i, item) {
                            let labelTombol = "Keluarkan";
                            let classTombol = "btn-merah";
                            let confirmMsg = "Keluarkan user ini dari grup?";

                            if (item.role == "Dosen" && item.username == currentUser) {
                                html += `
                                <tr>
                                <td>${item.id_nomor_induk}</td>
                                <td>${item.username}</td>
                                <td><b>${item.nama}</b></td>
                                <td>${item.role}</td>
                                <td> - </td>
                                </tr>`;
                                return;
                            }

                            html += `
                                <tr>
                                    <td>${item.id_nomor_induk}</td>
                                    <td>${item.username}</td>
                                    <td><b>${item.nama}</b></td>
                                    <td>${item.role}</td>
                                    <td>
                                        <button class="btn btn-sm ${classTombol}" onclick="kickMember('${item.username}', '${confirmMsg}')">${labelTombol}</button>
                                    </td>
                                </tr>`;
                        });
                    } else {
                        html = '<tr><td colspan="5" align="center">Belum ada anggota.</td></tr>';
                    }
                    $('#listMember').html(html);
                }
            });
        }

        function kickMember(username, pesanKonfirmasi) {
            if (confirm(pesanKonfirmasi)) {
                $.ajax({
                    url: urlAPI,
                    type: 'POST',
                    data: {
                        action: 'hapus_member',
                        idgrup: idgrup,
                        username: username
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status == 'success') {
                            if (username == currentUser) {
                                $('body').html(`
                                    <div style="font-family: 'Segoe UI', sans-serif; text-align:center; margin-top: 100px; color: var(--text-color);">
                                        <h2>Anda telah keluar dari grup ini.</h2>
                                        <br>
                                        <a href="group_home_dosen.php" class="btn btn-biru">Kembali ke Daftar Group</a>
                                    </div>
                                `);
                            } else {
                                loadMembers();
                            }
                        } else alert(res.pesan);
                    }
                });
            }
        }
    </script>

</body>

</html>