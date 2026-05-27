<?php
session_start();

if (!isset($_SESSION['iduser'])) {
    die("Akses ditolak. <a href='login.php'>Login</a>");
}

$idgrup = "";
if (isset($_GET['id'])) {
    $idgrup = $_GET['id'];
}

$back = "group_home_mahasiswa.php";

if (isset($_GET['mode']) && $_GET['mode'] === "dosen") {
    $back = "group_home_dosen.php";
}

if ($idgrup == "") {
    die("ID Group tidak ditemukan. <a href='group_home_mahasiswa.php'>Kembali</a>");
}

$loggedInUser = $_SESSION['iduser'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Group - Mahasiswa</title>

    <link rel="stylesheet" href="css/detail_group_mahasiswa_style.css">

    <script src="jquery/jquery-3.7.1.js"></script>
</head>

<body>

    <a href="<?php echo $back; ?>" class="btn btn-abu">« Back</a>

    <br><br>

    <div class="header-box">
        <h2 id="judulHeader">Loading...</h2>
        <p id="deskripsiHeader">...</p>
        <div style="margin-top: 10px;">
            Kode Pendaftaran: <span id="kodeHeader" class="kode-badge">...</span>
        </div>
    </div>

    <div class="tab-nav">
        <button class="tab-btn active" onclick="bukaTab('tabEvent', this)">Daftar Event</button>
        <button class="tab-btn" onclick="bukaTab('tabMember', this)">Anggota Grup</button>
    </div>

    <div id="tabEvent" class="tab-content active">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 150px;">Poster</th>
                        <th>Tanggal</th>
                        <th>Judul</th>
                        <th>Jenis</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody id="listEvent">
                    <tr>
                        <td colspan="5" align="center">Memuat data event...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="tabMember" class="tab-content">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID (NRP/NPK)</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Role</th>
                        <th style="width: 100px;">Aksi</th>
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
        const urlAPI = "detail_group_mahasiswa_process.php";
        const currentUser = "<?php echo $loggedInUser; ?>";

        const backPage = "<?php echo $back; ?>";
        const mode = "<?php echo isset($_GET['mode']) ? $_GET['mode'] : ''; ?>";

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
                        $('#judulHeader').text("Grup Tidak Ditemukan");
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
                            let badgeClass = 'bg-publik';
                            if (item.jenis == 'Privat') badgeClass = 'bg-privat';

                            let posterImg = '<span style="color:var(--text-secondary); font-size:12px;">No Image</span>';
                            if (item.poster_extension && item.poster_extension !== '') {
                                let imgPath = `image/event/${item.idevent}.${item.poster_extension}`;
                                posterImg = `<img src="${imgPath}" class="poster-thumb">`;
                            }

                            html += `
                                <tr>
                                    <td align="center">${posterImg}</td>
                                    <td>${item.tanggal}</td>
                                    <td><b>${item.judul}</b></td>
                                    <td><span class="label-event ${badgeClass}">${item.jenis}</span></td>
                                    <td>${item.keterangan}</td>
                                </tr>`;
                        });
                    } else {
                        html = '<tr><td colspan="5" align="center">Belum ada event di grup ini.</td></tr>';
                    }
                    $('#listEvent').html(html);
                }
            });
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

                            let tombolAksi = "-";

                            if (item.username == currentUser) {
                                tombolAksi = `<button class="btn btn-orange" onclick="leaveGroup()">Keluar</button>`;
                            }

                            html += `
                                <tr>
                                    <td>${item.id_nomor_induk}</td>
                                    <td>${item.username}</td>
                                    <td><b>${item.nama}</b></td>
                                    <td>${item.role}</td>
                                    <td align="center">${tombolAksi}</td>
                                </tr>`;
                        });
                    } else {
                        html = '<tr><td colspan="5" align="center">Belum ada anggota.</td></tr>';
                    }
                    $('#listMember').html(html);
                }
            });
        }

        function leaveGroup() {
            if (confirm('Apakah Anda yakin ingin keluar dari grup ini?')) {
                $.ajax({
                    url: urlAPI,
                    type: 'POST',
                    data: {
                        action: 'leave_group',
                        idgrup: idgrup,
                        mode: mode
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status == 'success') {
                            $('body').html(`
                                <div style="font-family: 'Segoe UI', sans-serif; text-align:center; margin-top: 100px; color: var(--text-color);">
                                    <h2>Anda telah keluar dari grup ini.</h2>
                                    <p style="color: var(--text-secondary);">Akses Anda ke halaman ini telah dicabut.</p>
                                    <br>
                                    <a href="` + backPage + `" class="btn btn-biru">Kembali ke Daftar Group</a>
                                </div>
                            `);
                        } else {
                            alert(res.pesan);
                        }
                    },
                    error: function() {
                        alert('Gagal menghubungi server.');
                    }
                });
            }
        }
    </script>

</body>

</html>