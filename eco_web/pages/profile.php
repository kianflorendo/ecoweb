<?php
// ── BottleBack Client Profile ────────────────────────────────
session_start();

define('DB_HOST','localhost'); define('DB_USER','root');
define('DB_PASS','');          define('DB_NAME','bottleback');

if (empty($_SESSION['user_id'])) {
    header('Location: login.php'); exit;
}

// Auto-logout after 2 hours
if (isset($_SESSION['user_login_time']) && (time() - $_SESSION['user_login_time']) > 7200) {
    session_destroy();
    header('Location: login.php?timeout=1'); exit;
}
$_SESSION['user_login_time'] = time();

$user_id = (int)$_SESSION['user_id'];
$user = null; $recent_tx = []; $connected = false;

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conn->connect_error) {
        $connected = true;
        $st = $conn->prepare("SELECT * FROM users WHERE id=? LIMIT 1");
        $st->bind_param('i', $user_id);
        $st->execute();
        $user = $st->get_result()->fetch_assoc();
        $st->close();

        if (!$user) { session_destroy(); header('Location: login.php'); exit; }

        // Community totals
        $r = $conn->query("SELECT COUNT(*) c FROM transactions WHERE status='Accepted'");
        $community_bottles = $r ? (int)$r->fetch_assoc()['c'] : 0;

        // Machine status
        $r = $conn->query("SELECT bin_level, is_online FROM machine_status ORDER BY updated_at DESC LIMIT 1");
        $machine = $r && $r->num_rows ? $r->fetch_assoc() : ['bin_level'=>0,'is_online'=>0];

        // Recent community transactions
        $r = $conn->query("SELECT * FROM transactions ORDER BY created_at DESC LIMIT 6");
        if ($r) while ($row = $r->fetch_assoc()) $recent_tx[] = $row;

        $conn->close();
    }
} catch (Exception $e) { $connected = false; }

