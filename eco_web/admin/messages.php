<?php

require 'auth.php';
$db = bb_db();
$connected = $db !== null;

$msg_notice = '';

// Delete message
if ($connected && $_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_id'])) {
    $id = intval($_POST['delete_id']);
    $st = $db->prepare("DELETE FROM contact_messages WHERE id=?");
    $st->bind_param('i', $id);
    $st->execute();
    $msg_notice = $st->affected_rows ? 'deleted' : 'error';
    $st->close();
}

// View single message
$view_msg = null;
if ($connected && isset($_GET['view'])) {
    $id = intval($_GET['view']);
    $st = $db->prepare("SELECT * FROM contact_messages WHERE id=?");
    $st->bind_param('i', $id);
    $st->execute();
    $view_msg = $st->get_result()->fetch_assoc();
    $st->close();
}

// List
$page = max(1, intval($_GET['p'] ?? 1));
$per_page = 15;
$search = $_GET['q'] ?? '';
$total = 0; $messages = [];

if ($connected) {
    if ($search) {
        $like = "%$search%";
        $st = $db->prepare("SELECT COUNT(*) c FROM contact_messages WHERE name LIKE ? OR email LIKE ? OR subject LIKE ?");
        $st->bind_param('sss',$like,$like,$like);
        $st->execute();
        $total = (int)$st->get_result()->fetch_assoc()['c'];
        $st->close();
        $offset = ($page-1)*$per_page;
        $st = $db->prepare("SELECT * FROM contact_messages WHERE name LIKE ? OR email LIKE ? OR subject LIKE ? ORDER BY created_at DESC LIMIT $per_page OFFSET $offset");
        $st->bind_param('sss',$like,$like,$like);
        $st->execute();
        $r = $st->get_result();
        while ($row=$r->fetch_assoc()) $messages[] = $row;
        $st->close();
    } else {
        $r = $db->query("SELECT COUNT(*) c FROM contact_messages");
        $total = $r ? (int)$r->fetch_assoc()['c'] : 0;
        $offset = ($page-1)*$per_page;
        $r = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT $per_page OFFSET $offset");
        if ($r) while ($row=$r->fetch_assoc()) $messages[] = $row;
    }
}
$total_pages = max(1, ceil($total/$per_page));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Messages — BottleBack Admin</title>
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
          <div class="label">Inbox</div>
          <h1>✉️ Contact <em>Messages</em></h1>
        </div>
        <a href="export.php?type=messages" class="btn btn--outline btn--sm">📥 Export CSV</a>
      </div>

      <?php if($msg_notice==='deleted'): ?><div class="alert alert--success">✅ Message deleted.</div><?php endif; ?>
      <?php if(!$connected): ?><div class="alert alert--warn">⚠️ Database not connected.</div><?php endif; ?>

      <!-- VIEW SINGLE MESSAGE MODAL -->
      <?php if($view_msg): ?>
      <div class="panel" style="border-color:var(--green-300);margin-bottom:1.6rem">
        <div class="panel-header" style="background:var(--green-50)">
          <span class="panel-title">📨 Message #<?php echo $view_msg['id']; ?></span>
          <a href="messages.php" class="btn btn--ghost btn--sm">✕ Close</a>
        </div>
        <div class="panel-body">
          <div class="grid-2" style="margin-bottom:1rem">
            <div>
              <p class="form-label">From</p>
              <p style="font-weight:600;color:var(--ink)"><?php echo htmlspecialchars($view_msg['name']); ?></p>
              <p style="font-size:.85rem"><?php echo htmlspecialchars($view_msg['email']); ?></p>
            </div>
            <div>
              <p class="form-label">Received</p>
              <p style="font-size:.9rem"><?php echo date('F j, Y \a\t H:i', strtotime($view_msg['created_at'])); ?></p>
            </div>
          </div>
          <div style="margin-bottom:1rem">
            <p class="form-label">Subject</p>
            <p style="font-weight:600;color:var(--ink)"><?php echo htmlspecialchars($view_msg['subject']); ?></p>
          </div>
          <div>
            <p class="form-label">Message</p>
            <div style="background:var(--off-white);border:1px solid var(--border);border-radius:10px;padding:1rem;font-size:.92rem;line-height:1.7;white-space:pre-wrap"><?php echo htmlspecialchars($view_msg['message']); ?></div>
          </div>
          <div style="margin-top:1rem;display:flex;gap:.8rem">
            <a href="mailto:<?php echo htmlspecialchars($view_msg['email']); ?>?subject=Re: <?php echo urlencode($view_msg['subject']); ?>" class="btn btn--primary btn--sm">📧 Reply via Email</a>
            <form method="POST" onsubmit="return confirm('Delete this message?')" style="display:inline">
              <input type="hidden" name="delete_id" value="<?php echo $view_msg['id']; ?>">
              <button type="submit" class="btn btn--danger btn--sm">🗑 Delete</button>
            </form>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <div class="panel">
        <div class="panel-header">
          <span class="panel-title">All Messages <span style="font-size:.82rem;font-family:var(--font-sans);font-weight:400;color:var(--muted)">(<?php echo number_format($total); ?> total)</span></span>
        </div>
        <div class="panel-body" style="padding-bottom:0">
          <form method="GET" class="filter-bar">
            <input type="text" name="q" class="form-input search-input" placeholder="🔍 Search by name, email, subject…" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn--primary btn--sm">Search</button>
            <?php if($search): ?><a href="messages.php" class="btn btn--ghost btn--sm">Clear</a><?php endif; ?>
          </form>
        </div>

        <?php if($connected && count($messages)): ?>
        <div class="table-wrap">
          <table>
            <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Subject</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody>
              <?php foreach($messages as $m): ?>
              <tr>
                <td class="td-mono"><?php echo $m['id']; ?></td>
                <td><strong style="color:var(--ink)"><?php echo htmlspecialchars($m['name']); ?></strong></td>
                <td style="font-size:.85rem"><?php echo htmlspecialchars($m['email']); ?></td>
                <td><?php echo htmlspecialchars(mb_strimwidth($m['subject'],0,40,'…')); ?></td>
                <td style="font-size:.82rem;color:var(--muted)"><?php echo date('M j, Y H:i', strtotime($m['created_at'])); ?></td>
                <td>
                  <div style="display:flex;gap:.4rem">
                    <a href="?view=<?php echo $m['id']; ?>" class="btn btn--outline btn--sm">👁 View</a>
                    <a href="mailto:<?php echo htmlspecialchars($m['email']); ?>" class="btn btn--ghost btn--sm">✉️</a>
                    <form method="POST" onsubmit="return confirm('Delete?')" style="display:inline">
                      <input type="hidden" name="delete_id" value="<?php echo $m['id']; ?>">
                      <button type="submit" class="btn btn--danger btn--sm">🗑</button>
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
          </div>
        </div>
        <?php else: ?>
        <div class="empty-state"><div class="empty-state__icon">📭</div><h4>No messages yet</h4><p>Contact form submissions will appear here.</p></div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>
<script src="admin.js"></script>
</body>
</html>
