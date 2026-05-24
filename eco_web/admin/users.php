<?php

require 'auth.php';
$db = bb_db();
$connected = $db !== null;

$msg = '';


if ($connected && $_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['toggle_user'])) {
    $uid    = intval($_POST['user_id']);
    $status = intval($_POST['new_status']);
    $st = $db->prepare("UPDATE users SET is_active=? WHERE id=?");
    $st->bind_param('ii', $status, $uid);
    $st->execute(); $st->close();
    $msg = 'toggled';
}


if ($connected && $_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_user'])) {
    $uid = intval($_POST['user_id']);
    $st = $db->prepare("DELETE FROM users WHERE id=?");
    $st->bind_param('i', $uid);
    $st->execute(); $st->close();
    $msg = 'deleted';
}


$search   = $_GET['q'] ?? '';
$page     = max(1, intval($_GET['p'] ?? 1));
$per_page = 20;
$total = 0; $users = [];

if ($connected) {
    if ($search) {
        $like = "%$search%";
        $st = $db->prepare("SELECT COUNT(*) c FROM users WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR barangay LIKE ?");
        $st->bind_param('ssss',$like,$like,$like,$like); $st->execute();
        $total = (int)$st->get_result()->fetch_assoc()['c']; $st->close();
        $offset = ($page-1)*$per_page;
        $st = $db->prepare("SELECT * FROM users WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR barangay LIKE ? ORDER BY created_at DESC LIMIT $per_page OFFSET $offset");
        $st->bind_param('ssss',$like,$like,$like,$like); $st->execute();
        $res = $st->get_result();
        while ($row=$res->fetch_assoc()) $users[] = $row;
        $st->close();
    } else {
        $r = $db->query("SELECT COUNT(*) c FROM users");
        $total = $r ? (int)$r->fetch_assoc()['c'] : 0;
        $offset = ($page-1)*$per_page;
        $r = $db->query("SELECT * FROM users ORDER BY created_at DESC LIMIT $per_page OFFSET $offset");
        if ($r) while ($row=$r->fetch_assoc()) $users[] = $row;
    }
}
$total_pages = max(1, ceil($total/$per_page));

