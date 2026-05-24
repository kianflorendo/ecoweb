<?php
// ── BottleBack Client Login ──────────────────────────────────
session_start();

define('DB_HOST','localhost'); define('DB_USER','root');
define('DB_PASS','');          define('DB_NAME','bottleback');

// Already logged in — redirect to profile
if (!empty($_SESSION['user_id'])) {
    header('Location: profile.php'); exit;
}

$errors = [];
$form   = ['email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $form['email'] = $email;

    if (!$email)    $errors[] = 'Email is required.';
    if (!$password) $errors[] = 'Password is required.';

    if (!$errors) {
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ($conn->connect_error) throw new Exception('DB error');

            $st = $conn->prepare("SELECT id, first_name, last_name, password_hash, is_active FROM users WHERE email=? LIMIT 1");
            $st->bind_param('s', $email);
            $st->execute();
            $user = $st->get_result()->fetch_assoc();
            $st->close();

            if (!$user) {
                $errors[] = 'No account found with that email.';
            } elseif (!$user['is_active']) {
                $errors[] = 'Your account has been deactivated. Please contact support.';
            } elseif (!password_verify($password, $user['password_hash'])) {
                $errors[] = 'Incorrect password. Please try again.';
            } else {
                // Success
                $conn->query("UPDATE users SET last_login=NOW() WHERE id={$user['id']}");
                $_SESSION['user_id']         = $user['id'];
                $_SESSION['user_name']       = $user['first_name'];
                $_SESSION['user_full_name']  = $user['first_name'].' '.$user['last_name'];
                $_SESSION['user_email']      = $email;
                $_SESSION['user_login_time'] = time();
                $conn->close();
                $redirect = $_GET['redirect'] ?? 'profile.php';
                header('Location: '.$redirect); exit;
            }
            $conn->close();
        } catch (Exception $e) {
            $errors[] = 'Database error. Make sure XAMPP is running.';
        }
    }
}

