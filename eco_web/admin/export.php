<?php

require 'auth.php';
$db = bb_db();
$connected = $db !== null;

// Handle CSV download
if ($connected && isset($_GET['download'])) {
    $type = $_GET['download'];

    if ($type === 'transactions') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="bottleback_transactions_'.date('Y-m-d').'.csv"');
        $out = fopen('php://output','w');
        fputcsv($out, ['ID','Date','Time','Bottles','Reward','Status','Node']);
        $r = $db->query("SELECT * FROM transactions ORDER BY created_at DESC");
        while ($row=$r->fetch_assoc()) {
            fputcsv($out, [
                $row['id'],
                date('Y-m-d',strtotime($row['created_at'])),
                date('H:i:s',strtotime($row['created_at'])),
                $row['bottle_count'],
                $row['reward_amount'],
                $row['status'],
                $row['node_id'],
            ]);
        }
        fclose($out); exit;
    }

    if ($type === 'users') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="bottleback_users_'.date('Y-m-d').'.csv"');
        $out = fopen('php://output','w');
        fputcsv($out, ['ID','First Name','Last Name','Email','Barangay','Total Bottles','Total Rewards','Status','Joined']);
        $r = $db->query("SELECT * FROM users ORDER BY created_at DESC");
        while ($row=$r->fetch_assoc()) {
            fputcsv($out, [
                $row['id'], $row['first_name'], $row['last_name'],
                $row['email'], $row['barangay'],
                $row['total_bottles'], $row['total_rewards'],
                $row['is_active']?'Active':'Inactive',
                date('Y-m-d H:i', strtotime($row['created_at'])),
            ]);
        }
        fclose($out); exit;
    }

    if ($type === 'messages') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="bottleback_messages_'.date('Y-m-d').'.csv"');
        $out = fopen('php://output','w');
        fputcsv($out, ['ID','Date','Name','Email','Subject','Message']);
        $r = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
        while ($row=$r->fetch_assoc()) {
            fputcsv($out, [$row['id'],date('Y-m-d H:i',strtotime($row['created_at'])),$row['name'],$row['email'],$row['subject'],$row['message']]);
        }
        fclose($out); exit;
    }
}


