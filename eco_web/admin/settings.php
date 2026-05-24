<?php

require 'auth.php';
$db = bb_db();
$connected = $db !== null;

$msg = '';

// Change password
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['change_pass'])) {
    $current = $_POST['current_pass'] ?? '';
    $new     = $_POST['new_pass'] ?? '';
    $confirm = $_POST['confirm_pass'] ?? '';

    // Read from config file
    $cfg_file = __DIR__.'/config.php';
    $cfg = file_exists($cfg_file) ? include $cfg_file : ['pass'=>'bottleback2027'];
    $stored_pass = $cfg['pass'] ?? 'bottleback2027';

    if ($current !== $stored_pass) {
        $msg = 'wrong_pass';
    } elseif (strlen($new) < 8) {
        $msg = 'short_pass';
    } elseif ($new !== $confirm) {
        $msg = 'mismatch';
    } else {
        file_put_contents($cfg_file, "<?php\nreturn ['pass'=>".var_export($new,true)."];\n");
        $msg = 'pass_changed';
    }
}

// Clear all transactions
if ($connected && $_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['clear_transactions'])) {
    $confirm = $_POST['confirm_clear'] ?? '';
    if ($confirm === 'DELETE') {
        $db->query("TRUNCATE TABLE transactions");
        $msg = 'cleared';
    } else {
        $msg = 'confirm_fail';
    }
}

