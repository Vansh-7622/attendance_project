<?php
// public/manage.php
require_once __DIR__ . '/../includes/auth_check.php';
require_login(); // allow admin/teacher
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Manage — Attendance System</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="../public/assets/css/style.css">
  <style>
    .grid { display:grid; grid-template-columns: 1fr 1fr; gap:16px; }
    .form-row { display:flex; gap:8px; align-items:center; margin-top:8px; }
    .list { max-height:300px; overflow:auto; border:1px solid #eee; border-radius:6px; padding:8px; background:#fff; }
    .list-item { padding:8px; border-bottom:1px solid #f1f1f1; display:flex; justify-content:space-between; align-items:center; }
    .list-item:last-child { border-bottom:none; }
    .muted { color:#666; font-size:13px; }
    .small { font-size:13px; color:#555; }
  </style>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">

</head>
<body>
  <header class="topbar">
    <div class="brand">Attendance System</div>
    <div class="user-area">
      <a class="btn" href="./dashboard.php">Dashboard</a>
      &nbsp;|&nbsp;
      <a class="btn" href="./reports.php">Reports</a>
      &nbsp;|&nbsp;
      <a class="btn" href="./api/logout.php">Logout</a>
    </div>
  </header>

  <main class="container">
    <section class="card">
      <h2>Manage Classes & Students</h2>
      <div class="grid">
        <!-- LEFT: Classes -->
        <div>
          <h3>Classes</h3>
          <div class="form-row">
            <input id="newClassName" placeholder="Class name (e.g. CSE-3A)" style="flex:1;padding:8px;border:1px solid #ddd;border-radius:6px;">
            <button id="addClassBtn" class="btn">Add Class</button>
          </div>
          <div style="margin-top:10px;" class="list" id="classesList">
            Loading classes...
          </div>
        </div>

        <!-- RIGHT: Students -->
        <div>
          <h3>Add Student</h3>
          <div class="form-row">
            <input id="studentRoll" placeholder="Roll No (e.g. CSE2A006)" style="flex:1;padding:8px;border:1px solid #ddd;border-radius:6px;">
            <input id="studentName" placeholder="Name" style="flex:1;padding:8px;border:1px solid #ddd;border-radius:6px;">
          </div>
          <div class="form-row" style="margin-top:8px;">
            <input id="studentEmail" placeholder="Email (optional)" style="flex:1;padding:8px;border:1px solid #ddd;border-radius:6px;">
            <select id="studentClass" style="padding:8px;border:1px solid #ddd;border-radius:6px;">
              <option value="">-- Select class --</option>
            </select>
            <button id="addStudentBtn" class="btn">Add Student</button>
          </div>

          <div style="margin-top:14px;">
            <h4>Students in Selected Class</h4>
            <div class="list" id="studentsList">Select a class to see students.</div>
          </div>
        </div>
      </div>

      <div id="manageMsg" style="margin-top:12px;color:#b00020;"></div>
    </section>
  </main>

  <!-- ✅ Correct relative path -->
  <script src="../public/assets/js/manage.js"></script>
  <script src="assets/js/ui.js"></script>

</body>
</html>
