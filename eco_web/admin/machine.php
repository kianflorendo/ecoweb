<?php

require 'auth.php';
$db = bb_db();
$connected = $db !== null;

$msg = '';

// Manual bin level update
if ($connected && $_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_bin'])) {
    $level = max(0, min(100, intval($_POST['bin_level'])));
    $node  = trim($_POST['node_id'] ?? 'node_001');
    $st = $db->prepare("UPDATE machine_status SET bin_level=?, updated_at=NOW() WHERE node_id=?");
    $st->bind_param('is', $level, $node);
    $st->execute();
    if ($st->affected_rows === 0) {
        // Insert if not exists
        $st2 = $db->prepare("INSERT INTO machine_status (node_id, bin_level, is_online) VALUES (?,?,1)");
        $st2->bind_param('si', $node, $level);
        $st2->execute();
        $st2->close();
    }
    $st->close();
    $msg = 'updated';
}

// Toggle online status
if ($connected && $_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['toggle_online'])) {
    $online = intval($_POST['is_online']);
    $node   = trim($_POST['node_id'] ?? 'node_001');
    $st = $db->prepare("UPDATE machine_status SET is_online=?, updated_at=NOW() WHERE node_id=?");
    $st->bind_param('is', $online, $node);
    $st->execute();
    $st->close();
    $msg = 'updated';
}

$machines = [];
if ($connected) {
    $r = $db->query("SELECT * FROM machine_status ORDER BY updated_at DESC");
    if ($r) while ($row=$r->fetch_assoc()) $machines[] = $row;
}