// KPIs
$kpi_total = $kpi_active = $kpi_today = 0;
if ($connected) {
    $r = $db->query("SELECT COUNT(*) c FROM users"); $kpi_total = $r?(int)$r->fetch_assoc()['c']:0;
    $r = $db->query("SELECT COUNT(*) c FROM users WHERE is_active=1"); $kpi_active = $r?(int)$r->fetch_assoc()['c']:0;
    $r = $db->query("SELECT COUNT(*) c FROM users WHERE DATE(created_at)=CURDATE()"); $kpi_today = $r?(int)$r->fetch_assoc()['c']:0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Users — BottleBack Admin</title>
  <link rel="stylesheet" href="admin.css"/>
</head>
<body>
<div class="admin-layout">
  <div class="sidebar-overlay" id="overlay"></div>
  <?php

  $current_admin_page = 'users.php';
  $nav_items = [
    ['file'=>'index.php',        'icon'=>'', 'label'=>'Dashboard'],
    ['file'=>'transactions.php', 'icon'=>'', 'label'=>'Transactions'],
    ['file'=>'machine.php',      'icon'=>'', 'label'=>'Machine Status'],
    ['file'=>'users.php',        'icon'=>'', 'label'=>'Users'],
    ['file'=>'messages.php',     'icon'=>'',  'label'=>'Messages'],
    ['file'=>'settings.php',     'icon'=>'',  'label'=>'Settings'],
  ];
  ?>
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
  <header class="topbar">
    <button class="topbar__toggle" id="sidebarToggle">☰</button>
    <div class="topbar__title">Users</div>
    <div class="topbar__right">
      <span class="topbar__user"> Admin</span>
      <a href="logout.php" class="topbar__logout">Sign Out</a>
    </div>
  </header>

  <div class="main-area">
    <div class="main-content">

      <div class="page-header">
        <div class="page-header__text">
          <div class="label">Accounts</div>
          <h1> Registered <em>Users</em></h1>
        </div>
  
      </div>

      <?php if($msg==='toggled'): ?><div class="alert alert--success"> User status updated.</div><?php endif; ?>
      <?php if($msg==='deleted'): ?><div class="alert alert--success"> User deleted.</div><?php endif; ?>
      <?php if(!$connected): ?><div class="alert alert--warn"> Database not connected.</div><?php endif; ?>

      <!-- KPIs -->
      <div class="kpi-grid" style="margin-bottom:1.8rem">
        <div class="kpi-card kpi-card--blue">
          <span class="kpi-icon"></span>
          <div><div class="kpi-label">Total Users</div><div class="kpi-value"><?php echo $connected?$kpi_total:'—'; ?></div></div>
        </div>
        <div class="kpi-card kpi-card--green">
          <span class="kpi-icon"></span>
          <div><div class="kpi-label">Active</div><div class="kpi-value"><?php echo $connected?$kpi_active:'—'; ?></div></div>
        </div>
        <div class="kpi-card kpi-card--teal">
          <span class="kpi-icon"></span>
          <div><div class="kpi-label">Joined Today</div><div class="kpi-value"><?php echo $connected?$kpi_today:'—'; ?></div></div>
        </div>
        <div class="kpi-card kpi-card--yellow">
          <span class="kpi-icon"></span>
          <div><div class="kpi-label">Inactive</div><div class="kpi-value"><?php echo $connected?($kpi_total-$kpi_active):'—'; ?></div></div>
        </div>
      </div>

      <div class="panel">
        <div class="panel-header">
          <span class="panel-title">All Accounts <span style="font-size:.82rem;font-family:var(--font-sans);font-weight:400;color:var(--muted)">(<?php echo number_format($total); ?> records)</span></span>
        </div>
        <div class="panel-body" style="padding-bottom:0">
          <form method="GET" class="filter-bar">
            <input type="text" name="q" class="form-input search-input" placeholder="🔍 Search name, email, barangay…" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn--primary btn--sm">Search</button>
            <?php if($search): ?><a href="users.php" class="btn btn--ghost btn--sm">Clear</a><?php endif; ?>
          </form>
        </div>

        <?php if($connected && count($users)): ?>
        <div class="table-wrap">
          <table>
            <thead>
              <tr><th>#</th><th>Name</th><th>Email</th><th>Barangay</th><th>Bottles</th><th>Rewards</th><th>Joined</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
              <?php foreach($users as $u): ?>
              <tr>
                <td class="td-mono"><?php echo $u['id']; ?></td>
                <td>
                  <div style="display:flex;align-items:center;gap:.6rem">
                    <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--green-400),var(--teal-500));display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;color:var(--white);font-family:var(--font-serif);flex-shrink:0">
                      <?php echo strtoupper(substr($u['first_name'],0,1)); ?>
                    </div>
                    <strong style="color:var(--ink)"><?php echo htmlspecialchars($u['first_name'].' '.$u['last_name']); ?></strong>
                  </div>
                </td>
                <td style="font-size:.85rem"><?php echo htmlspecialchars($u['email']); ?></td>
                <td><span class="badge badge--muted"><?php echo htmlspecialchars($u['barangay']); ?></span></td>
                <td style="font-weight:600;color:var(--green-600)"><?php echo number_format($u['total_bottles']); ?></td>
                <td style="font-weight:600;color:var(--teal-500)"><?php echo number_format($u['total_rewards']); ?></td>
                <td style="font-size:.82rem;color:var(--muted)"><?php echo date('M j, Y', strtotime($u['created_at'])); ?></td>
                <td>
                  <span class="badge badge--<?php echo $u['is_active']?'green':'red'; ?>">
                    <?php echo $u['is_active']?'Active':'Inactive'; ?>
                  </span>
                </td>
                <td>
                  <div style="display:flex;gap:.4rem">
                    <!-- Toggle active -->
                    <form method="POST" style="display:inline">
                      <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                      <input type="hidden" name="new_status" value="<?php echo $u['is_active']?0:1; ?>">
                      <button type="submit" name="toggle_user" class="btn btn--ghost btn--sm" title="<?php echo $u['is_active']?'Deactivate':'Activate'; ?>">
                        <?php echo $u['is_active']?'':''; ?>
                      </button>
                    </form>
                    <!-- Delete -->
                    <form method="POST" onsubmit="return confirm('Delete user <?php echo htmlspecialchars($u['first_name']); ?>? This cannot be undone.')" style="display:inline">
                      <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                      <button type="submit" name="delete_user" class="btn btn--danger btn--sm">🗑</button>
                    </form>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="panel-body" style="padding-top:1rem">
          <div class="pagination">
            <?php for($i=1;$i<=$total_pages;$i++): ?>
            <a href="?p=<?php echo $i; ?>&q=<?php echo urlencode($search); ?>" class="page-btn <?php echo $i===$page?'page-btn--active':''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            <span style="font-size:.78rem;color:var(--muted);margin-left:.5rem">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
          </div>
        </div>

        <?php else: ?>
        <div class="empty-state">
          <div class="empty-state__icon"></div>
          <h4>No users yet</h4>
          <p>Registered residents will appear here once someone creates an account at <code>pages/register.php</code>.</p>
        </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>
<script src="admin.js"></script>
</body>
</html>
