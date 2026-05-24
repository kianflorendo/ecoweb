<?php

session_start();

define('DB_HOST','localhost'); define('DB_USER','root');
define('DB_PASS','');          define('DB_NAME','bottleback');

if (empty($_SESSION['user_id'])) {
    header('Location: login.php'); exit;
}
$user_id = (int)$_SESSION['user_id'];

$errors = []; $success = '';
$form   = [];

// Load current user
$user = null;
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conn->connect_error) {
        $st = $conn->prepare("SELECT * FROM users WHERE id=? LIMIT 1");
        $st->bind_param('i', $user_id);
        $st->execute();
        $user = $st->get_result()->fetch_assoc();
        $st->close();
        $conn->close();
    }
} catch (Exception $e) {}

if (!$user) { session_destroy(); header('Location: login.php'); exit; }
$form = ['first_name'=>$user['first_name'], 'last_name'=>$user['last_name'],
         'email'=>$user['email'], 'barangay'=>$user['barangay']];

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'info';

    if ($action === 'info') {
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name  = trim($_POST['last_name']  ?? '');
        $email      = trim($_POST['email']      ?? '');
        $barangay   = trim($_POST['barangay']   ?? '');
        $form = compact('first_name','last_name','email','barangay');

        if (!$first_name) $errors[] = 'First name is required.';
        if (!$last_name)  $errors[] = 'Last name is required.';
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
        if (!$barangay)   $errors[] = 'Barangay is required.';

        if (!$errors) {
            try {
                $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                // Check email uniqueness (excluding self)
                $st = $conn->prepare("SELECT id FROM users WHERE email=? AND id!=? LIMIT 1");
                $st->bind_param('si', $email, $user_id);
                $st->execute();
                if ($st->get_result()->num_rows) {
                    $errors[] = 'That email is already used by another account.';
                } else {
                    $st2 = $conn->prepare("UPDATE users SET first_name=?, last_name=?, email=?, barangay=? WHERE id=?");
                    $st2->bind_param('ssssi', $first_name, $last_name, $email, $barangay, $user_id);
                    $st2->execute();
                    $st2->close();
                    $_SESSION['user_name']      = $first_name;
                    $_SESSION['user_full_name'] = "$first_name $last_name";
                    $_SESSION['user_email']     = $email;
                    $success = 'info';
                    $user['first_name'] = $first_name;
                    $user['last_name']  = $last_name;
                    $user['email']      = $email;
                    $user['barangay']   = $barangay;
                }
                $st->close();
                $conn->close();
            } catch (Exception $e) { $errors[] = 'Database error.'; }
        }
    }

    if ($action === 'password') {
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password']     ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (!password_verify($current, $user['password_hash'])) {
            $errors[] = 'Current password is incorrect.';
        } elseif (strlen($new) < 8) {
            $errors[] = 'New password must be at least 8 characters.';
        } elseif ($new !== $confirm) {
            $errors[] = 'New passwords do not match.';
        } else {
            try {
                $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                $hash = password_hash($new, PASSWORD_DEFAULT);
                $st = $conn->prepare("UPDATE users SET password_hash=? WHERE id=?");
                $st->bind_param('si', $hash, $user_id);
                $st->execute();
                $st->close();
                $conn->close();
                $success = 'password';
            } catch (Exception $e) { $errors[] = 'Database error.'; }
        }
    }
}

