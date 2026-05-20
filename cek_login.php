<?php
// cek_login.php
session_start();
require_once 'includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = md5($_POST['password']); // MD5 sesuai data awal di database

$sql = "SELECT * FROM user WHERE username='$username' AND password='$password'";
$result = mysqli_query($koneksi, $sql);

if (mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);
    $_SESSION['id_user']  = $user['id_user'];
    $_SESSION['nama']     = $user['nama'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role']     = $user['role'];

    if ($user['role'] == 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }
} else {
    header("Location: login.php?error=1");
}
exit;
