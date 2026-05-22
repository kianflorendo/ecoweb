// BottleBack Admin Panel — JS

document.addEventListener('DOMContentLoaded', () => {
  const sidebar  = document.getElementById('sidebar');
  const overlay  = document.getElementById('overlay');
  const toggle   = document.getElementById('sidebarToggle');
  const close    = document.getElementById('sidebarClose');

  function openSidebar() {
    sidebar?.classList.add('open');
    overlay?.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeSidebar() {
    sidebar?.classList.remove('open');
    overlay?.classList.remove('open');
    document.body.style.overflow = '';
  }

  toggle?.addEventListener('click', openSidebar);
  close?.addEventListener('click', closeSidebar);
  overlay?.addEventListener('click', closeSidebar);

  // Auto-dismiss alerts after 5 seconds
  document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => {
      el.style.transition = 'opacity .5s';
      el.style.opacity = '0';
      setTimeout(() => el.remove(), 500);
    }, 5000);
  });

  // Animate KPI values counting up
  document.querySelectorAll('.kpi-value').forEach(el => {
    const raw = el.textContent.trim().replace(/,/g, '');
    const num = parseInt(raw, 10);
    if (isNaN(num) || num === 0 || raw.includes('—') || raw.includes('%')) return;
    let start = 0;
    const dur = 900;
    const step = dur / 60;
    const inc  = num / (dur / step);
    const timer = setInterval(() => {
      start = Math.min(start + inc, num);
      el.textContent = Math.round(start).toLocaleString();
      if (start >= num) clearInterval(timer);
    }, step);
  });

  // Bin fill animation
  document.querySelectorAll('.bin-fill').forEach(el => {
    const target = el.style.height;
    el.style.height = '0';
    requestAnimationFrame(() => {
      el.style.transition = 'height 1.2s cubic-bezier(.4,0,.2,1)';
      el.style.height = target;
    });
  });

  // Progress bar animation
  document.querySelectorAll('.progress-fill').forEach(el => {
    const target = el.style.width;
    el.style.width = '0';
    requestAnimationFrame(() => {
      el.style.transition = 'width 1s cubic-bezier(.4,0,.2,1)';
      el.style.width = target;
    });
  });
});