$stats = [];
if ($connected) {
    $r = $db->query("SELECT COUNT(*) c FROM transactions"); $stats['total_tx'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
    $r = $db->query("SELECT COUNT(*) c FROM transactions WHERE status='Accepted'"); $stats['accepted'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
    $r = $db->query("SELECT COUNT(*) c FROM transactions WHERE status='Rejected'"); $stats['rejected'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
    $r = $db->query("SELECT COALESCE(SUM(reward_amount),0) c FROM transactions"); $stats['rewards'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
    $r = $db->query("SELECT COUNT(*) c FROM contact_messages"); $stats['messages'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
    $r = $db->query("SELECT COUNT(*) c FROM users"); $stats['users'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Export Data — BottleBack Admin</title>
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
          <div class="label">Data</div>
          <h1> Export <em>Data</em></h1>
        </div>
      </div>

      <?php if(!$connected): ?><div class="alert alert--warn">⚠️ Database not connected. Connect first to export data.</div><?php endif; ?>

      <div class="grid-2">

        
        <div class="panel">
          <div class="panel-header"><span class="panel-title">🍶 Transactions Export</span></div>
          <div class="panel-body">
            <p style="margin-bottom:1.2rem;font-size:.9rem">Export all bottle transaction records as a CSV file. Includes transaction ID, date/time, bottle count, reward amount, status, and node.</p>
            <div style="background:var(--off-white);border:1px solid var(--border);border-radius:10px;padding:.9rem 1.1rem;margin-bottom:1.2rem;font-size:.85rem">
              <strong>Columns:</strong> ID, Date, Time, Bottles, Reward, Status, Node
            </div>
            <?php if($connected): ?>
            <p style="font-size:.82rem;color:var(--muted);margin-bottom:1rem">📦 <?php echo number_format($stats['total_tx']); ?> records · <?php echo number_format($stats['accepted']); ?> accepted · <?php echo number_format($stats['rejected']); ?> rejected</p>
            <a href="?download=transactions" class="btn btn--primary">⬇️ Download Transactions CSV</a>
            <?php else: ?>
            <button class="btn btn--primary" disabled>⬇️ Download Transactions CSV</button>
            <?php endif; ?>
          </div>
        </div>

       
        <div class="panel">
          <div class="panel-header"><span class="panel-title">✉️ Messages Export</span></div>
          <div class="panel-body">
            <p style="margin-bottom:1.2rem;font-size:.9rem">Export all contact form messages as a CSV file. Includes ID, date, name, email, subject, and message body.</p>
            <div style="background:var(--off-white);border:1px solid var(--border);border-radius:10px;padding:.9rem 1.1rem;margin-bottom:1.2rem;font-size:.85rem">
              <strong>Columns:</strong> ID, Date, Name, Email, Subject, Message
            </div>
            <?php if($connected): ?>
            <p style="font-size:.82rem;color:var(--muted);margin-bottom:1rem">📨 <?php echo number_format($stats['messages']); ?> messages total</p>
            <a href="?download=messages" class="btn btn--primary">⬇️ Download Messages CSV</a>
            <?php else: ?>
            <button class="btn btn--primary" disabled>⬇️ Download Messages CSV</button>
            <?php endif; ?>
          </div>
        </div>

      </div>

    
      <div class="panel" style="margin-bottom:1.6rem">
        <div class="panel-header"><span class="panel-title">👥 Users Export</span></div>
        <div class="panel-body" style="display:flex;align-items:flex-start;gap:2rem;flex-wrap:wrap">
          <div style="flex:1;min-width:220px">
            <p style="font-size:.9rem;margin-bottom:.8rem">Export all registered resident accounts as a CSV file. Includes name, email, barangay, bottle totals, and account status.</p>
            <div style="background:var(--off-white);border:1px solid var(--border);border-radius:10px;padding:.9rem 1.1rem;font-size:.85rem">
              <strong>Columns:</strong> ID, First Name, Last Name, Email, Barangay, Total Bottles, Total Rewards, Status, Joined
            </div>
          </div>
          <div style="flex-shrink:0;padding-top:.4rem">
            <?php if($connected): ?>
            <p style="font-size:.82rem;color:var(--muted);margin-bottom:.8rem">👥 <?php echo number_format($stats['users']); ?> registered users</p>
            <a href="?download=users" class="btn btn--primary">⬇️ Download Users CSV</a>
            <?php else: ?>
            <button class="btn btn--primary" disabled>⬇️ Download Users CSV</button>
            <?php endif; ?>
          </div>
        </div>
      </div>

      
      <?php if($connected): ?>
      <div class="panel">
        <div class="panel-header"><span class="panel-title"> Database Summary</span></div>
        <div class="panel-body">
          <div class="kpi-grid" style="margin-bottom:0">
            <div class="kpi-card kpi-card--green">
              <span class="kpi-icon"></span>
              <div><div class="kpi-label">Total Transactions</div><div class="kpi-value"><?php echo number_format($stats['total_tx']); ?></div></div>
            </div>
            <div class="kpi-card kpi-card--green">
              <span class="kpi-icon"></span>
              <div><div class="kpi-label">Accepted</div><div class="kpi-value"><?php echo number_format($stats['accepted']); ?></div></div>
            </div>
            <div class="kpi-card kpi-card--red">
              <span class="kpi-icon"></span>
              <div><div class="kpi-label">Rejected</div><div class="kpi-value"><?php echo number_format($stats['rejected']); ?></div></div>
            </div>
            <div class="kpi-card kpi-card--yellow">
              <span class="kpi-icon"></span>
              <div><div class="kpi-label">Total Rewards</div><div class="kpi-value"><?php echo number_format($stats['rewards']); ?></div></div>
            </div>
            <div class="kpi-card kpi-card--teal">
              <span class="kpi-icon"></span>
              <div><div class="kpi-label">Messages</div><div class="kpi-value"><?php echo number_format($stats['messages']); ?></div></div>
            </div>
            <div class="kpi-card kpi-card--blue">
              <span class="kpi-icon"></span>
              <div><div class="kpi-label">Users</div><div class="kpi-value"><?php echo number_format($stats['users']); ?></div></div>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>

    </div>
  </div>
</div>
<script src="admin.js"></script>
</body>
</html>
