<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) session_start();

$current_page = basename($_SERVER['PHP_SELF']);
$current_dir  = basename(dirname($_SERVER['PHP_SELF']));
$base         = ($current_dir === 'pages') ? '../' : '';
$pb           = ($current_dir === 'pages') ? '' : 'pages/';

$logged_in    = !empty($_SESSION['user_id']);
$user_name    = $_SESSION['user_name'] ?? '';
$user_initial = $user_name ? strtoupper(substr($user_name,0,1)) : '';
?>

<!-- Flaticon Icons -->
<link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>

<nav class="navbar" id="navbar">
  <div class="navbar__inner">

    <!-- Logo -->
    <a href="<?php echo $base; ?>index.php" class="navbar__logo">
      <i class="fi fi-sr-save-the-planet"></i>
      <span class="logo-text">Bottle<strong>Back</strong></span>
    </a>

    <!-- Mobile Toggle -->
    <button class="navbar__toggle" id="navToggle" aria-label="Toggle navigation">
      <span></span><span></span><span></span>
    </button>

    <!-- Navigation Links -->
    <ul class="navbar__links" id="navLinks">

      <?php
      $links = [
        ['href'=>$base.'index.php',       'label'=>'Home',         'file'=>'index.php'],
        ['href'=>$pb.'about.php',         'label'=>'About',        'file'=>'about.php'],
        ['href'=>$pb.'how-it-works.php',  'label'=>'How It Works', 'file'=>'how-it-works.php'],
        ['href'=>$pb.'data.php',          'label'=>'Live Data',    'file'=>'data.php'],
        ['href'=>$pb.'awareness.php',     'label'=>'Awareness',    'file'=>'awareness.php'],
        ['href'=>$pb.'contact.php',       'label'=>'Contact',      'file'=>'contact.php'],
      ];

      foreach ($links as $link):
        $is_active = ($current_page === $link['file']);
      ?>
        <li>
          <a href="<?php echo $link['href']; ?>"
             class="nav-link <?php echo $is_active ? 'nav-link--active' : ''; ?>">
             <?php echo $link['label']; ?>
          </a>
        </li>
      <?php endforeach; ?>

      <?php if ($logged_in): ?>

      <!-- User Dropdown -->
      <li class="nav-user-wrap" style="position:relative">

        <button class="nav-user-btn"
                id="userMenuBtn"
                onclick="toggleUserMenu()"
                aria-label="User menu">

          <span class="nav-avatar">
            <?php echo $user_initial; ?>
          </span>

          <span class="nav-username">
            <?php echo htmlspecialchars($user_name); ?>
          </span>

          <span class="nav-caret">▾</span>
        </button>

        <div class="nav-dropdown" id="userDropdown">

          <a href="<?php echo $pb; ?>profile.php"
             class="nav-dropdown__item">
             My Profile
          </a>

          <a href="<?php echo $pb; ?>edit-profile.php"
             class="nav-dropdown__item">
             Edit Profile
          </a>

          <div class="nav-dropdown__divider"></div>

          <a href="<?php echo $pb; ?>logout.php"
             class="nav-dropdown__item nav-dropdown__item--danger">
             Sign Out
          </a>

        </div>
      </li>

      <?php else: ?>

      <!-- Login -->
      <li>
        <a href="<?php echo $pb; ?>login.php"
           class="nav-link"
           style="display:inline-flex;align-items:center;gap:.4rem">
          Sign In
        </a>
      </li>

      <!-- Register -->
      <li>
        <a href="<?php echo $pb; ?>register.php"
           class="btn btn--primary btn--sm"
           style="font-size:.82rem;padding:.5em 1.2em">
          Join Free
        </a>
      </li>

      <?php endif; ?>

    </ul>
  </div>
</nav>

<style>

/* Logo Icon */
.navbar__logo{
  display:flex;
  align-items:center;
  gap:10px;
  text-decoration:none;
}

.navbar__logo i{
  font-size:28px;
  color:#22c55e;
  display:flex;
  align-items:center;
}

/* ── Auth nav styles ── */

.nav-user-wrap{
  list-style:none;
}

.nav-user-btn{
  display:flex;
  align-items:center;
  gap:.5rem;
  background:rgba(255,255,255,.08);
  border:1px solid rgba(255,255,255,.15);
  border-radius:var(--r-full,999px);
  padding:.35rem .75rem .35rem .45rem;
  cursor:pointer;
  font-family:var(--font-sans);
  color:rgba(255,255,255,.85);
  font-size:.85rem;
  transition:.2s;
}

.nav-user-btn:hover{
  background:rgba(255,255,255,.14);
  border-color:rgba(255,255,255,.25);
}

.nav-avatar{
  width:26px;
  height:26px;
  border-radius:50%;
  background:linear-gradient(135deg,var(--green-400),var(--teal-500));
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:.75rem;
  font-weight:700;
  color:#fff;
  flex-shrink:0;
}

.nav-username{
  max-width:100px;
  overflow:hidden;
  text-overflow:ellipsis;
  white-space:nowrap;
}

.nav-caret{
  font-size:.6rem;
  opacity:.6;
  transition:transform .2s;
}

.nav-user-btn.open .nav-caret{
  transform:rotate(180deg);
}

.nav-dropdown{
  display:none;
  position:absolute;
  top:calc(100% + .6rem);
  right:0;
  background:var(--green-800,#163122);
  border:1px solid rgba(255,255,255,.1);
  border-radius:14px;
  min-width:190px;
  padding:.4rem;
  box-shadow:0 16px 48px rgba(0,0,0,.35);
  z-index:500;
  animation:fadeDown .18s ease;
}

@keyframes fadeDown{
  from{
    opacity:0;
    transform:translateY(-8px)
  }
  to{
    opacity:1;
    transform:translateY(0)
  }
}

.nav-dropdown.open{
  display:block;
}

.nav-dropdown__item{
  display:flex;
  align-items:center;
  gap:.6rem;
  padding:.62rem .9rem;
  border-radius:8px;
  font-size:.85rem;
  color:rgba(255,255,255,.7);
  transition:.15s;
  text-decoration:none;
}

.nav-dropdown__item:hover{
  background:rgba(255,255,255,.08);
  color:#fff;
}

.nav-dropdown__item--danger{
  color:rgba(248,113,113,.8);
}

.nav-dropdown__item--danger:hover{
  background:rgba(220,38,38,.12);
  color:#f87171;
}

.nav-dropdown__divider{
  height:1px;
  background:rgba(255,255,255,.08);
  margin:.3rem .5rem;
}

/* Mobile */

@media(max-width:680px){

  .nav-dropdown{
    position:static;
    box-shadow:none;
    border:none;
    background:transparent;
    padding:0;
    animation:none;
  }

  .nav-dropdown.open{
    display:flex;
    flex-direction:column;
  }

  .nav-user-btn{
    width:100%;
    border-radius:8px;
  }

  .nav-dropdown__item{
    color:rgba(255,255,255,.65);
  }
}

</style>

<script>

function toggleUserMenu(){

  const btn = document.getElementById('userMenuBtn');
  const dd  = document.getElementById('userDropdown');

  if (!btn || !dd) return;

  btn.classList.toggle('open');
  dd.classList.toggle('open');
}

// Close on outside click

document.addEventListener('click', function(e){

  const wrap = document.querySelector('.nav-user-wrap');

  if (wrap && !wrap.contains(e.target)) {

    document.getElementById('userMenuBtn')?.classList.remove('open');

    document.getElementById('userDropdown')?.classList.remove('open');
  }
});

</script>