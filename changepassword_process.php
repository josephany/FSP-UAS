<?php
session_start();

if (!isset($_SESSION['iduser'])) {
    header("location: login.php");
    exit();
}

require_once("class/Users.php");
$users = new Users();

$username = $_SESSION['iduser'];

$old_pwd = $_POST['oldPassword'];
$new_pwd = $_POST['newPassword'];
$confirm_pwd = $_POST['confirmPassword'];

$len_pass = 0;
while (isset($new_pwd[$len_pass])) {
    $len_pass++;
}

if ($len_pass > 100) {
    $_SESSION['password_status'] = 'PWD_TERLALU_PANJANG';
    header("location: change_password.php");
    exit();
}

if (!$users->verifyPassword($username, $old_pwd)) {
    $_SESSION['password_status'] = 'OLD_PWD_SALAH';
    header("location: change_password.php");
    exit();
}

if ($new_pwd != $confirm_pwd) {
    $_SESSION['password_status'] = 'PWD_TIDAKCOCOK';
    header("location: change_password.php");
    exit();
}

if ($users->verifyPassword($username, $new_pwd)) {
    $_SESSION['password_status'] = 'PWD_SAMA_DENGAN_YANG_LAMA';
    header("location: change_password.php");
    exit();
}

if ($users->changePassword($username, $new_pwd)) {
    $_SESSION['password_status'] = 'SUCCESS';
} else {
    $_SESSION['password_status'] = 'FAILED';
}

header("location: change_password.php");
exit();