function binColor($l){ if($l>=90)return 'danger'; if($l>=70)return 'warn'; return 'ok'; }
function binLabel($l){ if($l>=90)return '🔴 FULL'; if($l>=70)return '🟡 HIGH'; return '🟢 OK'; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Machine Status — BottleBack Admin</title>
  <link rel="stylesheet" href="admin.css"/>
</head>
<body>
<div class="admin-layout">
  <div class="sidebar-overlay" id="overlay"></div>
  <?php include 'partials/nav.php'; ?>
  <div class="main-area">
    <div class="main-content">

      <div class="page-header">
        <div class="page-header__text">
          <div class="label">Hardware</div>
          <h1> Machine <em>Status</em></h1>
        </div>
      </div>

      <?php if($msg==='updated'): ?><div class="alert alert--success"> Machine status updated.</div><?php endif; ?>
      <?php if(!$connected): ?><div class="alert alert--warn"> Database not connected.</div><?php endif; ?>

      <!-- HOW ARDUINO CONNECTS -->
      <div class="panel" style="margin-bottom:1.6rem">
        <div class="panel-header"><span class="panel-title"> Arduino Connection Guide</span></div>
        <div class="panel-body">
          <div class="grid-2">
            <div>
              <h4 style="margin-bottom:.6rem">API Endpoint</h4>
              <p style="font-size:.88rem;margin-bottom:.8rem">The Arduino sends POST data via a Python serial bridge to:</p>
              <code style="display:block;background:var(--green-900);color:var(--green-300);padding:.9rem 1.1rem;border-radius:10px;font-size:.82rem;line-height:1.7">
                POST http://localhost/eco_web/api/receive_data.php<br><br>
                Fields:<br>
                &nbsp;bottle_count  — int (default 1)<br>
                &nbsp;reward_amount — int (default 1)<br>
                &nbsp;status        — Accepted | Rejected<br>
                &nbsp;bin_level     — int 0–100<br>
                &nbsp;node_id       — string (default node_001)
              </code>
            </div>
            <div>
              <h4 style="margin-bottom:.6rem">Quick Test (curl)</h4>
              <p style="font-size:.88rem;margin-bottom:.8rem">Simulate an Arduino POST to test the endpoint:</p>
              <code style="display:block;background:var(--green-900);color:var(--green-300);padding:.9rem 1.1rem;border-radius:10px;font-size:.82rem;line-height:1.7;word-break:break-all">
                curl -X POST \<br>
                &nbsp;http://localhost/eco_web/api/receive_data.php \<br>
                &nbsp;-d "bottle_count=1&reward_amount=1<br>
                &nbsp;&status=Accepted&bin_level=25<br>
                &nbsp;&node_id=node_001"
              </code>
            </div>
          </div>
        </div>
      </div>

      <!-- MACHINE NODES -->
      <?php if($connected && count($machines)): ?>
      <?php foreach($machines as $m): ?>
      <?php $bc = binColor($m['bin_level']); ?>
      <div class="panel">
        <div class="panel-header">
          <span class="panel-title"> <?php echo htmlspecialchars($m['node_id']); ?> — Barangay Muzon</span>
          <span class="badge badge--<?php echo $m['is_online']?'green':'red'; ?>">
            <?php echo $m['is_online']?'● Online':'○ Offline'; ?>
          </span>
        </div>
        <div class="panel-body">
          <div style="display:flex;gap:3rem;flex-wrap:wrap;align-items:flex-start">

            <!-- BIN VISUAL -->
            <div style="display:flex;flex-direction:column;align-items:center;gap:.6rem">
              <div class="bin-visual" style="width:72px;height:160px">
                <div class="bin-fill bin-fill--<?php echo $bc; ?>" style="height:<?php echo $m['bin_level']; ?>%"></div>
                <div class="bin-label"><?php echo $m['bin_level']; ?>%</div>
              </div>
              <span style="font-size:.78rem;color:var(--muted)">Bin Fill</span>
            </div>

            <!-- STATS -->
            <div style="flex:1;min-width:220px">
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.2rem">
                <div class="kpi-card" style="padding:1rem">
                  <span class="kpi-icon" style="font-size:1.4rem"></span>
                  <div>
                    <div class="kpi-label">Fill Level</div>
                    <div class="kpi-value" style="font-size:1.5rem"><?php echo $m['bin_level']; ?>%</div>
                  </div>
                </div>
                <div class="kpi-card" style="padding:1rem">
                  <span class="kpi-icon" style="font-size:1.4rem"><?php echo $m['is_online']?'🟢':'🔴'; ?></span>
                  <div>
                    <div class="kpi-label">Status</div>
                    <div class="kpi-value" style="font-size:1.1rem"><?php echo $m['is_online']?'Online':'Offline'; ?></div>
                  </div>
                </div>
              </div>
              <div class="progress-bar">
                <div class="progress-fill progress-fill--<?php echo $bc==='ok'?'':$bc; ?>" style="width:<?php echo $m['bin_level']; ?>%"></div>
              </div>
              <p style="font-size:.8rem;margin:.4rem 0 .8rem"><?php echo binLabel($m['bin_level']); ?> — Last updated: <?php echo date('M j, Y H:i:s', strtotime($m['updated_at'])); ?></p>

              <!-- UPDATE BIN FORM -->
              <form method="POST" style="display:flex;gap:.6rem;align-items:flex-end;flex-wrap:wrap">
                <input type="hidden" name="node_id" value="<?php echo htmlspecialchars($m['node_id']); ?>">
                <div class="form-group" style="margin:0">
                  <label class="form-label" style="margin-bottom:.3rem">Set Bin Level (%)</label>
                  <input type="number" name="bin_level" class="form-input" style="max-width:120px" min="0" max="100" value="<?php echo $m['bin_level']; ?>">
                </div>
                <button type="submit" name="update_bin" class="btn btn--primary btn--sm" style="margin-bottom:0">Update</button>
              </form>
            </div>

            <!-- TOGGLE ONLINE -->
            <div>
              <h4 style="margin-bottom:.8rem">Online Status</h4>
              <form method="POST" style="display:flex;gap:.6rem">
                <input type="hidden" name="node_id" value="<?php echo htmlspecialchars($m['node_id']); ?>">
                <?php if($m['is_online']): ?>
                  <input type="hidden" name="is_online" value="0">
                  <button type="submit" name="toggle_online" class="btn btn--danger btn--sm">Set Offline</button>
                <?php else: ?>
                  <input type="hidden" name="is_online" value="1">
                  <button type="submit" name="toggle_online" class="btn btn--primary btn--sm">Set Online</button>
                <?php endif; ?>
              </form>
            </div>

          </div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php elseif($connected): ?>
      <div class="panel"><div class="empty-state"><div class="empty-state__icon"></div><h4>No machine nodes found</h4><p>Run <code>setup.sql</code> to seed the initial machine row.</p></div></div>
      <?php endif; ?>

    </div>
  </div>
</div>
<script src="admin.js"></script>
</body>
</html>
