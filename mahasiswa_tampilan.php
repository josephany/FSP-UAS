<?php

session_start();

if (!isset($_SESSION['iduser']) || $_SESSION['isadmin'] != 1) {
    header("location: login.php");
    exit();
}

require_once("class/Mahasiswa.php");

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

$mahasiswa = new Mahasiswa();
$totalMahasiswa = $mahasiswa->getTotalMahasiswa($cari);
$totalPages = (int)(($totalMahasiswa + $limit - 1) / $limit);

$listMahasiswa = $mahasiswa->getMahasiswaWithPaging($offset, $limit, $cari);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>

    <link rel="stylesheet" href="css/mahasiswa_tampilan_style.css">

    <script src="jquery/jquery-3.7.1.js"></script>
</head>

<body>

    <div class="container">
        <h1>Manajemen Data Mahasiswa</h1>

        <div style="margin-bottom: 20px;">
            <a href="index.php" class="btn btn-secondary">&laquo; Menu Utama</a>
            <a href="mahasiswainsert.php" class="btn btn-primary">+ Tambah Mahasiswa</a>
        </div>

        <form action="mahasiswa_tampilan.php" method="get" class="search-box">
            <label for="cari">Cari Nama:</label>
            <input type="text" id="cari" name="cari" value="<?php echo ($cari); ?>" placeholder="Ketik nama...">
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>NRP</th>
                        <th>Username Akun</th>
                        <th>Nama</th>
                        <th>Gender</th>
                        <th>Tgl Lahir</th>
                        <th>Angkatan</th>
                        <th>Foto</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($listMahasiswa->num_rows > 0) {
                        while ($row = $listMahasiswa->fetch_assoc()) {
                    ?>
                            <tr>
                                <td><?php echo $row['nrp'] ?></td>
                                <td>
                                    <?php
                                    if (isset($row['username']) && $row['username']) {
                                        echo $row['username'];
                                    } else {
                                        echo '<span style="color:var(--btn-secondary); font-style:italic;">(kosong)</span>';
                                    }
                                    ?>
                                </td>
                                <td><b><?php echo $row['nama'] ?></b></td>
                                <td>
                                    <?php echo ($row['gender'] == 'Pria') ? 'Pria' : 'Wanita'; ?>
                                </td>
                                <td><?php echo $row['tanggal_lahir'] ?></td>
                                <td><?php echo $row['angkatan'] ?></td>
                                <td>
                                    <?php
                                    if ($row['foto_extention']) {
                                        echo '<img src="image/' . $row['nrp'] . '.' . $row['foto_extention'] . '" class="foto-mhs">';
                                    } else {
                                        echo 'No Pic';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="mahasiswaedit.php?nrp=<?php echo $row['nrp'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="mahasiswadelete_process.php?nrp=<?php echo $row['nrp'] ?>" class="btn btn-sm btn-danger tombolhapus">Delete</a>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo '<tr><td colspan="8" align="center">Tidak ada data mahasiswa.</td></tr>';
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
                    echo '<a href="mahasiswa_tampilan.php?page=1' . $cari_param . '" class="page-link">First</a>';
                } else {
                    echo '<span class="page-link disabled">First</span>';
                }

                if ($page > 1) {
                    echo '<a href="mahasiswa_tampilan.php?page=' . ($page - 1) . $cari_param . '" class="page-link">Prev</a>';
                } else {
                    echo '<span class="page-link disabled">Prev</span>';
                }

                for ($i = 1; $i <= $totalPages; $i++) {
                    if ($i == $page) {
                        echo '<span class="page-link active">' . $i . '</span>';
                    } else {
                        echo '<a href="mahasiswa_tampilan.php?page=' . $i . $cari_param . '" class="page-link">' . $i . '</a>';
                    }
                }

                if ($page < $totalPages) {
                    echo '<a href="mahasiswa_tampilan.php?page=' . ($page + 1) . $cari_param . '" class="page-link">Next</a>';
                } else {
                    echo '<span class="page-link disabled">Next</span>';
                }

                if ($page < $totalPages) {
                    echo '<a href="mahasiswa_tampilan.php?page=' . $totalPages . $cari_param . '" class="page-link">Last</a>';
                } else {
                    echo '<span class="page-link disabled">Last</span>';
                }
                ?>
            </div>
        <?php } ?>

    </div>
    <script>
        $(document).ready(function() {
            $('.tombolhapus').on('click', function(event) {
                if (!confirm('Menghapus data mahasiswa akan juga menghapus akun yang terkait. Apakah Anda yakin ingin menghapus data ini?')) {
                    event.preventDefault();
                }
            });
        });
    </script>
</body>

</html>