<?php
// public/index.php
// Simple login page (POSTs to api/login.php)

session_start();
if (!empty($_SESSION['user'])) {
    // already logged in -> redirect to dashboard
    header('Location: /attendance_project/public/dashboard.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Attendance System — Login</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body { font-family: Arial, sans-serif; display:flex; align-items:center; justify-content:center; height:100vh; margin:0; background:#f4f6f9; }
    .card { background:#fff; padding:24px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.08); width:320px; }
    .card h1 { margin:0 0 12px; font-size:20px; }
    label { display:block; margin-top:10px; font-size:13px; color:#333; }
    input[type="text"], input[type="password"] { width:100%; padding:8px 10px; margin-top:6px; border:1px solid #ddd; border-radius:4px; box-sizing:border-box; }
    button { margin-top:16px; width:100%; padding:10px; border:none; background:#2b6cb0; color:white; border-radius:6px; cursor:pointer; font-weight:600; }
    .msg { margin-top:12px; color:#b00020; font-size:14px; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Login — Attendance System</h1>
    <form id="loginForm" method="post" action="api/login.php">
      <label for="username">Username</label>
      <input id="username" name="username" type="text" required autocomplete="username">

      <label for="password">Password</label>
      <input id="password" name="password" type="password" required autocomplete="current-password">

      <button type="submit">Login</button>
    </form>

    <?php if (!empty($_GET['error'])): ?>
      <div class="msg"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <div style="margin-top:10px; font-size:12px; color:#555;">
      (Use the admin you created earlier: <strong>admin / admin123</strong> unless you changed it.)
    </div>
  </div>
  
</body>
</html>
