<?php
session_start();

if (!isset($_SESSION['iduser'])) {
    header("Location: login.php");
    exit;
}

$idgrup = $_GET['idgrup'] ?? 0;
$myUsername = $_SESSION['iduser'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum Diskusi</title>

    <link rel="stylesheet" href="css/list_thread_style.css">

    <script src="jquery/jquery-3.7.1.js"></script>
</head>

<body>

    <div class="container">
        <div style="margin-bottom: 20px;">
            <a href="javascript:history.back()" class="btn btn-grey">&laquo; Kembali</a>
        </div>
        <div class="top-header">
        <h2>Topik Diskusi Grup</h2>
        <div class="notif-box">
            <div class="notif-icon">
                🔔
                <span id="notifBadge">0</span>
            </div>
        </div>
        </div>

        <div class="input-group">
            <input type="text" id="judulInput" placeholder="Tulis judul topik baru...">
            <button onclick="buatThread()" class="btn btn-blue">+ Buat Thread</button>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Judul Topik</th>
                        <th>Dibuat Oleh</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="threadTable">
                    <tr>
                        <td colspan="4" align="center">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const idgrup = <?php echo $idgrup; ?>;
        const myUser = "<?php echo $myUsername; ?>";

         $(document).ready(function() {

            loadThreads();
            loadMentionNotif();

            setInterval(function(){

                loadThreads();
                loadMentionNotif();

            }, 1000);

        });

        function loadThreads() {
            $.getJSON('chat_process.php', {
                action: 'list_threads',
                idgrup: idgrup
            }, function(data) {
                let html = '';
                if (data.length === 0) {
                    html = '<tr><td colspan="4" align="center">Belum ada topik diskusi.</td></tr>';
                } else {
                    $.each(data, function(i, row) {
                        let statusBadge = row.status === 'Open' ?
                            `<span class="badge bg-open">Open</span>` : `<span class="badge bg-close">Closed</span>`;

                        let aksi = '';

                        if (row.status === 'Open') {
                            aksi += `<a href="chat_room.php?idthread=${row.idthread}" class="btn btn-blue">Chat</a> `;
                        } else {
                            aksi += `<button class="btn btn-grey" disabled>Arsip</button> `;
                        }

                        if (row.username_pembuat === myUser && row.status === 'Open') {
                            aksi += `<button onclick="tutupThread(${row.idthread})" class="btn btn-red">Tutup</button>`;
                        }

                        html += `<tr>
                        <td><b>${row.judul}</b><br><small style="color:var(--text-color); opacity:0.7;">${row.tanggal_pembuatan}</small></td>
                        <td>${row.nama_pembuat}</td>
                        <td>${statusBadge}</td>
                        <td>${aksi}</td>
                    </tr>`;
                    });
                }
                $('#threadTable').html(html);
            });
        }

        function buatThread() {
            let judul = $('#judulInput').val();
            if (!judul.trim()) return alert("Judul harus diisi!");

            $.post('chat_process.php', {
                action: 'create_thread',
                idgrup: idgrup,
                judul: judul
            }, function(res) {
                if (res.status === 'success') {
                    $('#judulInput').val('');
                    loadThreads();
                } else {
                    alert(res.message);
                }
            }, 'json');
        }

        function tutupThread(id) {
            if (confirm("Yakin ingin menutup diskusi ini?")) {
                $.post('chat_process.php', {
                    action: 'close_thread',
                    idthread: id
                }, function(res) {
                    loadThreads();
                }, 'json');
            }
        }

        function loadMentionNotif(){

            $.getJSON('chat_process.php', {

                action: 'get_mention_count',
                idgrup: idgrup

            }, function(res){

                if(res.total > 0){

                    $('#notifBadge')
                        .text(res.total)
                        .fadeIn(200);

                }else{

                    $('#notifBadge').fadeOut(200);

                }

            });
        }
    </script>

</body>
</html>