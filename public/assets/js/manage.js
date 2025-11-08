// public/assets/js/manage.js
async function fetchJSON(url, opts = {}) {
  const res = await fetch(url, opts);
  if (!res.ok) {
    const txt = await res.text();
    throw new Error(txt || res.statusText);
  }
  return res.json();
}

function showMsg(text, color = '#b00020') {
  const el = document.getElementById('manageMsg');
  el.style.color = color;
  el.textContent = text;
  if (text) setTimeout(()=>{ el.textContent = ''; }, 4000);
}

async function loadClassesIntoList() {
  const container = document.getElementById('classesList');
  const sel = document.getElementById('studentClass');
  container.innerHTML = 'Loading...';
  sel.innerHTML = '<option value="">-- Select class --</option>';
  try {
    const r = await fetchJSON('/attendance_project/public/api/get_classes.php');
    if (!r.success) throw new Error(r.error || 'Failed to load classes');
    const classes = r.data;
    if (!classes.length) {
      container.innerHTML = '<div class="muted">No classes found.</div>';
      return;
    }
    sel.innerHTML += classes.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
    container.innerHTML = classes.map(c => `
      <div class="list-item" data-id="${c.id}">
        <div>
          <strong>${c.name}</strong>
        </div>
        <div class="muted">
          <a href="#" class="view-students" data-id="${c.id}">View</a>
        </div>
      </div>
    `).join('');
    // attach view handlers
    document.querySelectorAll('.view-students').forEach(el => {
      el.addEventListener('click', (ev) => {
        ev.preventDefault();
        const id = el.getAttribute('data-id');
        document.getElementById('studentClass').value = id;
        loadStudentsForClass(id);
      });
    });
  } catch (e) {
    container.innerHTML = '<div class="muted">Error loading classes</div>';
    showMsg('Error loading classes: ' + e.message);
  }
}

async function addClass() {
  const name = document.getElementById('newClassName').value.trim();
  if (!name) { showMsg('Enter a class name'); return; }
  const form = new FormData();
  form.append('name', name);

  try {
    const r = await fetchJSON('/attendance_project/public/api/add_class.php', { method: 'POST', body: form });
    if (!r.success) throw new Error(r.error || 'Failed to add');
    showMsg('Class added: ' + r.name, 'green');
    document.getElementById('newClassName').value = '';
    await loadClassesIntoList();
  } catch (e) {
    showMsg('Error adding class: ' + e.message);
  }
}

async function addStudent() {
  const roll = document.getElementById('studentRoll').value.trim();
  const name = document.getElementById('studentName').value.trim();
  const email = document.getElementById('studentEmail').value.trim();
  const classId = document.getElementById('studentClass').value;

  if (!roll || !name || !classId) { showMsg('Roll, name and class are required'); return; }

  const form = new FormData();
  form.append('roll_no', roll);
  form.append('name', name);
  form.append('class_id', classId);
  form.append('email', email);

  try {
    const r = await fetchJSON('/attendance_project/public/api/add_student.php', { method: 'POST', body: form });
    if (!r.success) throw new Error(r.error || 'Failed to add student');
    showMsg('Student added: ' + r.name, 'green');
    document.getElementById('studentRoll').value = '';
    document.getElementById('studentName').value = '';
    document.getElementById('studentEmail').value = '';
    await loadStudentsForClass(classId);
  } catch (e) {
    showMsg('Error adding student: ' + e.message);
  }
}

async function loadStudentsForClass(classId) {
  const container = document.getElementById('studentsList');
  container.innerHTML = 'Loading...';
  try {
    const r = await fetchJSON('/attendance_project/public/api/get_students.php?class_id=' + encodeURIComponent(classId));
    if (!r.success) throw new Error(r.error || 'Failed');
    const students = r.data;
    if (!students.length) {
      container.innerHTML = '<div class="muted">No students in this class.</div>';
      return;
    }
    container.innerHTML = students.map(s => `
      <div class="list-item">
        <div>
          <strong>${s.roll_no}</strong> — ${s.name}
          <div class="muted">${s.email || ''}</div>
        </div>
      </div>
    `).join('');
  } catch (e) {
    container.innerHTML = '<div class="muted">Error loading students</div>';
    showMsg('Error loading students: ' + e.message);
  }
}

document.addEventListener('DOMContentLoaded', () => {
  loadClassesIntoList();
  document.getElementById('addClassBtn').addEventListener('click', addClass);
  document.getElementById('addStudentBtn').addEventListener('click', addStudent);
  document.getElementById('studentClass').addEventListener('change', (ev) => {
    const id = ev.target.value;
    if (id) loadStudentsForClass(id);
    else document.getElementById('studentsList').innerHTML = 'Select a class to see students.';
  });
});
