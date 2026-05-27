<?php
session_start();

if (!isset($_SESSION['iduser']) || $_SESSION['isadmin'] != 1) {
    header("location: login.php");
    exit();
}

require_once("class/Dosen.php");

$cari = "";
if (isset($_GET['cari'])) {
    $cari = $_GET['cari'];
}

$limit = 5;
$page = 1;
if (isset($_GET['page'])) {
    $page = (int)$_GET['page'];
    if ($page <= 0) {
        $page = 1;
    }
}
$offset = ($page - 1) * $limit;

$dosen = new Dosen();

$totalDosen = $dosen->getTotalDosen($cari);
$totalPages = (int)(($totalDosen + $limit - 1) / $limit);

$listDosen = $dosen->getDosenWithPaging($offset, $limit, $cari);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Dosen</title>

    <link rel="stylesheet" href="css/dosen_tampilan_style.css">

    <script src="jquery/jquery-3.7.1.js"></script>
</head>

<body>

    <div class="container">
        <h1>Manajemen Data Dosen</h1>

        <div style="margin-bottom: 20px;">
            <a href="index.php" class="btn btn-secondary">&laquo; Menu Utama</a>
            <a href="doseninsert.php" class="btn btn-primary">+ Tambah Dosen</a>
        </div>

        <form action="dosen_tampilan.php" method="get" class="search-box">
            <label for="cari">Cari Nama Dosen:</label>
            <input type="text" id="cari" name="cari" value="<?php echo ($cari); ?>" placeholder="Ketik nama...">
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>NPK</th>
                        <th>Username Akun</th>
                        <th>Nama</th>
                        <th>Foto</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($listDosen->num_rows > 0) {
                        while ($row = $listDosen->fetch_assoc()) {
                    ?>
                            <tr>
                                <td><?php echo $row['npk'] ?></td>
                                <td>
                                    <?php
                                    if (isset($row['username']) && $row['username']) {
                                        echo $row['username'];
                                    } else {
                                        echo '<span style="color:var(--btn-secondary); font-style:italic;">tidak ada</span>';
                                    }
                                    ?>
                                </td>
                                <td><b><?php echo $row['nama'] ?></b></td>
                                <td>
                                    <?php
                                    if ($row['foto_extension']) {
                                        echo '<img src="image/' . $row['npk'] . '.' . $row['foto_extension'] . '" class="foto-dosen">';
                                    } else {
                                        echo 'Tidak ada foto';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="dosenedit.php?npk=<?php echo $row['npk'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="dosendelete_process.php?npk=<?php echo $row['npk'] ?>" class="btn btn-sm btn-danger tombol-hapus">Delete</a>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo '<tr><td colspan="5" align="center">Tidak ada data dosen ditemukan.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1) { ?>
            <div class="pagination-container">
                <?php
                $cari_param = "";
                if (!empty($cari)) {
                    $cari_param = "&cari=" . $cari;
                }
                if ($page > 1) {
                    echo '<a href="dosen_tampilan.php?page=1' . $cari_param . '" class="page-link">First</a>';
                } else {
                    echo '<span class="page-link disabled">First</span>';
                }

                if ($page > 1) {
                    echo '<a href="dosen_tampilan.php?page=' . ($page - 1) . $cari_param . '" class="page-link">Prev</a>';
                } else {
                    echo '<span class="page-link disabled">Prev</span>';
                }

                for ($i = 1; $i <= $totalPages; $i++) {
                    if ($i == $page) {
                        echo '<span class="page-link active">' . $i . '</span>';
                    } else {
                        echo '<a href="dosen_tampilan.php?page=' . $i . $cari_param . '" class="page-link">' . $i . '</a>';
                    }
                }

                if ($page < $totalPages) {
                    echo '<a href="dosen_tampilan.php?page=' . ($page + 1) . $cari_param . '" class="page-link">Next</a>';
                } else {
                    echo '<span class="page-link disabled">Next</span>';
                }

                if ($page < $totalPages) {
                    echo '<a href="dosen_tampilan.php?page=' . $totalPages . $cari_param . '" class="page-link">Last</a>';
                } else {
                    echo '<span class="page-link disabled">Last</span>';
                }
                ?>
            </div>
        <?php } ?>

    </div>
    <script>
        $(document).ready(function() {
            $('.tombol-hapus').on('click', function(event) {
                if (!confirm('Menghapus data dosen akan juga menghapus akun yang terkait. Apakah Anda yakin ingin menghapus data ini?')) {
                    event.preventDefault();
                }
            });
        });
    </script>
</body>

</html>