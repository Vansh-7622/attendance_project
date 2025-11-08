// public/assets/js/app.js
async function fetchJSON(url, opts = {}) {
  const res = await fetch(url, opts);
  if (!res.ok) {
    const txt = await res.text();
    throw new Error(txt || res.statusText);
  }
  return res.json();
}

async function loadClasses() {
  const sel = document.getElementById('classSelect');
  sel.innerHTML = '<option value="">-- Loading classes --</option>';
  try {
    const r = await fetchJSON('/attendance_project/public/api/get_classes.php');
    if (!r.success) throw new Error(r.error || 'Failed to load classes');
    const options = r.data.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
    sel.innerHTML = '<option value="">-- Select class --</option>' + options;
  } catch (e) {
    sel.innerHTML = '<option value="">-- Error loading --</option>';
    document.getElementById('msg').textContent = 'Error loading classes: ' + e.message;
  }
}

async function loadStudents() {
  const classId = document.getElementById('classSelect').value;
  const tbody = document.querySelector('#studentsTbl tbody');
  tbody.innerHTML = '';
  document.getElementById('msg').textContent = '';

  if (!classId) {
    document.getElementById('msg').textContent = 'Please select a class.';
    return;
  }
  const date = document.getElementById('attDate').value;

  try {
    const r = await fetchJSON('/attendance_project/public/api/get_students.php?class_id=' + encodeURIComponent(classId));
    if (!r.success) throw new Error(r.error || 'Failed to load students');
    if (r.data.length === 0) {
      tbody.innerHTML = '<tr><td colspan="4">No students found for this class.</td></tr>';
      return;
    }
    tbody.innerHTML = r.data.map(s => `
      <tr data-id="${s.id}">
        <td>${s.roll_no}</td>
        <td>${s.name}</td>
        <td>${s.email || ''}</td>
        <td>
          <select class="status-select">
            <option value="present">Present</option>
            <option value="absent">Absent</option>
            <option value="late">Late</option>
          </select>
        </td>
      </tr>`).join('');
  } catch (e) {
    document.getElementById('msg').textContent = 'Error loading students: ' + e.message;
  }
}

async function saveAttendance() {
  const classId = document.getElementById('classSelect').value;
  if (!classId) { document.getElementById('msg').textContent = 'Select a class first.'; return; }
  const date = document.getElementById('attDate').value;
  const rows = document.querySelectorAll('#studentsTbl tbody tr');
  if (rows.length === 0) { document.getElementById('msg').textContent = 'No students to save.'; return; }

  const marks = Array.from(rows).map(r => ({
    student_id: r.dataset.id,
    status: r.querySelector('.status-select').value
  }));

  try {
    const r = await fetchJSON('/attendance_project/public/api/mark_attendance.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({ class_id: parseInt(classId,10), date, marks })
    });
    if (r.success) {
      document.getElementById('msg').style.color = 'green';
      document.getElementById('msg').textContent = 'Attendance saved successfully.';
      setTimeout(()=>{ document.getElementById('msg').textContent = ''; }, 2500);
    } else {
      throw new Error(r.error || 'Unknown error');
    }
  } catch (e) {
    document.getElementById('msg').style.color = '#b00020';
    document.getElementById('msg').textContent = 'Error saving attendance: ' + e.message;
  }
}

document.addEventListener('DOMContentLoaded', () => {
  loadClasses();
  document.getElementById('loadBtn').addEventListener('click', loadStudents);
  document.getElementById('saveBtn').addEventListener('click', saveAttendance);
});
