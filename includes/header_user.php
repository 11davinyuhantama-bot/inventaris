<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'peminjam') {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User - Inventaris Sekolah</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: Arial, sans-serif; background: #f0f4f8; }
  nav { background: #27ae60; padding: 0 20px; display: flex; align-items: center; height: 55px; }
  nav a { color: #fff; text-decoration: none; padding: 18px 14px; font-size: 14px; display: inline-block; }
  nav a:hover { background: #219a52; }
  nav .brand { font-weight: bold; font-size: 16px; margin-right: 20px; }
  nav .right { margin-left: auto; }
  .container { max-width: 950px; margin: 30px auto; padding: 0 15px; }
  .card { background: #fff; border-radius: 8px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 20px; }
  h2 { color: #1a3c5e; margin-bottom: 18px; font-size: 20px; }
  table { width: 100%; border-collapse: collapse; font-size: 14px; }
  th { background: #27ae60; color: #fff; padding: 10px 12px; text-align: left; }
  td { padding: 9px 12px; border-bottom: 1px solid #eee; }
  tr:hover td { background: #f7f9fb; }
  .btn { padding: 6px 14px; border: none; border-radius: 5px; cursor: pointer; font-size: 13px; text-decoration: none; display: inline-block; }
  .btn-primary { background: #27ae60; color: #fff; }
  .btn-warning { background: #e6a817; color: #fff; }
  .btn:hover { opacity: 0.88; }
  input[type=number], select { width: 100%; padding: 9px 11px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px; margin-bottom: 14px; }
  label { display: block; font-size: 13px; color: #444; margin-bottom: 4px; }
  .badge-tersedia { background: #d4edda; color: #155724; padding: 3px 9px; border-radius: 12px; font-size: 12px; }
  .badge-habis { background: #f8d7da; color: #721c24; padding: 3px 9px; border-radius: 12px; font-size: 12px; }
  .badge-dipinjam { background: #fff3cd; color: #856404; padding: 3px 9px; border-radius: 12px; font-size: 12px; }
  .badge-kembali { background: #d4edda; color: #155724; padding: 3px 9px; border-radius: 12px; font-size: 12px; }
  .badge-baik { background: #cce5ff; color: #004085; padding: 3px 9px; border-radius: 12px; font-size: 12px; }
  .badge-rusak { background: #f8d7da; color: #721c24; padding: 3px 9px; border-radius: 12px; font-size: 12px; }
  .alert { padding: 12px 16px; border-radius: 6px; margin-bottom: 18px; font-size: 14px; }
  .alert-success { background: #d4edda; color: #155724; }
  .alert-danger  { background: #f8d7da; color: #721c24; }
</style>
</head>
<body>
<nav>
  <span class="brand">📦 Inventaris Sekolah</span>
  <a href="dashboard.php">Daftar Barang</a>
  <a href="riwayat.php">Riwayat Saya</a>
  <span class="right">
    👤 <?= htmlspecialchars($_SESSION['nama']) ?>
    &nbsp;|&nbsp; <a href="../logout.php">Logout</a>
  </span>
</nav>
<div class="container">