// Clear all messages
if ($connected && $_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['clear_messages'])) {
    $confirm = $_POST['confirm_clear_msg'] ?? '';
    if ($confirm === 'DELETE') {
        $db->query("TRUNCATE TABLE contact_messages");
        $msg = 'msgs_cleared';
    } else {
        $msg = 'confirm_fail';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Settings — BottleBack Admin</title>
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
          <div class="label">Configuration</div>
          <h1> Admin <em>Settings</em></h1>
        </div>
      </div>

      <?php if($msg==='pass_changed'): ?><div class="alert alert--success"> Password changed successfully.</div><?php endif; ?>
      <?php if($msg==='wrong_pass'): ?><div class="alert alert--error"> Current password is incorrect.</div><?php endif; ?>
      <?php if($msg==='short_pass'): ?><div class="alert alert--error"> New password must be at least 8 characters.</div><?php endif; ?>
      <?php if($msg==='mismatch'): ?><div class="alert alert--error"> Passwords do not match.</div><?php endif; ?>
      <?php if($msg==='cleared'): ?><div class="alert alert--success"> All transactions cleared.</div><?php endif; ?>
      <?php if($msg==='msgs_cleared'): ?><div class="alert alert--success"> All contact messages cleared.</div><?php endif; ?>
      <?php if($msg==='confirm_fail'): ?><div class="alert alert--error"> Type DELETE exactly to confirm.</div><?php endif; ?>

      <div class="grid-2">

        <!-- CHANGE PASSWORD -->
        <div class="panel">
          <div class="panel-header"><span class="panel-title">Change Admin Password</span></div>
          <div class="panel-body">
            <form method="POST">
              <div class="form-group">
                <label class="form-label">Current Password</label>
                <input type="password" name="current_pass" class="form-input" required>
              </div>
              <div class="form-group">
                <label class="form-label">New Password (min 8 chars)</label>
                <input type="password" name="new_pass" class="form-input" minlength="8" required>
              </div>
              <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="confirm_pass" class="form-input" required>
              </div>
              <button type="submit" name="change_pass" class="btn btn--primary">Update Password</button>
            </form>
          </div>
        </div>

        <!-- DB INFO -->
        <div class="panel">
          <div class="panel-header"><span class="panel-title"> Database Info</span></div>
          <div class="panel-body">
            <table style="width:100%;font-size:.88rem">
              <tr style="border-bottom:1px solid var(--border)"><td style="padding:.6rem 0;color:var(--muted)">Host</td><td><code>localhost</code></td></tr>
              <tr style="border-bottom:1px solid var(--border)"><td style="padding:.6rem 0;color:var(--muted)">Database</td><td><code>bottleback</code></td></tr>
              <tr style="border-bottom:1px solid var(--border)"><td style="padding:.6rem 0;color:var(--muted)">User</td><td><code>root</code></td></tr>
              <tr style="border-bottom:1px solid var(--border)"><td style="padding:.6rem 0;color:var(--muted)">Status</td><td>
                <span class="badge badge--<?php echo $connected?'green':'red'; ?>"><?php echo $connected?'● Connected':'○ Offline'; ?></span>
              </td></tr>
              <tr><td style="padding:.6rem 0;color:var(--muted)">Config file</td><td><code>api/receive_data.php</code></td></tr>
            </table>
            <div class="alert alert--info" style="margin-top:1rem">
              <span>ℹ</span>
              <p>Edit DB credentials in <code>api/receive_data.php</code> and <code>pages/data.php</code>.</p>
            </div>
          </div>
        </div>

      </div>

      <!-- DANGER ZONE -->
      <div class="panel" style="border-color:var(--red-300)">
        <div class="panel-header" style="background:#fff5f5;border-color:var(--red-300)">
          <span class="panel-title" style="color:var(--red-500)"> Danger Zone</span>
        </div>
        <div class="panel-body">
          <div class="alert alert--error" style="margin-bottom:1.4rem">
            <span></span>
            <p>These actions are <strong>irreversible</strong>. They permanently delete data from the database. Type <code>DELETE</code> exactly to confirm.</p>
          </div>
          <div class="grid-2">
            <div style="border:1px solid var(--red-300);border-radius:var(--r-md);padding:1.2rem">
              <h4 style="color:var(--red-500);margin-bottom:.4rem">Clear All Transactions</h4>
              <p style="font-size:.85rem;margin-bottom:1rem">Permanently deletes all bottle transaction records. Machine stats will reset to zero.</p>
              <form method="POST" onsubmit="return confirm('This will DELETE ALL transactions. Are you sure?')">
                <div class="form-group">
                  <label class="form-label">Type DELETE to confirm</label>
                  <input type="text" name="confirm_clear" class="form-input" placeholder="DELETE" required>
                </div>
                <button type="submit" name="clear_transactions" class="btn btn--danger"> Clear All Transactions</button>
              </form>
            </div>
            <div style="border:1px solid var(--red-300);border-radius:var(--r-md);padding:1.2rem">
              <h4 style="color:var(--red-500);margin-bottom:.4rem">Clear All Messages</h4>
              <p style="font-size:.85rem;margin-bottom:1rem">Permanently deletes all contact form messages from the inbox.</p>
              <form method="POST" onsubmit="return confirm('This will DELETE ALL messages. Are you sure?')">
                <div class="form-group">
                  <label class="form-label">Type DELETE to confirm</label>
                  <input type="text" name="confirm_clear_msg" class="form-input" placeholder="DELETE" required>
                </div>
                <button type="submit" name="clear_messages" class="btn btn--danger">🗑 Clear All Messages</button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- ADMIN INFO -->
      <div class="panel">
        <div class="panel-header"><span class="panel-title">ℹ Admin Panel Info</span></div>
        <div class="panel-body">
          <div class="grid-2">
            <div>
              <h4 style="margin-bottom:.6rem">File Structure</h4>
              <code style="display:block;background:var(--green-900);color:var(--green-300);padding:.9rem 1.1rem;border-radius:10px;font-size:.78rem;line-height:1.9">
                eco_web/<br>
                ├── admin/<br>
                │&nbsp;&nbsp; ├── login.php       ← Login page<br>
                │&nbsp;&nbsp; ├── auth.php        ← Session guard<br>
                │&nbsp;&nbsp; ├── index.php       ← Dashboard<br>
                │&nbsp;&nbsp; ├── transactions.php<br>
                │&nbsp;&nbsp; ├── machine.php<br>
                │&nbsp;&nbsp; ├── messages.php<br>
                │&nbsp;&nbsp; ├── export.php<br>
                │&nbsp;&nbsp; ├── settings.php<br>
                │&nbsp;&nbsp; ├── logout.php<br>
                │&nbsp;&nbsp; ├── admin.css<br>
                │&nbsp;&nbsp; └── admin.js
              </code>
            </div>
            <div>
              <h4 style="margin-bottom:.6rem">Access URL</h4>
              <p style="font-size:.88rem;margin-bottom:.8rem">Navigate to the admin panel at:</p>
              <code style="display:block;background:var(--green-900);color:var(--green-300);padding:.9rem 1.1rem;border-radius:10px;font-size:.82rem">
                http://localhost/eco_web/admin/login.php
              </code>
              <p style="font-size:.82rem;margin-top:.8rem;color:var(--muted)">Default credentials are in <code>admin/login.php</code>. Change them before deploying.</p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
<script src="admin.js"></script>
</body>
</html>
