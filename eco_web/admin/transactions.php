<?php

require 'auth.php';
$db = bb_db();
$connected = $db !== null;

// Handle delete
$msg = '';
if ($connected && $_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_id'])) {
    $id = intval($_POST['delete_id']);
    $st = $db->prepare("DELETE FROM transactions WHERE id=?");
    $st->bind_param('i', $id);
    $st->execute();
    $msg = $st->affected_rows ? 'success' : 'error';
    $st->close();
}

// Filters
$status_filter = $_GET['status'] ?? '';
$search        = $_GET['q'] ?? '';
$page          = max(1, intval($_GET['p'] ?? 1));
$per_page      = 20;

$where = [];
$params = []; $types = '';
if ($status_filter && in_array($status_filter, ['Accepted','Rejected'])) {
    $where[] = "status=?"; $params[] = $status_filter; $types .= 's';
}
if ($search) {
    $like = "%$search%";
    $where[] = "(node_id LIKE ? OR id LIKE ?)"; $params[] = $like; $params[] = $like; $types .= 'ss';
}
$where_sql = $where ? 'WHERE '.implode(' AND ', $where) : '';

$total = 0; $transactions = [];
if ($connected) {
    $r = $db->query("SELECT COUNT(*) c FROM transactions $where_sql");
    // For simplicity without prepared count (only safe filters used above):
    if ($where) {
        $st = $db->prepare("SELECT COUNT(*) c FROM transactions $where_sql");
        $st->bind_param($types, ...$params);
        $st->execute();
        $total = (int)$st->get_result()->fetch_assoc()['c'];
        $st->close();
    } else {
        $r = $db->query("SELECT COUNT(*) c FROM transactions");
        $total = $r ? (int)$r->fetch_assoc()['c'] : 0;
    }
    $offset = ($page-1)*$per_page;
    $sql = "SELECT * FROM transactions $where_sql ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
    if ($where) {
        $st = $db->prepare($sql);
        $st->bind_param($types, ...$params);
        $st->execute();
        $res = $st->get_result();
        while ($row=$res->fetch_assoc()) $transactions[] = $row;
        $st->close();
    } else {
        $r = $db->query($sql);
        if ($r) while ($row=$r->fetch_assoc()) $transactions[] = $row;
    }
}
$total_pages = max(1, ceil($total / $per_page));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Transactions — BottleBack Admin</title>
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
          <div class="label">Records</div>
          <h1> <em>Transactions</em></h1>
        </div>
        
      </div>

      <?php if($msg==='success'): ?><div class="alert alert--success"> Transaction deleted.</div><?php endif; ?>
      <?php if($msg==='error'): ?><div class="alert alert--error"> Could not delete transaction.</div><?php endif; ?>
      <?php if(!$connected): ?><div class="alert alert--warn"> Database not connected.</div><?php endif; ?>

      <div class="panel">
        <div class="panel-header">
          <span class="panel-title">All Transactions <span style="font-size:.82rem;font-family:var(--font-sans);font-weight:400;color:var(--muted)">(<?php echo number_format($total); ?> records)</span></span>
        </div>
        <div class="panel-body" style="padding-bottom:0">
          <!-- FILTERS -->
          <form method="GET" class="filter-bar">
            <input type="text" name="q" class="form-input search-input" placeholder=" Search by ID or node…" value="<?php echo htmlspecialchars($search); ?>">
            <select name="status" class="form-select" style="max-width:160px">
              <option value="">All Statuses</option>
              <option value="Accepted" <?php echo $status_filter==='Accepted'?'selected':''; ?>> Accepted</option>
              <option value="Rejected" <?php echo $status_filter==='Rejected'?'selected':''; ?>> Rejected</option>
            </select>
            <button type="submit" class="btn btn--primary btn--sm">Filter</button>
            <?php if($search||$status_filter): ?><a href="transactions.php" class="btn btn--ghost btn--sm">Clear</a><?php endif; ?>
          </form>
        </div>

        <?php if($connected && count($transactions)): ?>
        <div class="table-wrap">
          <table>
            <thead>
              <tr><th>#</th><th>Date & Time</th><th>Bottles</th><th>Reward</th><th>Node</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
              <?php foreach($transactions as $tx): ?>
              <tr>
                <td class="td-mono"><?php echo $tx['id']; ?></td>
                <td><?php echo date('M j, Y H:i:s', strtotime($tx['created_at'])); ?></td>
                <td><?php echo $tx['bottle_count'] ?? 1; ?></td>
                <td><?php echo $tx['reward_amount'] ?? '—'; ?></td>
                <td><span class="badge badge--muted"><?php echo htmlspecialchars($tx['node_id'] ?? ''); ?></span></td>
                <td><span class="badge badge--<?php echo $tx['status']==='Accepted'?'green':'red'; ?>"><?php echo htmlspecialchars($tx['status']); ?></span></td>
                <td>
                  <form method="POST" onsubmit="return confirm('Delete transaction #<?php echo $tx['id']; ?>?')">
                    <input type="hidden" name="delete_id" value="<?php echo $tx['id']; ?>">
                    <button type="submit" class="btn btn--danger btn--sm"></button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- PAGINATION -->
        <div class="panel-body" style="padding-top:1rem">
          <div class="pagination">
            <?php
            $qs = http_build_query(['q'=>$search,'status'=>$status_filter]);
            for($i=1;$i<=$total_pages;$i++):
              $active = $i===$page?'page-btn--active':'';
            ?>
            <a href="?p=<?php echo $i; ?>&<?php echo $qs; ?>" class="page-btn <?php echo $active; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            <span style="font-size:.78rem;color:var(--muted);margin-left:.5rem">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
          </div>
        </div>

        <?php else: ?>
        <div class="empty-state"><div class="empty-state__icon"></div><h4>No transactions found</h4><p>Connect your Arduino or adjust your filters.</p></div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>
<script src="admin.js"></script>
</body>
</html>
