<?php
// public/dashboard.php
require_once __DIR__ . '/../includes/auth_check.php';
require_login();
$user = $_SESSION['user'] ?? null;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Dashboard — Attendance System</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .cards { display:flex; gap:12px; flex-wrap:wrap; margin-bottom:18px; }
    .card-small { background:#fff; padding:14px; border-radius:10px; box-shadow:0 6px 18px rgba(0,0,0,0.06); min-width:180px; flex:1; }
    .card-small h3 { margin:0 0 6px; font-size:14px; color:#555; }
    .card-small .big { font-size:22px; font-weight:700; color:#222; }
    .class-breakdown { margin-top:12px; }
    .class-row { display:flex; justify-content:space-between; padding:8px 6px; border-bottom:1px solid #f3f3f3; }
    .muted { color:#666; font-size:13px; }
  </style>
</head>
<script src="assets/js/attendance.js"></script>

<body>
  <header class="topbar">
    <div class="brand">Attendance System</div>
    <div class="user-area">
      Logged in as: <strong><?= htmlspecialchars($user['name'] ?? $user['username'] ?? 'User') ?></strong>
      &nbsp;|&nbsp;
      <a class="btn" href="manage.php">Manage</a>
      &nbsp;|&nbsp;
      <a class="btn" href="reports.php">Reports</a>
      &nbsp;|&nbsp;
      <a class="btn" href="api/logout.php">Logout</a>
    </div>
  </header>

  <main class="container">
    <section class="card">
      <h2>Dashboard</h2>
      
      <!-- MARK ATTENDANCE SECTION -->
<div class="card-small" style="margin-bottom:18px;">
  <h3>Mark Attendance</h3>
  <form id="attendanceForm" onsubmit="return false;" style="margin-bottom:10px;">
    <select id="classSelect" style="padding:8px;border:1px solid #ccc;border-radius:6px;">
      <option value="">-- Select class --</option>
    </select>
    <input type="date" id="attendanceDate" value="<?php echo date('Y-m-d'); ?>" style="padding:8px;border:1px solid #ccc;border-radius:6px;">
    <button type="button" id="loadStudentsBtn" class="btn">Load Students</button>
    <button type="button" id="markAllPresentBtn" class="btn">Mark All Present</button>
  </form>

  <div id="studentsTableArea" style="overflow-x:auto;"></div>
  <button id="saveAttendanceBtn" class="btn" style="margin-top:10px;">Save Attendance</button>
  <div id="attendanceMsg" style="margin-top:10px;color:#b00020;"></div>
</div>

      <div class="cards" id="summaryCards">
        <div class="card-small">
          <h3>Total students</h3>
          <div class="big" id="totalStudents">—</div>
        </div>
        <div class="card-small">
          <h3>Total classes</h3>
          <div class="big" id="totalClasses">—</div>
        </div>
        <div class="card-small">
          <h3>Marked today</h3>
          <div class="big" id="markedToday">—</div>
          <div class="muted">Present: <span id="presentToday">—</span> (<span id="overallPercentToday">—</span>%)</div>
        </div>
      </div>

      <div class="card-small" style="padding:12px;">
        <h3>Per-class breakdown (today)</h3>
        <div class="class-breakdown" id="perClassList">
          Loading...
        </div>
      </div>

      <div id="dmsg" style="margin-top:12px;color:#b00020;"></div>
    </section>
  </main>

  <script src="assets/js/dashboard.js"></script>
  
</body>
</html>
