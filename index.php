<?php
session_start();

$user_role = '';
if (isset($_SESSION['role'])) {
    $user_role = $_SESSION['role'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Grouping System</title>

    <link rel="stylesheet" href="css/index_style.css">
</head>

<body>

    <div class="container fade-in">
        <h1>Selamat Datang di<br>Aplikasi Grouping System</h1>

        <?php
        if (isset($_SESSION['iduser'])) {
            echo '<div class="user-info">';
            echo '<p>Anda login sebagai: <strong>' . ($_SESSION['iduser']) . '</strong></p>';
            echo '<div class="user-actions">';
            echo '<a href="change_password.php">Ganti Password</a>';
            echo '<a href="logout.php" class="logout-link">Logout</a>';
            echo '</div>';
            echo '</div>';

            echo '<hr>';

            if (isset($_SESSION['isadmin']) && $_SESSION['isadmin'] == 1) {
                echo '<h2>Menu Admin</h2>';
                echo '<p>Silakan memilih data yang ingin dikelola:</p>';
                echo '<ul class="menu-list">';

                echo '<li class="menu-item">';
                echo '<a href="dosen_tampilan.php" class="btn-menu btn-primary">Kelola Data Dosen</a>';
                echo '</li>';

                echo '<li class="menu-item">';
                echo '<a href="mahasiswa_tampilan.php" class="btn-menu btn-primary">Kelola Data Mahasiswa</a>';
                echo '</li>';

                echo '</ul>';
            } elseif ($user_role == 'mahasiswa') {
                echo '<h2>Menu Mahasiswa</h2>';
                echo '<p>Kelola grup yang Anda ikuti:</p>';
                echo '<ul class="menu-list">';

                echo '<li class="menu-item">';
                echo '<a href="group_home_mahasiswa.php" class="btn-menu btn-success">Group Saya</a>';
                echo '</li>';

                echo '</ul>';
            } else {
                echo '<h2>Menu Dosen</h2>';
                echo '<p>Kelola grup yang Anda buat:</p>';
                echo '<ul class="menu-list">';

                echo '<li class="menu-item">';
                echo '<a href="group_home_dosen.php" class="btn-menu btn-primary">Kelola Group Saya</a>';
                echo '</li>';

                echo '</ul>';
            }
        } else {
            echo '<p>Anda belum login.</p>';
            echo '<ul class="menu-list">';
            echo '<li class="menu-item">';
            echo '<a href="login.php" class="btn-menu btn-primary">Login ke Aplikasi</a>';
            echo '</li>';
            echo '</ul>';
        }
        ?>
    </div>

</body>

</html>