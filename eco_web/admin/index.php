<?php

require 'auth.php';

$db = bb_db();
$connected = $db !== null;

// Stats
$today_bottles = $today_rewards = $total_bottles = $total_rewards = 0;
$rejected = $bin_level = 0;
$recent_tx = [];
$daily_data = [];

if ($connected) {
    $r = $db->query("SELECT COUNT(*) c FROM transactions WHERE DATE(created_at)=CURDATE() AND status='Accepted'");
    if ($r) $today_bottles = (int)$r->fetch_assoc()['c'];

    $r = $db->query("SELECT COALESCE(SUM(reward_amount),0) c FROM transactions WHERE DATE(created_at)=CURDATE()");
    if ($r) $today_rewards = (int)$r->fetch_assoc()['c'];

    $r = $db->query("SELECT COUNT(*) c FROM transactions WHERE status='Accepted'");
    if ($r) $total_bottles = (int)$r->fetch_assoc()['c'];

    $r = $db->query("SELECT COALESCE(SUM(reward_amount),0) c FROM transactions");
    if ($r) $total_rewards = (int)$r->fetch_assoc()['c'];

    $r = $db->query("SELECT COUNT(*) c FROM transactions WHERE status='Rejected'");
    if ($r) $rejected = (int)$r->fetch_assoc()['c'];

    $r = $db->query("SELECT bin_level FROM machine_status ORDER BY updated_at DESC LIMIT 1");
    if ($r && $r->num_rows) $bin_level = (int)$r->fetch_assoc()['bin_level'];

    $r = $db->query("SELECT * FROM transactions ORDER BY created_at DESC LIMIT 8");
    if ($r) while ($row = $r->fetch_assoc()) $recent_tx[] = $row;

    // Daily bottles last 7 days
    $r = $db->query("SELECT DATE(created_at) d, COUNT(*) c FROM transactions WHERE status='Accepted' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY d ORDER BY d");
    if ($r) while ($row = $r->fetch_assoc()) $daily_data[$row['d']] = (int)$row['c'];

    // Fill missing days
    for ($i = 6; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-$i days"));
        if (!isset($daily_data[$d])) $daily_data[$d] = 0;
    }
    ksort($daily_data);

    $r2 = $db->query("SELECT COUNT(*) c FROM contact_messages WHERE DATE(created_at)=CURDATE()");
    $new_msgs = $r2 ? (int)$r2->fetch_assoc()['c'] : 0;
}