$timeout_msg = isset($_GET['timeout']) ? 'Your session expired. Please sign in again.' : '';
$logout_msg  = isset($_GET['logout'])  ? 'You have been signed out successfully.' : '';
$registered  = isset($_GET['registered']) ? 'Account created! Please sign in.' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sign In — BottleBack</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    :root{
      --green-900:#0b2016;--green-800:#163122;--green-700:#1f4530;
      --green-600:#2a5c3f;--green-500:#357a4e;--green-400:#4fa36a;
      --green-300:#73c48a;--green-200:#a8dbb8;--green-100:#d5f0de;
      --teal-700:#0d4d52;--teal-500:#178c96;--teal-300:#4ec9d4;
      --earth-400:#c4843c;
      --ink:#0c1a10;--text:#253320;--muted:#546a5a;--border:#c5d9c9;
      --white:#ffffff;--off-white:#f7faf8;
      --red-500:#dc2626;--red-100:#fee2e2;
      --font-serif:'Playfair Display',Georgia,serif;
      --font-sans:'DM Sans',system-ui,sans-serif;
      --r-md:14px;--r-lg:22px;--r-full:999px;
      --shadow-xl:0 32px 80px rgba(12,26,16,.28);
      --ease:cubic-bezier(.4,0,.2,1);
    }
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    html{font-size:16px}
    body{font-family:var(--font-sans);background:var(--green-900);min-height:100vh;display:grid;grid-template-columns:1fr 1fr;overflow:hidden}

    /* ── LEFT PANEL ── */
    .left-panel{position:relative;display:flex;flex-direction:column;justify-content:center;padding:3rem;overflow:hidden;background:var(--green-900)}
    .left-bg{position:absolute;inset:0;pointer-events:none}
    .orb{position:absolute;border-radius:50%;filter:blur(90px)}
    .orb1{width:700px;height:700px;background:var(--green-600);opacity:.25;top:-250px;right:-200px}
    .orb2{width:450px;height:450px;background:var(--teal-500);opacity:.18;bottom:-150px;left:-100px}
    .orb3{width:250px;height:250px;background:var(--earth-400);opacity:.13;top:40%;left:30%}
    .grid-lines{position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.025) 1px,transparent 1px);background-size:55px 55px}
    .left-content{position:relative;z-index:2;max-width:460px}
    .nav-back{display:inline-flex;align-items:center;gap:.5rem;color:rgba(255,255,255,.5);font-size:.83rem;margin-bottom:3rem;transition:.2s;text-decoration:none}
    .nav-back:hover{color:var(--green-300)}
    .logo{display:flex;align-items:center;gap:.65rem;margin-bottom:1.4rem}
    .logo-icon{font-size:1.8rem}
    .logo-text{font-family:var(--font-sans);font-size:1.15rem;font-weight:500;color:rgba(255,255,255,.9)}
    .logo-text strong{color:var(--green-300)}
    h1{font-family:var(--font-serif);color:var(--white);font-size:clamp(2.2rem,4vw,3.2rem);font-weight:900;line-height:1.1;margin-bottom:1rem}
    h1 em{color:var(--green-300);font-style:italic}
    .hero-sub{color:rgba(255,255,255,.58);font-size:1rem;line-height:1.75;margin-bottom:2.4rem;max-width:400px}
    .perks{display:flex;flex-direction:column;gap:.9rem}
    .perk{display:flex;align-items:flex-start;gap:.85rem}
    .perk-icon{width:36px;height:36px;background:rgba(115,196,138,.15);border:1px solid rgba(115,196,138,.3);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0}
    .perk-text strong{display:block;color:rgba(255,255,255,.85);font-size:.88rem;margin-bottom:.15rem}
    .perk-text span{color:rgba(255,255,255,.45);font-size:.81rem}
    .left-footer{margin-top:3rem;padding-top:1.8rem;border-top:1px solid rgba(255,255,255,.08)}
    .left-footer p{font-size:.78rem;color:rgba(255,255,255,.3);line-height:1.6}

    /* ── RIGHT PANEL ── */
    .right-panel{background:var(--off-white);display:flex;flex-direction:column;justify-content:center;padding:3rem clamp(2rem,5vw,4rem);overflow-y:auto}
    .form-wrap{max-width:420px;width:100%;margin:0 auto}
    .form-eyebrow{font-size:.72rem;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--green-500);margin-bottom:.7rem}
    .form-title{font-family:var(--font-serif);font-size:1.9rem;font-weight:900;color:var(--ink);margin-bottom:.4rem}
    .form-title em{color:var(--green-500)}
    .form-sub{font-size:.88rem;color:var(--muted);margin-bottom:2rem;line-height:1.6}
    .form-sub a{color:var(--green-600);font-weight:600;text-decoration:underline;text-underline-offset:3px}

    /* Alerts */
    .alert{padding:.85rem 1.1rem;border-radius:10px;margin-bottom:1.4rem;font-size:.86rem;display:flex;align-items:flex-start;gap:.6rem;line-height:1.5}
    .alert--error{background:#fff1f2;border:1px solid #fecdd3;color:var(--red-500)}
    .alert--success{background:var(--green-100);border:1px solid var(--green-200);color:var(--green-700)}
    .alert--info{background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8}
    .alert ul{margin-top:.3rem;padding-left:1.1rem;list-style:disc}
    .alert li{margin-bottom:.15rem}

    /* Fields */
    .field{margin-bottom:1.15rem}
    .field-label{display:flex;justify-content:space-between;align-items:center;margin-bottom:.45rem}
    label{font-size:.8rem;font-weight:600;letter-spacing:.05em;text-transform:uppercase;color:var(--muted)}
    .forgot-link{font-size:.78rem;color:var(--green-600);text-decoration:underline;text-underline-offset:3px}
    .input-wrap{position:relative}
    input[type="email"],input[type="password"],input[type="text"]{
      width:100%;background:var(--white);border:1.5px solid var(--border);border-radius:10px;
      padding:.85rem 1.1rem;font-family:var(--font-sans);font-size:.93rem;color:var(--ink);
      outline:none;transition:.2s var(--ease)
    }
    input:focus{border-color:var(--green-500);box-shadow:0 0 0 3px rgba(53,122,78,.1)}
    input.invalid{border-color:var(--red-500)}
    .toggle-pw{position:absolute;right:1rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);font-size:.85rem;cursor:pointer;padding:.2rem}
    .toggle-pw:hover{color:var(--green-600)}

    /* Submit */
    .btn-submit{width:100%;padding:1rem;background:var(--green-500);color:var(--white);border:none;border-radius:var(--r-full);font-family:var(--font-sans);font-size:.98rem;font-weight:600;cursor:pointer;transition:.2s var(--ease);margin-top:.3rem;display:flex;align-items:center;justify-content:center;gap:.5rem}
    .btn-submit:hover{background:var(--green-600);transform:translateY(-2px);box-shadow:0 8px 24px rgba(53,122,78,.35)}

    .divider{display:flex;align-items:center;gap:1rem;margin:1.4rem 0;color:var(--muted);font-size:.78rem}
    .divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--border)}

    .create-link{display:block;text-align:center;padding:.9rem;border:1.5px solid var(--border);border-radius:var(--r-full);font-size:.9rem;color:var(--text);font-weight:500;transition:.2s var(--ease)}
    .create-link:hover{border-color:var(--green-400);color:var(--green-600);background:var(--green-50)}
    .create-link strong{color:var(--green-600)}

    .form-footer{margin-top:2rem;text-align:center;font-size:.78rem;color:var(--muted)}
    .form-footer a{color:var(--green-600);text-decoration:underline;text-underline-offset:3px}

    /* ── RESPONSIVE ── */
    @media(max-width:860px){
      body{grid-template-columns:1fr;overflow:auto}
      .left-panel{display:none}
      .right-panel{min-height:100vh;padding:2.5rem 1.5rem}
    }
  </style>