$welcome = isset($_GET['welcome']);
$first   = $user['first_name'] ?? $_SESSION['user_name'] ?? 'Resident';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Profile — BottleBack</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css"/>
  <style>
    /* ── Profile-specific styles ── */
    .profile-hero{background:var(--green-900);padding:7rem 0 3.5rem;position:relative;overflow:hidden}
    .profile-hero::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 70% 50%,rgba(23,140,150,.25),transparent 65%)}
    .profile-hero .container{position:relative;z-index:2;display:flex;align-items:flex-end;justify-content:space-between;gap:2rem;flex-wrap:wrap}
    .profile-identity{display:flex;align-items:center;gap:1.4rem}
    .profile-avatar{width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,var(--green-500),var(--teal-500));display:flex;align-items:center;justify-content:center;font-size:2rem;font-family:var(--font-serif);font-weight:900;color:var(--white);flex-shrink:0;border:3px solid rgba(255,255,255,.2)}
    .profile-name{font-family:var(--font-serif);font-size:clamp(1.6rem,4vw,2.2rem);font-weight:900;color:var(--white);margin-bottom:.3rem}
    .profile-name em{color:var(--green-300)}
    .profile-meta{font-size:.83rem;color:rgba(255,255,255,.5);display:flex;align-items:center;gap:1rem;flex-wrap:wrap}
    .profile-meta span{display:flex;align-items:center;gap:.3rem}
    .profile-actions{display:flex;gap:.8rem;flex-wrap:wrap}

    .welcome-banner{background:linear-gradient(135deg,var(--green-600),var(--teal-700));border-radius:16px;padding:1.4rem 1.8rem;margin-bottom:2rem;display:flex;align-items:center;gap:1.2rem;color:var(--white);animation:fadeUp .5s ease both}
    @keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
    .welcome-banner__icon{font-size:2.2rem;flex-shrink:0}
    .welcome-banner h3{color:var(--white);margin-bottom:.2rem;font-size:1.15rem}
    .welcome-banner p{color:rgba(255,255,255,.75);font-size:.88rem}

    .stats-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:1.2rem;margin-bottom:2rem}
    .stat-box{background:var(--white);border:1px solid var(--border);border-radius:16px;padding:1.4rem;text-align:center;box-shadow:0 2px 8px rgba(12,26,16,.06);transition:.25s}
    .stat-box:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(12,26,16,.1)}
    .stat-box__icon{font-size:1.8rem;margin-bottom:.5rem}
    .stat-box__val{font-family:var(--font-serif);font-size:2.4rem;font-weight:900;line-height:1;color:var(--ink);margin-bottom:.2rem}
    .stat-box__val--green{color:var(--green-500)}
    .stat-box__val--teal{color:var(--teal-500)}
    .stat-box__label{font-size:.76rem;font-weight:600;letter-spacing:.07em;text-transform:uppercase;color:var(--muted)}

    .profile-grid{display:grid;grid-template-columns:1fr 1.5fr;gap:1.6rem;margin-bottom:2rem}

    .card{background:var(--white);border:1px solid var(--border);border-radius:16px;overflow:hidden;box-shadow:0 2px 8px rgba(12,26,16,.06)}
    .card-header{padding:1.1rem 1.4rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
    .card-title{font-family:var(--font-serif);font-size:.98rem;font-weight:700;color:var(--ink)}
    .card-body{padding:1.4rem}

    .info-row{display:flex;justify-content:space-between;align-items:center;padding:.65rem 0;border-bottom:1px solid var(--border);font-size:.88rem}
    .info-row:last-child{border-bottom:none}
    .info-row .key{color:var(--muted);font-weight:500}
    .info-row .val{color:var(--ink);font-weight:600;text-align:right}

    .machine-status-card{background:var(--green-900);border:1px solid var(--green-700);border-radius:16px;padding:1.4rem;color:var(--white)}
    .ms-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;font-size:.82rem}
    .ms-dot{width:8px;height:8px;border-radius:50%;background:var(--green-400);box-shadow:0 0 8px var(--green-400);animation:pulse 2s infinite}
    @keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
    .ms-stat{display:flex;justify-content:space-between;padding:.55rem 0;border-bottom:1px solid rgba(255,255,255,.07);font-size:.86rem;color:rgba(255,255,255,.55)}
    .ms-stat:last-child{border-bottom:none}
    .ms-stat strong{color:var(--green-300)}

    .tx-row{display:flex;justify-content:space-between;align-items:center;padding:.65rem 0;border-bottom:1px solid var(--border);font-size:.85rem}
    .tx-row:last-child{border-bottom:none}
    .tx-time{color:var(--muted);font-size:.78rem}
    .badge-sm{font-size:.71rem;font-weight:700;padding:.22em .75em;border-radius:var(--r-full);display:inline-flex}
    .badge-sm--green{background:var(--green-100);color:var(--green-700)}
    .badge-sm--red{background:#fee2e2;color:var(--red-500)}

    @media(max-width:880px){.profile-grid{grid-template-columns:1fr}}
    @media(max-width:600px){.stats-row{grid-template-columns:1fr 1fr}.profile-identity{flex-direction:column;align-items:flex-start}}
  </style>
</head>
<body>
<?php include '../includes/nav.php'; ?>

<!-- PROFILE HERO -->
<section class="profile-hero">
  <div class="container">
    <div class="profile-identity">
      <div class="profile-avatar"><?php echo strtoupper(substr($user['first_name']??'R',0,1)); ?></div>
      <div>
        <div class="profile-name">
          <?php echo htmlspecialchars($user['first_name']); ?> <em><?php echo htmlspecialchars($user['last_name']); ?></em>
        </div>
        <div class="profile-meta">
          <span> <?php echo htmlspecialchars($user['email']); ?></span>
          <span> Brgy. <?php echo htmlspecialchars($user['barangay']); ?></span>
          <span> Joined <?php echo date('M Y', strtotime($user['created_at'])); ?></span>
        </div>
      </div>
    </div>
    <div class="profile-actions">
      <a href="edit-profile.php" class="btn btn--ghost btn--sm">✏️ Edit Profile</a>
      <a href="logout.php" class="btn btn--outline btn--sm" style="border-color:rgba(255,255,255,.3);color:rgba(255,255,255,.7)">Sign Out</a>
    </div>
  </div>
</section>

<!-- MAIN CONTENT -->
<section class="section section--light" style="padding-top:2.5rem;padding-bottom:3rem">
  <div class="container">

    <?php if($welcome): ?>
    <div class="welcome-banner">
      <div class="welcome-banner__icon"></div>
      <div>
        <h3>Welcome to BottleBack, <?php echo htmlspecialchars($first); ?>!</h3>
        <p>Your account is ready. Start recycling at the Barangay Muzon machine to earn your first reward.</p>
      </div>
    </div>
    <?php endif; ?>

    <!-- STATS ROW -->
    <div class="stats-row">
      <div class="stat-box">
        <div class="stat-box__icon"></div>
        <div class="stat-box__val stat-box__val--green"><?php echo $connected ? number_format($user['total_bottles']) : '—'; ?></div>
        <div class="stat-box__label">My Bottles</div>
      </div>
      <div class="stat-box">
        <div class="stat-box__icon"></div>
        <div class="stat-box__val stat-box__val--teal"><?php echo $connected ? number_format($user['total_rewards']) : '—'; ?></div>
        <div class="stat-box__label">My Rewards</div>
      </div>
      <div class="stat-box">
        <div class="stat-box__icon"></div>
        <div class="stat-box__val"><?php echo $connected ? number_format($community_bottles) : '—'; ?></div>
        <div class="stat-box__label">Community Total</div>
      </div>
      <div class="stat-box">
        <div class="stat-box__icon"><?php echo ($connected && $machine['is_online']) ? '🟢' : '🔴'; ?></div>
        <div class="stat-box__val" style="font-size:1.3rem;padding-top:.3rem"><?php echo ($connected && $machine['is_online']) ? 'Online' : 'Offline'; ?></div>
        <div class="stat-box__label">Machine Status</div>
      </div>
    </div>

    <!-- PROFILE GRID -->
    <div class="profile-grid">

      <!-- Account Info + Machine Status -->
      <div style="display:flex;flex-direction:column;gap:1.4rem">
        <div class="card">
          <div class="card-header">
            <span class="card-title"> Account Details</span>
            <a href="edit-profile.php" class="btn btn--outline btn--sm">Edit</a>
          </div>
          <div class="card-body">
            <div class="info-row"><span class="key">Full Name</span><span class="val"><?php echo htmlspecialchars($user['first_name'].' '.$user['last_name']); ?></span></div>
            <div class="info-row"><span class="key">Email</span><span class="val"><?php echo htmlspecialchars($user['email']); ?></span></div>
            <div class="info-row"><span class="key">Barangay</span><span class="val"><?php echo htmlspecialchars($user['barangay']); ?></span></div>
            <div class="info-row"><span class="key">Joined</span><span class="val"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span></div>
            <div class="info-row"><span class="key">Last Login</span><span class="val"><?php echo $user['last_login'] ? date('M j, Y H:i', strtotime($user['last_login'])) : 'First login'; ?></span></div>
          </div>
        </div>

        <?php if($connected): ?>
        <div class="machine-status-card">
          <div class="ms-header">
            <span style="display:flex;align-items:center;gap:.5rem">
              <span class="ms-dot"></span>
              node_001 — Barangay Muzon
            </span>
            <span style="color:var(--green-300);font-weight:600"><?php echo $machine['is_online']?'ONLINE':'OFFLINE'; ?></span>
          </div>
          <div class="ms-stat"><span> Bin Fill</span><strong><?php echo $machine['bin_level']; ?>%</strong></div>
          <div class="ms-stat"><span> Community Bottles</span><strong><?php echo number_format($community_bottles); ?></strong></div>
          <div class="ms-stat"><span> Arduino</span><strong><?php echo $machine['is_online']?'Connected':'Awaiting'; ?></strong></div>
          <div style="margin-top:.9rem;background:rgba(255,255,255,.06);border-radius:var(--r-full);height:8px;overflow:hidden">
            <?php $bin_color = $machine['bin_level']>=90 ? '#f87171' : ($machine['bin_level']>=70 ? '#fbbf24' : 'var(--green-400)'); ?>
            <div style="height:100%;width:<?php echo $machine['bin_level']; ?>%;background:<?php echo $bin_color; ?>;border-radius:var(--r-full);transition:width 1s"></div>
          </div>
          <p style="font-size:.76rem;color:rgba(255,255,255,.35);margin-top:.5rem;text-align:center">
            <?php if($machine['bin_level']>=90) echo '⚠️ Bin full — machine may not accept bottles';
                  elseif($machine['bin_level']>=70) echo '⚠️ Bin filling up soon';
                  else echo ' Machine ready to accept bottles'; ?>
          </p>
        </div>
        <?php endif; ?>
      </div>

      <!-- Community Recent Activity -->
      <div class="card">
        <div class="card-header">
          <span class="card-title"> Community Activity</span>
          <a href="data.php" class="btn btn--outline btn--sm">View Dashboard →</a>
        </div>
        <div class="card-body">
          <?php if($connected && count($recent_tx)): ?>
          <p style="font-size:.8rem;color:var(--muted);margin-bottom:1rem">Most recent transactions across all users at the machine:</p>
          <?php foreach($recent_tx as $tx): ?>
          <div class="tx-row">
            <div>
              <span class="badge-sm badge-sm--<?php echo $tx['status']==='Accepted'?'green':'red'; ?>"><?php echo $tx['status']; ?></span>
              <span style="margin-left:.5rem;color:var(--text)"><?php echo $tx['bottle_count']; ?> bottle<?php echo $tx['bottle_count']!=1?'s':''; ?></span>
              <?php if($tx['status']==='Accepted'): ?>
              <span style="color:var(--muted);font-size:.82rem"> →  <?php echo $tx['reward_amount']; ?> reward</span>
              <?php endif; ?>
            </div>
            <span class="tx-time"><?php echo date('M j, H:i', strtotime($tx['created_at'])); ?></span>
          </div>
          <?php endforeach; ?>
          <?php else: ?>
          <div style="text-align:center;padding:2.5rem 1rem">
            <div style="font-size:2rem;margin-bottom:.7rem"></div>
            <h4 style="color:var(--ink);margin-bottom:.4rem">No activity yet</h4>
            <p style="font-size:.88rem;max-width:300px;margin:0 auto">Machine transactions will appear here once the Arduino is connected.</p>
          </div>
          <?php endif; ?>
        </div>
      </div>

    </div>

    <!-- HOW TO EARN -->
    <div class="card" style="border-color:var(--green-200);background:var(--green-50)">
      <div class="card-header" style="background:var(--green-100);border-color:var(--green-200)">
        <span class="card-title" style="color:var(--green-800)"> How to Earn Rewards</span>
      </div>
      <div class="card-body">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1.2rem">
          <?php
          $steps=[['','Bring your used plastic PET bottles to the BottleBack machine in Barangay Muzon.','Step 1: Get Your Bottles'],
                  ['','Insert bottles into the input slot one at a time. IR and ultrasonic sensors check each one.','Step 2: Insert & Validate'],
                  ['','Each accepted bottle earns you a free drink or biscuit, dispensed right from the machine.','Step 3: Collect Your Reward'],
                  ['','Your contribution helps reduce plastic waste and keeps Barangay Muzon clean.','Step 4: Make an Impact']];
          foreach($steps as $i=>[$icon,$desc,$title]):
          ?>
          <div style="text-align:center;padding:.8rem">
            <div style="font-size:2rem;margin-bottom:.5rem"><?php echo $icon; ?></div>
            <strong style="display:block;color:var(--green-800);font-size:.88rem;margin-bottom:.3rem"><?php echo $title; ?></strong>
            <p style="font-size:.82rem;color:var(--muted);line-height:1.55"><?php echo $desc; ?></p>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

  </div>
</section>

<?php include '../includes/footer.php'; ?>
<script src="../js/main.js"></script>
</body>
</html>