$barangays = ['Muzon','Dolores','San Juan','Sta. Ana','San Isidro','Pagala','Kalinawan','Manga','Sampaloc','Sta. Cruz'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Profile — BottleBack</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css"/>
  <style>
    .ep-hero{background:var(--green-900);padding:7rem 0 3rem;position:relative;overflow:hidden}
    .ep-hero::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 60% 60%,rgba(53,122,78,.3),transparent 65%)}
    .ep-hero .container{position:relative;z-index:2}
    .ep-hero h1{color:var(--white);margin-bottom:.4rem}
    .ep-hero h1 em{color:var(--green-300)}
    .ep-hero p{color:rgba(255,255,255,.5);font-size:.9rem}
    .ep-back{display:inline-flex;align-items:center;gap:.5rem;color:rgba(255,255,255,.5);font-size:.82rem;margin-bottom:1.5rem;text-decoration:none;transition:.2s}
    .ep-back:hover{color:var(--green-300)}

    .ep-layout{display:grid;grid-template-columns:260px 1fr;gap:2rem;padding:2.5rem 0 3rem;align-items:start}

    /* Sidebar nav */
    .ep-sidebar{background:var(--white);border:1px solid var(--border);border-radius:16px;overflow:hidden;box-shadow:0 2px 8px rgba(12,26,16,.06);position:sticky;top:2rem}
    .ep-sidebar__user{padding:1.4rem;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:.9rem}
    .ep-avatar{width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,var(--green-500),var(--teal-500));display:flex;align-items:center;justify-content:center;font-size:1.3rem;font-weight:900;color:var(--white);font-family:var(--font-serif);flex-shrink:0}
    .ep-sidebar__name{font-weight:700;font-size:.95rem;color:var(--ink)}
    .ep-sidebar__email{font-size:.78rem;color:var(--muted)}
    .ep-nav a{display:flex;align-items:center;gap:.7rem;padding:.75rem 1.2rem;font-size:.87rem;color:var(--muted);border-left:3px solid transparent;transition:.2s}
    .ep-nav a:hover{color:var(--green-600);background:var(--green-50)}
    .ep-nav a.active{color:var(--green-700);background:var(--green-50);border-left-color:var(--green-500);font-weight:600}

    /* Form card */
    .ep-card{background:var(--white);border:1px solid var(--border);border-radius:16px;overflow:hidden;box-shadow:0 2px 8px rgba(12,26,16,.06);margin-bottom:1.4rem}
    .ep-card__header{padding:1.2rem 1.6rem;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:.7rem}
    .ep-card__icon{font-size:1.2rem}
    .ep-card__title{font-family:var(--font-serif);font-size:1.05rem;font-weight:700;color:var(--ink)}
    .ep-card__body{padding:1.6rem}

    .alert{padding:.85rem 1.1rem;border-radius:10px;margin-bottom:1.3rem;font-size:.86rem;display:flex;align-items:flex-start;gap:.6rem;line-height:1.5}
    .alert--error{background:#fff1f2;border:1px solid #fecdd3;color:var(--red-500)}
    .alert--success{background:var(--green-100);border:1px solid var(--green-200);color:var(--green-700)}
    .alert ul{margin:.3rem 0 0 1rem;list-style:disc}
    .alert li{margin-bottom:.15rem}

    .form-row-2{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
    .field{margin-bottom:1.1rem}
    label{display:block;font-size:.78rem;font-weight:600;letter-spacing:.05em;text-transform:uppercase;color:var(--muted);margin-bottom:.42rem}
    .inp{width:100%;background:var(--off-white);border:1.5px solid var(--border);border-radius:10px;padding:.8rem 1rem;font-family:var(--font-sans);font-size:.92rem;color:var(--ink);outline:none;transition:.2s}
    .inp:focus{border-color:var(--green-500);box-shadow:0 0 0 3px rgba(53,122,78,.1);background:var(--white)}
    select.inp{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23546a5a' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 1rem center;background-color:var(--off-white)}
    .inp-wrap{position:relative}
    .toggle-pw{position:absolute;right:.9rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);font-size:.85rem;cursor:pointer}
    .toggle-pw:hover{color:var(--green-600)}

    .pw-bar{height:4px;background:var(--border);border-radius:var(--r-full);overflow:hidden;margin-top:.45rem}
    .pw-fill{height:100%;border-radius:var(--r-full);transition:.3s;width:0}
    .pw-hint{font-size:.73rem;color:var(--muted);margin-top:.3rem}

    .btn-save{padding:.85rem 2rem;background:var(--green-500);color:var(--white);border:none;border-radius:var(--r-full);font-family:var(--font-sans);font-size:.93rem;font-weight:600;cursor:pointer;transition:.2s}
    .btn-save:hover{background:var(--green-600);transform:translateY(-1px);box-shadow:0 6px 18px rgba(53,122,78,.3)}
    .btn-cancel{padding:.85rem 1.5rem;background:transparent;border:1.5px solid var(--border);border-radius:var(--r-full);font-family:var(--font-sans);font-size:.93rem;color:var(--muted);cursor:pointer;transition:.2s;text-decoration:none;display:inline-flex;align-items:center}
    .btn-cancel:hover{border-color:var(--green-400);color:var(--green-600)}

    @media(max-width:820px){
      .ep-layout{grid-template-columns:1fr}
      .ep-sidebar{position:static}
      .form-row-2{grid-template-columns:1fr}
    }
  </style>
</head>
<body>
<?php include '../includes/nav.php'; ?>

<!-- HERO -->
<section class="ep-hero">
  <div class="container">
    <a href="profile.php" class="ep-back">← Back to Profile</a>
    <h1>Edit <em>Profile</em></h1>
    <p>Update your personal information and account settings.</p>
  </div>
</section>

<!-- CONTENT -->
<section style="background:var(--off-white);min-height:60vh">
  <div class="container">
    <div class="ep-layout">

      <!-- SIDEBAR -->
      <div class="ep-sidebar">
        <div class="ep-sidebar__user">
          <div class="ep-avatar"><?php echo strtoupper(substr($user['first_name'],0,1)); ?></div>
          <div>
            <div class="ep-sidebar__name"><?php echo htmlspecialchars($user['first_name'].' '.$user['last_name']); ?></div>
            <div class="ep-sidebar__email"><?php echo htmlspecialchars($user['email']); ?></div>
          </div>
        </div>
        <nav class="ep-nav">
          <a href="profile.php"> My Profile</a>
          <a href="edit-profile.php" class="active"> Edit Profile</a>
          <a href="data.php"> Live Dashboard</a>
          <a href="awareness.php"> Awareness</a>
          <a href="contact.php"> Contact</a>
          <a href="logout.php" style="color:#ef4444;margin-top:.5rem;border-top:1px solid var(--border)"> Sign Out</a>
        </nav>
      </div>

      <!-- FORMS -->
      <div>

        <!-- Personal Info -->
        <div class="ep-card">
          <div class="ep-card__header">
            <span class="ep-card__icon"></span>
            <span class="ep-card__title">Personal Information</span>
          </div>
          <div class="ep-card__body">
            <?php if($success==='info'): ?>
            <div class="alert alert--success"> Your profile has been updated successfully.</div>
            <?php endif; ?>
            <?php if($errors && ($_POST['action']??'')!=='password'): ?>
            <div class="alert alert--error"> <div><ul><?php foreach($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div></div>
            <?php endif; ?>

            <form method="POST">
              <input type="hidden" name="action" value="info">
              <div class="form-row-2">
                <div class="field">
                  <label>First Name</label>
                  <input type="text" name="first_name" class="inp" value="<?php echo htmlspecialchars($form['first_name']); ?>" required>
                </div>
                <div class="field">
                  <label>Last Name</label>
                  <input type="text" name="last_name" class="inp" value="<?php echo htmlspecialchars($form['last_name']); ?>" required>
                </div>
              </div>
              <div class="field">
                <label>Email Address</label>
                <input type="email" name="email" class="inp" value="<?php echo htmlspecialchars($form['email']); ?>" required>
              </div>
              <div class="field">
                <label>Barangay</label>
                <select name="barangay" class="inp">
                  <?php foreach($barangays as $b): ?>
                  <option value="<?php echo $b; ?>" <?php echo $form['barangay']===$b?'selected':''; ?>><?php echo $b; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div style="display:flex;gap:.8rem;align-items:center;flex-wrap:wrap;margin-top:.4rem">
                <button type="submit" class="btn-save"> Save Changes</button>
                <a href="profile.php" class="btn-cancel">Cancel</a>
              </div>
            </form>
          </div>
        </div>

        <!-- Change Password -->
        <div class="ep-card">
          <div class="ep-card__header">
            <span class="ep-card__icon"></span>
            <span class="ep-card__title">Change Password</span>
          </div>
          <div class="ep-card__body">
            <?php if($success==='password'): ?>
            <div class="alert alert--success"> Password changed successfully.</div>
            <?php endif; ?>
            <?php if($errors && ($_POST['action']??'')==='password'): ?>
            <div class="alert alert--error"> <div><ul><?php foreach($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div></div>
            <?php endif; ?>

            <form method="POST">
              <input type="hidden" name="action" value="password">
              <div class="field">
                <label>Current Password</label>
                <div class="inp-wrap">
                  <input type="password" name="current_password" class="inp" placeholder="Your current password" required>
                  <button type="button" class="toggle-pw" onclick="togglePw(this)">👁</button>
                </div>
              </div>
              <div class="field">
                <label>New Password <span style="text-transform:none;letter-spacing:0;font-weight:400">(min 8 chars)</span></label>
                <div class="inp-wrap">
                  <input type="password" name="new_password" class="inp" placeholder="Choose a strong password" id="newPw" oninput="checkStrength(this.value)" required>
                  <button type="button" class="toggle-pw" onclick="togglePw(this)">👁</button>
                </div>
                <div class="pw-bar"><div class="pw-fill" id="pwFill"></div></div>
                <span class="pw-hint" id="pwHint">Enter a new password</span>
              </div>
              <div class="field">
                <label>Confirm New Password</label>
                <div class="inp-wrap">
                  <input type="password" name="confirm_password" class="inp" placeholder="Repeat new password" required>
                  <button type="button" class="toggle-pw" onclick="togglePw(this)">👁</button>
                </div>
              </div>
              <button type="submit" class="btn-save"> Update Password</button>
            </form>
          </div>
        </div>

        <!-- Account Info (read-only) -->
        <div class="ep-card">
          <div class="ep-card__header">
            <span class="ep-card__icon">ℹ</span>
            <span class="ep-card__title">Account Information</span>
          </div>
          <div class="ep-card__body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
              <?php $facts=[
                [' Member Since', date('F j, Y', strtotime($user['created_at']))],
                [' Total Bottles', number_format($user['total_bottles'])],
                [' Total Rewards', number_format($user['total_rewards'])],
                [' Account Status', $user['is_active']?'Active':'Inactive'],
              ]; foreach($facts as [$label,$val]): ?>
              <div style="background:var(--off-white);border:1px solid var(--border);border-radius:10px;padding:1rem;text-align:center">
                <div style="font-size:.75rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:.3rem"><?php echo $label; ?></div>
                <div style="font-family:var(--font-serif);font-size:1.1rem;font-weight:700;color:var(--ink)"><?php echo $val; ?></div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

      </div><!-- /forms -->
    </div>
  </div>
</section>

<?php include '../includes/footer.php'; ?>
<script src="../js/main.js"></script>
<script>
function togglePw(btn) {
  const inp = btn.previousElementSibling || btn.parentElement.querySelector('input');
  inp.type = inp.type === 'password' ? 'text' : 'password';
  btn.textContent = inp.type === 'password' ? '👁' : '';
}
function checkStrength(pw) {
  const fill  = document.getElementById('pwFill');
  const hint  = document.getElementById('pwHint');
  let s = 0;
  if (pw.length>=8)  s++;
  if (pw.length>=12) s++;
  if (/[A-Z]/.test(pw)) s++;
  if (/[0-9]/.test(pw)) s++;
  if (/[^A-Za-z0-9]/.test(pw)) s++;
  const lvl=[
    {w:'0%',c:'#e5e7eb',t:'Enter a password'},
    {w:'25%',c:'#ef4444',t:'Weak'},
    {w:'50%',c:'#f97316',t:'Fair'},
    {w:'75%',c:'#eab308',t:'Good'},
    {w:'90%',c:'#22c55e',t:'Strong'},
    {w:'100%',c:'#16a34a',t:'Very strong ✓'},
  ][Math.min(s,5)];
  fill.style.width=lvl.w; fill.style.background=lvl.c;
  hint.textContent=lvl.t; hint.style.color=lvl.c;
}
</script>
</body>
</html>