</head>
<body>

<!-- ── LEFT PANEL ── -->
<div class="left-panel">
  <div class="left-bg">
    <div class="orb orb1"></div>
    <div class="orb orb2"></div>
    <div class="orb orb3"></div>
    <div class="grid-lines"></div>
  </div>
  <div class="left-content">
    <a href="../index.php" class="nav-back">← Back to BottleBack</a>
    <div class="logo">
      <span class="logo-icon"></span>
      <span class="logo-text">Bottle<strong>Back</strong></span>
    </div>
    <h1>Recycle.<br/>Earn. <em>Track.</em></h1>
    <p class="hero-sub">Join Barangay Muzon's smart recycling program. Deposit plastic bottles, earn rewards, and monitor your impact — all in one place.</p>
    <div class="perks">
      <div class="perk">
        <div class="perk-icon">🍶</div>
        <div class="perk-text">
          <strong>Track Your Bottles</strong>
          <span>See every bottle you've deposited and all-time totals.</span>
        </div>
      </div>
      <div class="perk">
        <div class="perk-icon"></div>
        <div class="perk-text">
          <strong>Monitor Your Rewards</strong>
          <span>Keep tabs on drinks and biscuits you've earned.</span>
        </div>
      </div>
      <div class="perk">
        <div class="perk-icon"></div>
        <div class="perk-text">
          <strong>See Your Impact</strong>
          <span>Know exactly how much you've contributed to a cleaner Muzon.</span>
        </div>
      </div>
    </div>
    <div class="left-footer">
      <p>BottleBack · Our Lady of Fatima University<br>College of Computer Studies · BSIT Capstone 2027<br>Barangay Muzon, Taytay, Rizal</p>
    </div>
  </div>
</div>

<!-- ── RIGHT PANEL ── -->
<div class="right-panel">
  <div class="form-wrap">
    <div class="form-eyebrow">Resident Portal</div>
    <div class="form-title">Welcome <em>Back</em></div>
    <p class="form-sub">Don't have an account? <a href="register.php">Create one free →</a></p>

    <?php if($timeout_msg): ?>
    <div class="alert alert--info"> <?php echo $timeout_msg; ?></div>
    <?php endif; ?>
    <?php if($logout_msg): ?>
    <div class="alert alert--success"> <?php echo $logout_msg; ?></div>
    <?php endif; ?>
    <?php if($registered): ?>
    <div class="alert alert--success"> <?php echo $registered; ?></div>
    <?php endif; ?>
    <?php if($errors): ?>
    <div class="alert alert--error">
       <div><ul><?php foreach($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div>
    </div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="field">
        <div class="field-label">
          <label for="email">Email Address</label>
        </div>
        <input type="email" id="email" name="email" placeholder="you@example.com"
               value="<?php echo htmlspecialchars($form['email']); ?>"
               class="<?php echo $errors ? 'invalid' : ''; ?>" autocomplete="email" required>
      </div>

      <div class="field">
        <div class="field-label">
          <label for="password">Password</label>
          <a href="forgot.php" class="forgot-link">Forgot password?</a>
        </div>
        <div class="input-wrap">
          <input type="password" id="password" name="password" placeholder="••••••••••"
                 class="<?php echo $errors ? 'invalid' : ''; ?>" autocomplete="current-password" required>
          <button type="button" class="toggle-pw" onclick="togglePw('password',this)">👁</button>
        </div>
      </div>

      <button type="submit" class="btn-submit">Sign In →</button>
    </form>

    <div class="divider">or</div>
    <a href="register.php" class="create-link">New here? <strong>Create a free account</strong></a>

    <div class="form-footer">
      <a href="../index.php">← Return to public site</a>
    </div>
  </div>
</div>

<script>
function togglePw(id, btn) {
  const inp = document.getElementById(id);
  if (inp.type === 'password') { inp.type = 'text'; btn.textContent = ''; }
  else { inp.type = 'password'; btn.textContent = '👁'; }
}
</script>
</body>
</html>
