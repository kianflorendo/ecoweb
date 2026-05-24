<?php

$page_title = "Live Dashboard | BottleBack";

define('DB_HOST','localhost'); define('DB_USER','root');
define('DB_PASS','');          define('DB_NAME','bottleback');

$db_connected = false; $today_bottles = 0; $today_rewards = 0;
$total_bottles = 0; $bin_level = null; $transactions = [];

try {
  $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  if (!$conn->connect_error) {
    $db_connected = true;
    $r = $conn->query("SELECT COUNT(*) AS cnt FROM transactions WHERE DATE(created_at)=CURDATE() AND status='Accepted'");
    if ($r) $today_bottles = $r->fetch_assoc()['cnt'];
    $r = $conn->query("SELECT SUM(reward_amount) AS tot FROM transactions WHERE DATE(created_at)=CURDATE()");
    if ($r) $today_rewards = (int)$r->fetch_assoc()['tot'];
    $r = $conn->query("SELECT COUNT(*) AS cnt FROM transactions WHERE status='Accepted'");
    if ($r) $total_bottles = $r->fetch_assoc()['cnt'];
    $r = $conn->query("SELECT bin_level FROM machine_status ORDER BY updated_at DESC LIMIT 1");
    if ($r && $r->num_rows>0) $bin_level = $r->fetch_assoc()['bin_level'];
    $r = $conn->query("SELECT * FROM transactions ORDER BY created_at DESC LIMIT 15");
    if ($r) while($row=$r->fetch_assoc()) $transactions[] = $row;
    $conn->close();
  }
} catch(Exception $e){ $db_connected = false; }

function binColor($l){ if($l===null)return 'pending'; if($l>=90)return 'danger'; if($l>=70)return 'warn'; return 'ok'; }
function binLabel($l){ if($l===null)return '—'; if($l>=90)return 'FULL'; if($l>=70)return 'HIGH'; return $l.'%'; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo $page_title; ?></title>
  <link rel="stylesheet" href="../css/style.css"/>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body class="body--dark">
<?php include '../includes/nav.php'; ?>

<section class="page-hero">
  <div class="container">
    <div class="section-label section-label--light">Real-Time Monitoring</div>
    <h1 class="page-hero__title">Machine <em>Dashboard</em></h1>
    <p class="page-hero__sub">
      <?php if($db_connected): ?>
         Live — Barangay Muzon Node &nbsp;·&nbsp; Last refresh: <?php echo date('M j, Y H:i:s'); ?>
      <?php else: ?>
        🔌 Database not yet connected — dashboard is ready and waiting for Arduino data.
      <?php endif; ?>
    </p>
  </div>
</section>

<section class="section section--dark dashboard-section">
  <div class="container">

    <?php if(!$db_connected): ?>
    <div class="alert-banner alert-banner--info">
      <span></span>
      <div>
        <strong>Setup Required</strong>
        Start XAMPP → create the <code>bottleback</code> database → run <code>database/setup.sql</code> → connect your Arduino.
        All dashboard cards will populate automatically — no code changes needed.
      </div>
    </div>
    <?php endif; ?>

    <!-- KPI CARDS -->
    <div class="kpi-grid">
      <div class="kpi-card kpi-card--green">
        <div class="kpi-icon"></div>
        <div class="kpi-body">
          <div class="kpi-label">Bottles Accepted Today</div>
          <div class="kpi-value"><?php echo $db_connected ? $today_bottles : '—'; ?></div>
        </div>
      </div>
      <div class="kpi-card kpi-card--yellow">
        <div class="kpi-icon"></div>
        <div class="kpi-body">
          <div class="kpi-label">Rewards Dispensed Today</div>
          <div class="kpi-value"><?php echo $db_connected ? $today_rewards : '—'; ?></div>
        </div>
      </div>
      <div class="kpi-card kpi-card--blue">
        <div class="kpi-icon"></div>
        <div class="kpi-body">
          <div class="kpi-label">Total Bottles (All-Time)</div>
          <div class="kpi-value"><?php echo $db_connected ? number_format($total_bottles) : '—'; ?></div>
        </div>
      </div>
      <div class="kpi-card kpi-card--<?php echo binColor($bin_level); ?>">
        <div class="kpi-icon"></div>
        <div class="kpi-body">
          <div class="kpi-label">Bin Fill Level</div>
          <div class="kpi-value"><?php echo binLabel($bin_level); ?></div>
        </div>
      </div>
    </div>

    <?php if($db_connected && $bin_level !== null): ?>
    <div class="bin-visual-wrap">
      <div class="bin-visual">
        <div class="bin-fill" style="height:<?php echo $bin_level; ?>%" data-level="<?php echo $bin_level; ?>"></div>
        <div class="bin-label-inside"><?php echo $bin_level; ?>%</div>
      </div>
      <div class="bin-caption">
        <strong>Collection Bin — Barangay Muzon Node</strong>
        <p><?php echo $bin_level>=90 ? ' Bin full — please empty before accepting more bottles.' : ($bin_level>=70 ? ' Bin filling up. Plan to empty soon.' : ' Bin level is acceptable.'); ?></p>
      </div>
    </div>
    <?php endif; ?>

    <!-- TRANSACTIONS TABLE -->
    <div class="data-history">
      <div class="data-history-header">
        <h3>Recent Transactions</h3>
        <?php if($db_connected && count($transactions)>0): ?>
        <span class="live-badge"><span class="pulse-dot pulse-dot--sm"></span> Live</span>
        <?php endif; ?>
      </div>

      <?php if($db_connected && count($transactions)>0): ?>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr><th>#</th><th>Date &amp; Time</th><th>Bottles</th><th>Reward</th><th>Status</th></tr>
          </thead>
          <tbody>
            <?php foreach($transactions as $tx): ?>
            <tr>
              <td><?php echo $tx['id']; ?></td>
              <td><?php echo date('M j, Y H:i:s', strtotime($tx['created_at'])); ?></td>
              <td><?php echo htmlspecialchars($tx['bottle_count'] ?? 1); ?></td>
              <td><?php echo htmlspecialchars($tx['reward_amount'] ?? '—'); ?></td>
              <td><span class="status-badge status-badge--<?php echo strtolower($tx['status'] ?? 'accepted'); ?>"><?php echo htmlspecialchars($tx['status'] ?? 'Accepted'); ?></span></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <?php else: ?>
      <div class="empty-state">
        <div class="empty-state__icon"></div>
        <h4>No transactions yet</h4>
        <p>Bottle transactions will appear here once the Arduino is connected and the database is set up.</p>
        <div class="db-setup">
          <h5> Quick Setup Steps</h5>
          <ol>
            <li>Open <strong>XAMPP Control Panel</strong> → Start <strong>Apache</strong> and <strong>MySQL</strong></li>
            <li>Go to <strong>phpMyAdmin</strong> → Create database: <code>bottleback</code></li>
            <li>Import / run <code>database/setup.sql</code></li>
            <li>Connect Arduino and run the Python serial bridge script</li>
            <li>Refresh this page — all data will appear automatically</li>
          </ol>
        </div>
      </div>
      <?php endif; ?>
    </div>

  </div>
</section>

<?php include '../includes/footer.php'; ?>
<script src="../js/main.js"></script>
</body>
</html>
