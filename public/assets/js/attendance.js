// public/assets/js/attendance.js
// Handles loading classes, students, marking all present, saving attendance,
// and refreshing dashboard summary immediately after save.
// Uses ui.js helpers: showToast() and decorStatusSelects()

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
    // use toast for nicer feedback
    if (window.showError) showError('Error loading classes: ' + e.message);
    else console.error(e);
  }
}

async function loadStudents() {
  const classId = document.getElementById('classSelect').value;
  const date = document.getElementById('attendanceDate').value;
  if (!classId) return showError ? showError('Select class first') : alert('Select class first');
  try {
    const r = await fetchJSON('./api/get_students.php?class_id=' + encodeURIComponent(classId));
    if (!r.success) throw new Error(r.error || 'Failed to load students');
    const students = r.data;
    if (!students.length) {
      document.getElementById('studentsTableArea').innerHTML = '<p class="muted">No students found.</p>';
      return;
    }
    let html = `<table class="table" id="studentsTbl">
      <thead><tr><th>Roll No</th><th>Name</th><th>Status</th></tr></thead><tbody>`;
    for (const s of students) {
      html += `<tr>
        <td>${s.roll_no}</td>
        <td>${s.name}</td>
        <td>
          <select class="status-select" data-student="${s.id}">
            <option value="present">Present</option>
            <option value="absent">Absent</option>
            <option value="late">Late</option>
          </select>
        </td>
      </tr>`;
    }
    html += '</tbody></table>';
    document.getElementById('studentsTableArea').innerHTML = html;

    // decorate selects (colors) if ui.js loaded
    if (typeof decorStatusSelects === 'function') decorStatusSelects();
  } catch (e) {
    if (window.showError) showError('Error loading students: ' + e.message);
    else console.error(e);
  }
}

function markAllPresent() {
  document.querySelectorAll('#studentsTbl .status-select').forEach(sel => sel.value = 'present');
  if (typeof decorStatusSelects === 'function') decorStatusSelects();
}

async function saveAttendance() {
  const classId = document.getElementById('classSelect').value;
  const date = document.getElementById('attendanceDate').value;
  if (!classId) return showError ? showError('Select class first') : alert('Select class first');

  const rows = document.querySelectorAll('#studentsTbl .status-select');
  if (!rows.length) return showError ? showError('No students loaded') : alert('No students loaded');

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
    if (!json.success) throw new Error(json.error || JSON.stringify(json));
    // success toast
    if (window.showSuccess) showSuccess('Attendance saved successfully!');
    else console.log('Attendance saved successfully!');

    // Immediately refresh dashboard summary (if dashboard elements exist)
    try {
      const s = await fetchJSON('./api/summary.php');
      if (s && s.success && document.getElementById('totalStudents')) {
        const d = s.data;
        if (document.getElementById('totalStudents')) document.getElementById('totalStudents').textContent = d.total_students;
        if (document.getElementById('totalClasses')) document.getElementById('totalClasses').textContent = d.total_classes;
        if (document.getElementById('markedToday')) document.getElementById('markedToday').textContent = d.marked_today;
        if (document.getElementById('presentToday')) document.getElementById('presentToday').textContent = d.present_today;
        if (document.getElementById('overallPercentToday')) document.getElementById('overallPercentToday').textContent = d.overall_percent_today;

        if (Array.isArray(d.per_class) && document.getElementById('perClassList')) {
          document.getElementById('perClassList').innerHTML = d.per_class.map(c => `
            <div class="class-row">
              <div><strong>${c.name}</strong></div>
              <div>${c.present}/${c.total} — ${c.percentage}%</div>
            </div>
          `).join('');
        }
      }
    } catch (e) {
      // non-fatal; ignore
      console.warn('Could not refresh summary:', e);
    }

  } catch (e) {
    if (window.showError) showError('Error saving attendance: ' + e.message);
    else console.error('Error saving attendance:', e);
  }
}

document.addEventListener('DOMContentLoaded', () => {
  loadClassOptions();
  const loadBtn = document.getElementById('loadStudentsBtn');
  if (loadBtn) loadBtn.addEventListener('click', loadStudents);
  const saveBtn = document.getElementById('saveAttendanceBtn');
  if (saveBtn) saveBtn.addEventListener('click', saveAttendance);
  const markAllBtn = document.getElementById('markAllPresentBtn');
  if (markAllBtn) markAllBtn.addEventListener('click', markAllPresent);
});
