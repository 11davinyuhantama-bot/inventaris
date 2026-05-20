<?php
// login.php
session_start();
if (isset($_SESSION['id_user'])) {
    // Sudah login, redirect sesuai role
    if ($_SESSION['role'] == 'admin') header("Location: admin/dashboard.php");
    else header("Location: user/dashboard.php");
    exit;
}
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Inventaris Sekolah</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: Arial, sans-serif; background: #1a3c5e; min-height: 100vh;
         display: flex; align-items: center; justify-content: center; }
  .card { background: #fff; padding: 40px 35px; border-radius: 10px;
          width: 380px; box-shadow: 0 8px 30px rgba(0,0,0,0.3); }
  h2 { text-align: center; color: #1a3c5e; margin-bottom: 8px; }
  p.sub { text-align: center; color: #666; font-size: 14px; margin-bottom: 25px; }
  label { display: block; font-size: 14px; color: #333; margin-bottom: 5px; }
  input { width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 6px;
          font-size: 14px; margin-bottom: 16px; }
  input:focus { outline: none; border-color: #1a3c5e; }
  button { width: 100%; padding: 12px; background: #1a3c5e; color: #fff;
           border: none; border-radius: 6px; font-size: 15px; cursor: pointer; }
  button:hover { background: #16324f; }
  .error { background: #ffe0e0; color: #c00; padding: 10px; border-radius: 6px;
           font-size: 13px; margin-bottom: 15px; text-align: center; }
  .hint { margin-top: 20px; font-size: 12px; color: #888; text-align: center; }
</style>
</head>
<body>
<div class="card">
  <h2>📦 Inventaris Sekolah</h2>
  <p class="sub">Silakan login untuk melanjutkan</p>

  <?php if ($error == '1'): ?>
    <div class="error">Username atau password salah!</div>
  <?php endif; ?>

  <form action="cek_login.php" method="POST">
    <label>Username</label>
    <input type="text" name="username" placeholder="Masukkan username" required>
    <label>Password</label>
    <input type="password" name="password" placeholder="Masukkan password" required>
    <button type="submit">Login</button>
  </form>

  <p class="hint">
    Admin: admin / admin123<br>
    User: budi / user123
  </p>
</div>
</body>
</html>
