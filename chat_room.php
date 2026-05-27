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
$chatModel->readMention(
    $idthread,
    $_SESSION['iduser']
);

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
    <label for="fileInput" class="plus-btn">+</label>
    <input type="file" id="fileInput" hidden>
    <div class="mention-wrapper">
        <input type="text"
               id="pesanInput"
               placeholder="Ketik pesan..."
               autocomplete="off">
        <div id="mentionList"></div>
    </div>

    <button onclick="kirimPesan()">KIRIM</button>

</div>

    <script>
        const idthread = <?php echo $idthread; ?>;
        const currentUser = '<?php echo $_SESSION['iduser']; ?>';
        let lastId = 0;
        let members = [];

        $(document).ready(function() {

            loadMembers();
            ambilPesan();
            setInterval(ambilPesan, 1000);

            $('#pesanInput').keypress(function(e) {
                if (e.which == 13) kirimPesan();
            });


            $('#pesanInput').on('keyup', function () {

            let text = $(this).val();
            let match = text.match(/(?:^|\s)@([a-zA-Z0-9_]*)$/);
            if (match) {

                let keyword = match[1].toLowerCase();
                let filtered = members.filter(member =>

                    member.username !== currentUser && (
                        member.username.toLowerCase().includes(keyword) ||
                        member.nama.toLowerCase().includes(keyword)
                    )
                );

                showMentionList(filtered);

            } else {

                $('#mentionList').hide();

            }

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
            let isiHtml = '';

                if(chat.tipe_chat == 'image'){

                    isiHtml = `
                        <img src="${chat.file_path}"
                             class="chat-image">
                    `;

                }else if(chat.tipe_chat == 'file'){

                    isiHtml = `
                        <a href="${chat.file_path}"
                           target="_blank">

                           📄 Download File

                        </a>
                    `;
                }else{

                    isiHtml = formatMention(chat.isi);

                }


            let html = `
            <div class="bubble ${type}">
                ${nameHtml}
                ${isiHtml}
                <span class="time">${chat.waktu_format}</span>
            </div>
        `;
            $('#chatBox').append(html);
        }


        function kirimPesan() {

            let text = $('#pesanInput').val();
            let file = $('#fileInput')[0].files[0];
            if (!text.trim() && !file) return;

            let formData = new FormData();

            formData.append('action', 'send_chat');
            formData.append('idthread', idthread);
            formData.append('isi', text);

            if (file) {
                formData.append('file', file);
            }

            $('#pesanInput').val('');
            $('#fileInput').val('');

            $('#pesanInput').attr(
                'placeholder',
                'Ketik pesan...'
            );

            $.ajax({
                url: 'chat_process.php',
                type: 'POST',
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,

                success: function(res) {

                    if (res.status === 'success') {
                        ambilPesan();
                    } else {
                        alert(res.message);
                    }
                }
            });

        }

        function scrollToBottom() {
            let box = document.getElementById("chatBox");
            box.scrollTop = box.scrollHeight;
        }

        //baru
        function loadMembers() {

            $.ajax({
                url: 'chat_process.php',
                data: {
                    action: 'get_members',
                    idthread: idthread
                },
                dataType: 'json',

                success: function(res) {

                    console.log(res);
                    members = res.members;

                }
            });
        }


    function showMentionList(list) {

        if(list.length == 0){
            $('#mentionList').hide();
            return;
        }

        let html = '';

        list.forEach(member => {

            html += `
                <div class="mention-item"
                     onclick="selectMention('${member.username}')">

                    ${member.nama}

                </div>
            `;

        });

        $('#mentionList').html(html).show();
    }

    function selectMention(username) {

        let text = $('#pesanInput').val();

        text = text.replace(/@\w*$/, '@' + username + ' ');

        $('#pesanInput').val(text);
        $('#mentionList').hide();
        $('#pesanInput').focus();
    }

    function formatMention(text) {

        text = $('<div>').text(text).html();
        return text.replace(
            /@(\w+)/g,
            '<span class="mention-tag">@$1</span>'
        );

    }

    $('#fileInput').change(function(){

        let file = this.files[0];
        if(file){

            $('#pesanInput').attr(
                'placeholder',
                'File dipilih: ' + file.name
            );

        }

    });
    </script>

</body>
</html>