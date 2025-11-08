<?php
// public/reports.php
require_once __DIR__ . '/../includes/auth_check.php';
require_login();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Attendance Reports</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="/attendance_project/public/assets/css/style.css">
  <style>
    .report-controls { display:flex; gap:12px; align-items:center; margin-bottom:12px; flex-wrap:wrap; }
    .small { font-size:13px; color:#555; }
    .csv-btn { background:#2d3748; color:#fff; padding:8px 10px; border-radius:6px; border:none; cursor:pointer; }
  </style>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">

</head>
<body>
  <header class="topbar">
    <div class="brand">Attendance System</div>
    <div class="user-area">
      <a class="btn" href="/attendance_project/public/dashboard.php">Dashboard</a>
      &nbsp;|&nbsp;
      <a class="btn" href="/attendance_project/public/api/logout.php">Logout</a>
    </div>
  </header>

  <main class="container">
    <section class="card">
      <h2>Attendance Report</h2>

      <div class="report-controls">
        <label>Class
          <select id="classSelectRpt"><option>-- Loading --</option></select>
        </label>

        <label>From
          <input type="date" id="fromDate" value="<?= date('Y-m-01') ?>">
        </label>

        <label>To
          <input type="date" id="toDate" value="<?= date('Y-m-d') ?>">
        </label>

        <button id="genBtn" class="btn">Generate</button>
        <button id="csvBtn" class="csv-btn">Export CSV</button>
      </div>

      <div id="reportArea">
        <table id="reportTbl" class="table">
          <thead>
            <tr><th>Roll No</th><th>Name</th><th>Present</th><th>Total</th><th>Percentage</th></tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

      <div id="rmsg" class="small" style="margin-top:8px;color:#b00020;"></div>
    </section>
  </main>

  <script>
    async function fetchJSON(url, opts){ const r = await fetch(url, opts); if(!r.ok) throw new Error(await r.text()); return r.json(); }

    async function loadClasses() {
      const sel = document.getElementById('classSelectRpt');
      sel.innerHTML = '<option>-- Loading --</option>';
      try {
        const r = await fetchJSON('/attendance_project/public/api/get_classes.php');
        if (!r.success) throw new Error(r.error || 'Failed');
        sel.innerHTML = '<option value="">-- Select class --</option>' + r.data.map(c=>`<option value="${c.id}">${c.name}</option>`).join('');
      } catch(e) {
        sel.innerHTML = '<option>-- Error --</option>';
        document.getElementById('rmsg').textContent = 'Error loading classes: '+e.message;
      }
    }

    async function generateReport() {
      const cid = document.getElementById('classSelectRpt').value;
      const from = document.getElementById('fromDate').value;
      const to = document.getElementById('toDate').value;
      const tbody = document.querySelector('#reportTbl tbody');
      tbody.innerHTML = '';
      document.getElementById('rmsg').textContent = '';
      if (!cid) { document.getElementById('rmsg').textContent = 'Select a class.'; return; }
      try {
        const r = await fetchJSON(`/attendance_project/public/api/reports.php?class_id=${encodeURIComponent(cid)}&from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}`);
        if (!r.success) throw new Error(r.error || 'Failed to generate');
        if (r.data.length===0) {
          tbody.innerHTML = '<tr><td colspan="5">No data available for this range.</td></tr>'; return;
        }
        tbody.innerHTML = r.data.map(row=>`<tr>
          <td>${row.roll_no}</td>
          <td>${row.name}</td>
          <td>${row.present}</td>
          <td>${row.total}</td>
          <td>${row.percentage}%</td>
        </tr>`).join('');
        // store last results for CSV export
        window.__lastReport = r.data;
      } catch(e) {
        document.getElementById('rmsg').textContent = 'Error: '+e.message;
      }
    }

    function exportCSV() {
      const data = window.__lastReport || [];
      if (!data.length) { document.getElementById('rmsg').textContent = 'No report to export.'; return; }
      const header = ['Roll No','Name','Present','Total','Percentage'];
      const rows = data.map(d => [d.roll_no, d.name, d.present, d.total, d.percentage]);
      const csv = [header, ...rows].map(r => r.map(c => `"${String(c).replace(/"/g,'""')}"`).join(',')).join('\n');
      const blob = new Blob([csv], {type:'text/csv;charset=utf-8;'});
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'attendance_report.csv';
      document.body.appendChild(a);
      a.click();
      a.remove();
      URL.revokeObjectURL(url);
    }

    document.addEventListener('DOMContentLoaded', () => {
      loadClasses();
      document.getElementById('genBtn').addEventListener('click', generateReport);
      document.getElementById('csvBtn').addEventListener('click', exportCSV);
    });
  </script>
  <script src="assets/js/ui.js"></script>

</body>
</html>
