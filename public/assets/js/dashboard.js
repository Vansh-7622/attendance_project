// public/assets/js/dashboard.js
async function fetchJSON(url, opts = {}) {
  const res = await fetch(url, opts);
  if (!res.ok) {
    const txt = await res.text();
    throw new Error(txt || res.statusText);
  }
  return res.json();
}

async function loadSummary() {
  const msgEl = document.getElementById('dmsg');
  msgEl.textContent = '';
  try {
    const r = await fetchJSON('./api/summary.php');
    if (!r.success) throw new Error(r.error || 'Failed to load');
    const d = r.data;
    document.getElementById('totalStudents').textContent = d.total_students;
    document.getElementById('totalClasses').textContent = d.total_classes;
    document.getElementById('markedToday').textContent = d.marked_today;
    document.getElementById('presentToday').textContent = d.present_today;
    document.getElementById('overallPercentToday').textContent = d.overall_percent_today;

    const pc = d.per_class || [];
    const container = document.getElementById('perClassList');
    if (!pc.length) {
      container.innerHTML = '<div class="muted">No attendance marked today.</div>';
    } else {
      container.innerHTML = pc.map(c => `
        <div class="class-row">
          <div><strong>${c.name}</strong></div>
          <div>${c.present}/${c.total} — ${c.percentage}%</div>
        </div>
      `).join('');
    }
  } catch (e) {
    document.getElementById('dmsg').textContent = 'Error loading summary: ' + e.message;
  }
}

document.addEventListener('DOMContentLoaded', () => {
  loadSummary();
  // refresh summary every 60s
  setInterval(loadSummary, 60000);
});
