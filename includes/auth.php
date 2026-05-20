<?php
// includes/auth.php
// Panggil file ini di setiap halaman yang butuh login
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}