function binColor($l){ if($l>=90)return 'danger'; if($l>=70)return 'warn'; return 'ok'; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard — BottleBack Admin</title>
  <link rel="stylesheet" href="admin.css"/>
</head>
<body>
<div class="admin-layout">
  <div class="sidebar-overlay" id="overlay"></div>
  <?php include 'partials/nav.php'; ?>
  <div class="main-area">
    <div class="main-content">

      <!-- PAGE HEADER -->
      <div class="page-header">
        <div class="page-header__text">
          <div class="label">Overview</div>
          <h1>Admin <em>Dashboard</em></h1>
        </div>
        <div style="display:flex;gap:.8rem;align-items:center;flex-wrap:wrap">
          <span style="font-size:.82rem;color:var(--muted)"> <?php echo date('F j, Y · H:i'); ?></span>
          <?php if($connected): ?>
            <span class="badge badge--green">● DB Connected</span>
          <?php else: ?>
            <span class="badge badge--red">● DB Offline</span>
          <?php endif; ?>
        </div>
      </div>

      <?php if(!$connected): ?>
      <div class="alert alert--warn">
        <span></span>
        <div><strong>Database not connected.</strong><p>Start XAMPP, create the <code>bottleback</code> database, and run <code>setup.sql</code>. Stats will appear automatically.</p></div>
      </div>
      <?php endif; ?>

      <!-- KPI CARDS -->
      <div class="kpi-grid">
        <div class="kpi-card kpi-card--green">
          <span class="kpi-icon"></span>
          <div class="kpi-body">
            <div class="kpi-label">Bottles Today</div>
            <div class="kpi-value"><?php echo $connected ? $today_bottles : '—'; ?></div>
          </div>
        </div>
        <div class="kpi-card kpi-card--yellow">
          <span class="kpi-icon"></span>
          <div class="kpi-body">
            <div class="kpi-label">Rewards Today</div>
            <div class="kpi-value"><?php echo $connected ? $today_rewards : '—'; ?></div>
          </div>
        </div>
        <div class="kpi-card kpi-card--blue">
          <span class="kpi-icon"></span>
          <div class="kpi-body">
            <div class="kpi-label">Total Accepted</div>
            <div class="kpi-value"><?php echo $connected ? number_format($total_bottles) : '—'; ?></div>
          </div>
        </div>
        <div class="kpi-card kpi-card--<?php echo $connected ? binColor($bin_level) === 'ok' ? 'teal' : binColor($bin_level) : 'teal'; ?>">
          <span class="kpi-icon"></span>
          <div class="kpi-body">
            <div class="kpi-label">Bin Fill Level</div>
            <div class="kpi-value"><?php echo $connected ? $bin_level.'%' : '—'; ?></div>
          </div>
        </div>
        <div class="kpi-card">
          <span class="kpi-icon"></span>
          <div class="kpi-body">
            <div class="kpi-label">Total Rewards</div>
            <div class="kpi-value"><?php echo $connected ? number_format($total_rewards) : '—'; ?></div>
          </div>
        </div>
        <div class="kpi-card kpi-card--red">
          <span class="kpi-icon"></span>
          <div class="kpi-body">
            <div class="kpi-label">Total Rejected</div>
            <div class="kpi-value"><?php echo $connected ? number_format($rejected) : '—'; ?></div>
          </div>
        </div>
      </div>

      <!-- CHARTS + BIN ROW -->
      <div class="grid-2" style="margin-bottom:1.6rem">

        <!-- 7-DAY CHART -->
        <div class="panel">
          <div class="panel-header">
            <span class="panel-title"> Bottles Accepted — Last 7 Days</span>
          </div>
          <div class="panel-body">
            <?php if($connected && array_sum($daily_data) > 0): ?>
            <div class="bar-chart" id="barChart"></div>
            <script>
              const data = <?php echo json_encode(array_values($daily_data)); ?>;
              const labels = <?php echo json_encode(array_map(fn($d)=>date('M j',strtotime($d)), array_keys($daily_data))); ?>;
              const max = Math.max(...data, 1);
              const wrap = document.getElementById('barChart');
              wrap.style.cssText = 'display:flex;align-items:flex-end;gap:10px;height:140px;margin-top:.5rem';
              labels.forEach((lbl,i)=>{
                const col = document.createElement('div');
                col.style.cssText = 'flex:1;display:flex;flex-direction:column;align-items:center;gap:4px';
                const h = Math.max((data[i]/max)*120, data[i]>0?8:2);
                col.innerHTML = `<span style="font-size:.72rem;color:var(--muted);font-weight:700">${data[i]||''}</span>
                  <div style="width:100%;height:${h}px;background:var(--green-400);border-radius:6px 6px 0 0;transition:height .6s"></div>
                  <span style="font-size:.68rem;color:var(--muted);text-align:center;line-height:1.2">${lbl}</span>`;
                wrap.appendChild(col);
              });
            </script>
            <?php else: ?>
            <div class="empty-state" style="padding:2rem">
              <div class="empty-state__icon"></div>
              <p>No data yet — connect Arduino to begin.</p>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- BIN STATUS -->
        <div class="panel">
          <div class="panel-header">
            <span class="panel-title"> Bin Fill Status</span>
            <a href="machine.php" class="btn btn--ghost btn--sm">View Machine →</a>
          </div>
          <div class="panel-body">
            <?php if($connected): ?>
            <div class="bin-wrap">
              <div>
                <div class="bin-visual">
                  <?php $bc = binColor($bin_level); ?>
                  <div class="bin-fill bin-fill--<?php echo $bc; ?>" style="height:<?php echo $bin_level; ?>%"></div>
                  <div class="bin-label"><?php echo $bin_level; ?>%</div>
                </div>
              </div>
              <div>
                <h4 style="margin-bottom:.4rem">node_001 — Barangay Muzon</h4>
                <div class="progress-bar" style="width:200px">
                  <div class="progress-fill progress-fill--<?php echo $bc==='ok'?'':$bc; ?>" style="width:<?php echo $bin_level; ?>%"></div>
                </div>
                <p style="font-size:.85rem;margin-top:.4rem">
                  <?php if($bin_level>=90) echo ' <strong>Bin full</strong> — empty immediately.';
                        elseif($bin_level>=70) echo ' <strong>Bin filling</strong> — plan to empty soon.';
                        else echo ' Bin level is acceptable.'; ?>
                </p>
                <p style="font-size:.78rem;margin-top:.6rem;color:var(--muted)">Accepted: <?php echo number_format($total_bottles); ?> bottles · Rejected: <?php echo number_format($rejected); ?></p>
              </div>
            </div>
            <?php else: ?>
            <div class="empty-state" style="padding:2rem"><div class="empty-state__icon"></div><p>Awaiting database connection.</p></div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- RECENT TRANSACTIONS -->
      <div class="panel">
        <div class="panel-header">
          <span class="panel-title"> Recent Transactions</span>
          <a href="transactions.php" class="btn btn--outline btn--sm">View All →</a>
        </div>
        <?php if($connected && count($recent_tx)): ?>
        <div class="table-wrap">
          <table>
            <thead><tr><th>#</th><th>Date & Time</th><th>Bottles</th><th>Reward</th><th>Node</th><th>Status</th></tr></thead>
            <tbody>
              <?php foreach($recent_tx as $tx): ?>
              <tr>
                <td class="td-mono"><?php echo $tx['id']; ?></td>
                <td><?php echo date('M j, Y H:i:s', strtotime($tx['created_at'])); ?></td>
                <td><?php echo $tx['bottle_count'] ?? 1; ?></td>
                <td><?php echo $tx['reward_amount'] ?? '—'; ?></td>
                <td><span class="badge badge--muted"><?php echo htmlspecialchars($tx['node_id'] ?? 'node_001'); ?></span></td>
                <td><span class="badge badge--<?php echo strtolower($tx['status']==='Accepted'?'green':'red'); ?>"><?php echo htmlspecialchars($tx['status']); ?></span></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
        <div class="empty-state"><div class="empty-state__icon"></div><h4>No transactions yet</h4><p>Transactions will appear here once Arduino is connected.</p></div>
        <?php endif; ?>
      </div>

    </div><!-- main-content -->
  </div><!-- main-area -->
</div>
<script src="admin.js"></script>
</body>
</html>
