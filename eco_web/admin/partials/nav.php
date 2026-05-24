<?php
$current_admin_page = basename($_SERVER['PHP_SELF']);
$nav_items = [
    ['file'=>'index.php',        'icon'=>'', 'label'=>'Dashboard'],
    ['file'=>'transactions.php', 'icon'=>'', 'label'=>'Transactions'],
    ['file'=>'machine.php',      'icon'=>'', 'label'=>'Machine Status'],
    ['file'=>'users.php',        'icon'=>'', 'label'=>'Users'],
    ['file'=>'messages.php',     'icon'=>'',  'label'=>'Messages'],
    
    ['file'=>'settings.php',     'icon'=>'',  'label'=>'Settings'],
];
?>
<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar__logo">
    <span></span>
    <span class="sidebar__logo-text">Bottle<strong>Back</strong></span>
    <button class="sidebar__close" id="sidebarClose">✕</button>
  </div>
  <div class="sidebar__badge">Admin Panel</div>
  <nav class="sidebar__nav">
    <?php foreach ($nav_items as $item): ?>
    <a href="<?php echo $item['file']; ?>" class="sidebar__link <?php echo $current_admin_page===$item['file']?'sidebar__link--active':''; ?>">
      <span class="sidebar__icon"><?php echo $item['icon']; ?></span>
      <?php echo $item['label']; ?>
    </a>
    <?php endforeach; ?>
  </nav>
  <div class="sidebar__footer">
    <a href="../index.php" class="sidebar__link" target="_blank">
      <span class="sidebar__icon"></span> Public Site ↗
    </a>
    <a href="logout.php" class="sidebar__link sidebar__link--danger">
      <span class="sidebar__icon"></span> Log Out
    </a>
  </div>
</aside>
<!-- TOPBAR -->
<header class="topbar">
  <button class="topbar__toggle" id="sidebarToggle">☰</button>
  <div class="topbar__title">
    <?php
    $titles = ['index.php'=>'Dashboard','transactions.php'=>'Transactions',
               'machine.php'=>'Machine Status','users.php'=>'Users',
               'messages.php'=>'Contact Messages','export.php'=>'Export Data',
               'settings.php'=>'Settings'];
    echo $titles[$current_admin_page] ?? 'Admin';
    ?>
  </div>
  <div class="topbar__right">
    <span class="topbar__user"> Admin</span>
    <a href="logout.php" class="topbar__logout">Sign Out</a>
  </div>
</header>
