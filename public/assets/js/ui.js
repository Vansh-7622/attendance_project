// public/assets/js/ui.js
// small UI helper: toast notifications and status pill helper

function showToast(message, type = 'default', timeout = 3500) {
  let wrap = document.querySelector('.toast-wrap');
  if (!wrap) {
    wrap = document.createElement('div');
    wrap.className = 'toast-wrap';
    document.body.appendChild(wrap);
  }
  const t = document.createElement('div');
  t.className = 'toast ' + (type === 'success' ? 'success' : (type === 'error' ? 'error' : ''));
  t.textContent = message;
  wrap.appendChild(t);
  // animate-in
  requestAnimationFrame(() => t.classList.add('show'));
  setTimeout(() => {
    t.classList.remove('show');
    setTimeout(()=> t.remove(), 250);
  }, timeout);
}

// convert status string to a pill element
function makeStatusPill(status) {
  const span = document.createElement('span');
  span.className = 'pill ' + status;
  span.textContent = status.charAt(0).toUpperCase() + status.slice(1);
  return span;
}
