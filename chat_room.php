<?php
session_start();
require_once("class/chat.php");

if (!isset($_SESSION['iduser'])) {
    header("Location: login.php");
    exit;
}

$idthread = $_GET['idthread'];
$chatModel = new ChatModel();
$thread = $chatModel->getThreadDetail($idthread);

if (!$thread || $thread['status'] == 'Close') {
    echo "<script>alert('Thread tidak ditemukan atau sudah ditutup.'); window.location='list_thread.php?idgrup=" . $thread['idgrup'] . "';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat: <?php echo ($thread['judul']); ?></title>

    <link rel="stylesheet" href="css/chat_room_style.css">

    <script src="jquery/jquery-3.7.1.js"></script>
</head>

<body>

    <div class="header">
        <a href="javascript:history.back()" class="back-btn">&larr;</a>
        <h3><?php echo $thread['judul']; ?></h3>
    </div>

    <div id="chatBox">
    </div>

    <div class="input-area">
        <input type="text" id="pesanInput" placeholder="Ketik pesan..." autocomplete="off">
        <button onclick="kirimPesan()">KIRIM</button>
    </div>

    <script>
        const idthread = <?php echo $idthread; ?>;
        let lastId = 0;

        $(document).ready(function() {
            ambilPesan();

            setInterval(ambilPesan, 1000);

            $('#pesanInput').keypress(function(e) {
                if (e.which == 13) kirimPesan();
            });
        });

        function ambilPesan() {
            $.ajax({
                url: 'chat_process.php',
                data: {
                    action: 'get_new_chats',
                    idthread: idthread,
                    last_id: lastId
                },
                dataType: 'json',
                success: function(chats) {
                    if (chats.length > 0) {
                        chats.forEach(chat => {
                            renderBubble(chat);
                            lastId = chat.idchat;
                        });
                        scrollToBottom();
                    }
                }
            });
        }

        function renderBubble(chat) {
            let type = chat.is_me ? 'me' : 'other';
            let nameHtml = chat.is_me ? '' : `<span class="sender-name">${chat.nama_pengirim}</span>`;

            let html = `
            <div class="bubble ${type}">
                ${nameHtml}
                ${chat.isi}
                <span class="time">${chat.waktu_format}</span>
            </div>
        `;
            $('#chatBox').append(html);
        }

        function kirimPesan() {
            let text = $('#pesanInput').val();
            if (!text.trim()) return;

            $('#pesanInput').val('');

            $.post('chat_process.php', {
                action: 'send_chat',
                idthread: idthread,
                isi: text
            }, function(res) {
                if (res.status === 'success') {
                    ambilPesan();
                } else {
                    alert("Gagal kirim: " + res.message);
                }
            }, 'json');
        }

        function scrollToBottom() {
            let box = document.getElementById("chatBox");
            box.scrollTop = box.scrollHeight;
        }
    </script>

</body>

</html>