// public/assets/js/attendance.js
// Handles loading classes, students, marking all present, saving attendance,
// and refreshing dashboard summary immediately after save.

async function fetchJSON(url, opts = {}) {
  const res = await fetch(url, opts);
  if (!res.ok) {
    const txt = await res.text();
    throw new Error(txt || res.statusText);
  }
  return res.json();
}

async function loadClassOptions() {
  const sel = document.getElementById('classSelect');
  try {
    const r = await fetchJSON('./api/get_classes.php');
    if (!r.success) throw new Error(r.error || 'Failed to load classes');
    sel.innerHTML = '<option value="">-- Select class --</option>' +
      r.data.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
  } catch (e) {
    document.getElementById('attendanceMsg').textContent = 'Error loading classes: ' + e.message;
  }
}

async function loadStudents() {
  const classId = document.getElementById('classSelect').value;
  const date = document.getElementById('attendanceDate').value;
  if (!classId) return alert('Select class first');
  const msg = document.getElementById('attendanceMsg');
  msg.textContent = '';
  try {
    const r = await fetchJSON('./api/get_students.php?class_id=' + encodeURIComponent(classId));
    if (!r.success) throw new Error(r.error || 'Failed to load students');
    const students = r.data;
    if (!students.length) {
      document.getElementById('studentsTableArea').innerHTML = '<p>No students found.</p>';
      return;
    }
    let html = `<table border="1" cellpadding="6" cellspacing="0" id="studentsTbl" style="width:100%;border-collapse:collapse;">
      <thead><tr><th style="text-align:left;padding:8px;">Roll No</th><th style="text-align:left;padding:8px;">Name</th><th style="text-align:left;padding:8px;">Status</th></tr></thead><tbody>`;
    for (const s of students) {
      html += `<tr>
        <td style="padding:8px;border-top:1px solid #eaeaea;">${s.roll_no}</td>
        <td style="padding:8px;border-top:1px solid #eaeaea;">${s.name}</td>
        <td style="padding:8px;border-top:1px solid #eaeaea;">
          <select class="status-select" data-student="${s.id}" style="padding:6px;border-radius:6px;">
            <option value="present">Present</option>
            <option value="absent">Absent</option>
            <option value="late">Late</option>
          </select>
        </td>
      </tr>`;
    }
    html += '</tbody></table>';
    document.getElementById('studentsTableArea').innerHTML = html;
  } catch (e) {
    msg.textContent = 'Error loading students: ' + e.message;
  }
}

function markAllPresent() {
  document.querySelectorAll('#studentsTbl .status-select').forEach(sel => sel.value = 'present');
}

async function saveAttendance() {
  const classId = document.getElementById('classSelect').value;
  const date = document.getElementById('attendanceDate').value;
  const msg = document.getElementById('attendanceMsg');
  msg.textContent = '';

  if (!classId) return alert('Select class first');
  const rows = document.querySelectorAll('#studentsTbl .status-select');
  if (!rows.length) return alert('No students loaded');

  const marks = [];
  rows.forEach(sel => {
    marks.push({
      student_id: sel.getAttribute('data-student'),
      status: sel.value
    });
  });

  try {
    const r = await fetch('./api/mark_attendance.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ class_id: classId, date: date, marks: marks })
    });
    const json = await r.json();
    if (!json.success) throw new Error(JSON.stringify(json));
    msg.style.color = 'green';
    msg.textContent = 'Attendance saved successfully!';

    // === Immediately refresh dashboard summary (if dashboard elements exist) ===
    try {
      fetch('./api/summary.php')
        .then(res => res.json())
        .then(s => {
          if (s && s.success && document.getElementById('totalStudents')) {
            const d = s.data;
            // Update small stat cards if present on the page
            if (document.getElementById('totalStudents')) document.getElementById('totalStudents').textContent = d.total_students;
            if (document.getElementById('totalClasses')) document.getElementById('totalClasses').textContent = d.total_classes;
            if (document.getElementById('markedToday')) document.getElementById('markedToday').textContent = d.marked_today;
            if (document.getElementById('presentToday')) document.getElementById('presentToday').textContent = d.present_today;
            if (document.getElementById('overallPercentToday')) document.getElementById('overallPercentToday').textContent = d.overall_percent_today;

            // Update per-class breakdown list if present
            if (Array.isArray(d.per_class) && document.getElementById('perClassList')) {
              document.getElementById('perClassList').innerHTML = d.per_class.map(c => `
                <div class="class-row">
                  <div><strong>${c.name}</strong></div>
                  <div>${c.present}/${c.total} — ${c.percentage}%</div>
                </div>
              `).join('');
            }
          }
        })
        .catch(e => {
          // ignore; not critical if dashboard not present
        });
    } catch (e) {
      // ignore
    }
    // ======================================================================

  } catch (e) {
    msg.style.color = '#b00020';
    msg.textContent = 'Error saving: ' + e.message;
  }
}

document.addEventListener('DOMContentLoaded', () => {
  // Initialize controls
  loadClassOptions();
  document.getElementById('loadStudentsBtn').addEventListener('click', loadStudents);
  document.getElementById('saveAttendanceBtn').addEventListener('click', saveAttendance);
  document.getElementById('markAllPresentBtn').addEventListener('click', markAllPresent);
});
