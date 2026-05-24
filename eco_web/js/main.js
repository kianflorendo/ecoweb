// ── Navbar scroll
const navbar = document.getElementById('navbar');
if (navbar) {
  const onScroll = () => navbar.classList.toggle('scrolled', window.scrollY > 55);
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
}

// ── Mobile nav toggle
const navToggle = document.getElementById('navToggle');
const navLinks  = document.getElementById('navLinks');
if (navToggle && navLinks) {
  navToggle.addEventListener('click', () => {
    const open = navLinks.classList.toggle('open');
    navToggle.setAttribute('aria-expanded', open);
    // Animate hamburger → X
    const spans = navToggle.querySelectorAll('span');
    if (open) {
      spans[0].style.transform = 'translateY(7px) rotate(45deg)';
      spans[1].style.opacity   = '0';
      spans[2].style.transform = 'translateY(-7px) rotate(-45deg)';
    } else {
      spans.forEach(s => { s.style.transform = ''; s.style.opacity = ''; });
    }
  });
  navLinks.querySelectorAll('.nav-link').forEach(l => l.addEventListener('click', () => {
    navLinks.classList.remove('open');
    navToggle.querySelectorAll('span').forEach(s => { s.style.transform=''; s.style.opacity=''; });
  }));
  document.addEventListener('click', e => {
    if (!navbar.contains(e.target)) {
      navLinks.classList.remove('open');
      navToggle.querySelectorAll('span').forEach(s => { s.style.transform=''; s.style.opacity=''; });
    }
  });
}

// ── Scroll reveal
const revealTargets = document.querySelectorAll(
  '.step-card, .awareness-card, .tip-card, .kpi-card, .component-card, ' +
  '.project-badge, .research-card, .objective-item, .flow-detail-item, ' +
  '.fact-card, .plastic-card, .contact-card, .lc-step, .info-block'
);
if ('IntersectionObserver' in window && revealTargets.length) {
  const obs = new IntersectionObserver((entries) => {
    entries.forEach((e, idx) => {
      if (e.isIntersecting) {
        e.target.style.animation = `slideUp .5s ${parseFloat(e.target.dataset.delay || 0)}s ease both`;
        obs.unobserve(e.target);
      }
    });
  }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });

  revealTargets.forEach((el, i) => {
    el.style.opacity = '0';
    el.dataset.delay = (i % 6) * 0.07;
    obs.observe(el);
  });
}

// ── Hero machine counter animation (cosmetic demo)
const counterEl = document.getElementById('counterDisplay');
const pointsEl  = document.getElementById('pointsDisplay');
if (counterEl && pointsEl) {
  let bottles = 0, points = 0;
  const tick = () => {
    // Simulate machine activity for demo — replace with real AJAX when Arduino is connected
    const interval = Math.random() * 8000 + 4000;
    setTimeout(() => {
      bottles++;
      points += Math.floor(Math.random() * 3) + 1;
      counterEl.textContent = bottles;
      pointsEl.textContent  = points;
      counterEl.style.color = '#73c48a';
      pointsEl.style.color  = '#73c48a';
      setTimeout(() => {
        counterEl.style.color = '';
        pointsEl.style.color  = '';
      }, 600);
      tick();
    }, interval);
  };
  tick();
}

// ── Bin fill level animation
const binFill = document.querySelector('.bin-fill');
if (binFill) {
  const level = parseInt(binFill.dataset.level || 0);
  binFill.style.height = '0%';
  setTimeout(() => { binFill.style.height = level + '%'; }, 400);
}

// ── Auto-refresh dashboard every 30s if on data page
if (window.location.pathname.includes('data.php')) {
  setTimeout(() => window.location.reload(), 30000);
}
