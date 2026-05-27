<?php
session_start();
require_once("class/Users.php");
$users = new Users();

$iduser = $_POST['iduser'];
$plain_pwd = $_POST['password'];

$login_result = $users->doLogin($iduser, $plain_pwd);

if ($login_result == false) {
    header("location:login.php?err=ERROR");
} else {
    $_SESSION['iduser'] = $iduser;

    $_SESSION['isadmin'] = $login_result['isadmin'];

    header("location:index.php");
}
