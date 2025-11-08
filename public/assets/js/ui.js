/* ui.js — lightweight UI helpers: toast messages & colored status selects */

/* ---- Toast notifications ---- */
function showToast(msg, type = 'success') {
  const container = document.querySelector('.toast-wrap') || createToastContainer();
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.textContent = msg;
  container.appendChild(toast);

  // Animate in
  setTimeout(() => toast.classList.add('show'), 100);
  // Auto-hide after 3 s
  setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 250);
  }, 3000);
}

function createToastContainer() {
  const wrap = document.createElement('div');
  wrap.className = 'toast-wrap';
  document.body.appendChild(wrap);
  return wrap;
}

/* ---- Decorate status selects ---- */
function decorStatusSelects() {
  document.querySelectorAll('select.status-select').forEach(sel => {
    sel.addEventListener('change', e => {
      const val = e.target.value.toLowerCase();
      e.target.style.background = {
        present: 'rgba(16,185,129,0.12)',
        absent: 'rgba(239,68,68,0.08)',
        late:   'rgba(245,158,11,0.08)'
      }[val] || '#fff';
    });
  });
}

/* ---- Tiny helper to show success/error easily ---- */
window.showSuccess = msg => showToast(msg, 'success');
window.showError   = msg => showToast(msg, 'error');
