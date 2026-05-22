<?php
// ── BottleBack Client Registration ──────────────────────────
session_start();

define('DB_HOST','localhost'); define('DB_USER','root');
define('DB_PASS','');          define('DB_NAME','bottleback');

if (!empty($_SESSION['user_id'])) {
    header('Location: profile.php'); exit;
}

$errors = [];
$form   = ['first_name'=>'','last_name'=>'','email'=>'','barangay'=>'Muzon'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name']  ?? '');
    $email      = trim($_POST['email']      ?? '');
    $password   = $_POST['password']         ?? '';
    $confirm    = $_POST['confirm_password'] ?? '';
    $barangay   = trim($_POST['barangay']   ?? 'Muzon');
    $agree      = isset($_POST['agree']);

    $form = compact('first_name','last_name','email','barangay');

    // Validation
    if (!$first_name)               $errors[] = 'First name is required.';
    if (!$last_name)                $errors[] = 'Last name is required.';
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL))
                                    $errors[] = 'A valid email address is required.';
    if (strlen($password) < 8)      $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $confirm)     $errors[] = 'Passwords do not match.';
    if (!$barangay)                 $errors[] = 'Barangay is required.';
    if (!$agree)                    $errors[] = 'You must agree to the terms to continue.';

    if (!$errors) {
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ($conn->connect_error) throw new Exception('DB error');

            // Check duplicate email
            $st = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
            $st->bind_param('s', $email);
            $st->execute();
            $exists = $st->get_result()->num_rows > 0;
            $st->close();

            if ($exists) {
                $errors[] = 'An account with that email already exists. <a href="login.php">Sign in instead?</a>';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $st = $conn->prepare("INSERT INTO users (first_name, last_name, email, password_hash, barangay) VALUES (?,?,?,?,?)");
                $st->bind_param('sssss', $first_name, $last_name, $email, $hash, $barangay);
                $st->execute();
                $new_id = $st->insert_id;
                $st->close();
                $conn->close();

                // Auto-login after registration
                $_SESSION['user_id']         = $new_id;
                $_SESSION['user_name']       = $first_name;
                $_SESSION['user_full_name']  = "$first_name $last_name";
                $_SESSION['user_email']      = $email;
                $_SESSION['user_login_time'] = time();
                header('Location: profile.php?welcome=1'); exit;
            }
            $conn->close();
        } catch (Exception $e) {
            $errors[] = 'Database error. Make sure XAMPP is running and the database is set up.';
        }
    }
}

$barangays = ['Muzon','Dolores','San Juan','Sta. Ana','San Isidro','Pagala','Kalinawan','Manga','Sampaloc','Sta. Cruz'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Create Account — BottleBack</title>
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
      --r-md:14px;--r-full:999px;
      --shadow-xl:0 32px 80px rgba(12,26,16,.28);
      --ease:cubic-bezier(.4,0,.2,1);
    }
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    html{font-size:16px}
    body{font-family:var(--font-sans);background:var(--green-900);min-height:100vh;display:grid;grid-template-columns:1fr 1fr;overflow:hidden}

    /* ── LEFT PANEL ── */
    .left-panel{position:relative;display:flex;flex-direction:column;justify-content:center;padding:3rem;overflow:hidden}
    .left-bg{position:absolute;inset:0;pointer-events:none}
    .orb{position:absolute;border-radius:50%;filter:blur(90px)}
    .orb1{width:650px;height:650px;background:var(--teal-700);opacity:.3;top:-200px;left:-150px}
    .orb2{width:500px;height:500px;background:var(--green-600);opacity:.22;bottom:-100px;right:-100px}
    .orb3{width:230px;height:230px;background:var(--earth-400);opacity:.15;top:55%;left:55%}
    .grid-lines{position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.025) 1px,transparent 1px);background-size:55px 55px}
    .left-content{position:relative;z-index:2;max-width:440px}
    .nav-back{display:inline-flex;align-items:center;gap:.5rem;color:rgba(255,255,255,.5);font-size:.83rem;margin-bottom:2.5rem;transition:.2s;text-decoration:none}
    .nav-back:hover{color:var(--green-300)}
    .logo{display:flex;align-items:center;gap:.65rem;margin-bottom:1.2rem}
    .logo-icon{font-size:1.8rem}
    .logo-text{font-family:var(--font-sans);font-size:1.15rem;font-weight:500;color:rgba(255,255,255,.9)}
    .logo-text strong{color:var(--green-300)}
    h1{font-family:var(--font-serif);color:var(--white);font-size:clamp(2rem,3.5vw,2.9rem);font-weight:900;line-height:1.1;margin-bottom:.9rem}
    h1 em{color:var(--green-300);font-style:italic}
    .hero-sub{color:rgba(255,255,255,.55);font-size:.95rem;line-height:1.75;margin-bottom:2rem;max-width:380px}
    .steps{display:flex;flex-direction:column;gap:.7rem;margin-bottom:2rem}
    .step{display:flex;align-items:center;gap:.85rem}
    .step-num{width:28px;height:28px;border-radius:50%;background:rgba(115,196,138,.18);border:1px solid rgba(115,196,138,.35);display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:var(--green-300);flex-shrink:0}
    .step span{color:rgba(255,255,255,.65);font-size:.87rem}
    .left-footer{margin-top:2rem;padding-top:1.5rem;border-top:1px solid rgba(255,255,255,.08)}
    .left-footer p{font-size:.76rem;color:rgba(255,255,255,.3);line-height:1.6}

    /* ── RIGHT PANEL ── */
    .right-panel{background:var(--off-white);display:flex;flex-direction:column;justify-content:center;padding:2.5rem clamp(1.5rem,5vw,3.5rem);overflow-y:auto}
    .form-wrap{max-width:440px;width:100%;margin:0 auto}
    .form-eyebrow{font-size:.72rem;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--green-500);margin-bottom:.6rem}
    .form-title{font-family:var(--font-serif);font-size:1.8rem;font-weight:900;color:var(--ink);margin-bottom:.35rem}
    .form-title em{color:var(--green-500)}
    .form-sub{font-size:.87rem;color:var(--muted);margin-bottom:1.6rem;line-height:1.6}
    .form-sub a{color:var(--green-600);font-weight:600;text-decoration:underline;text-underline-offset:3px}

    .alert{padding:.85rem 1.1rem;border-radius:10px;margin-bottom:1.3rem;font-size:.86rem;display:flex;align-items:flex-start;gap:.6rem;line-height:1.5}
    .alert--error{background:#fff1f2;border:1px solid #fecdd3;color:var(--red-500)}
    .alert ul{margin-top:.3rem;padding-left:1.1rem;list-style:disc}
    .alert li{margin-bottom:.15rem}

    /* Form layout */
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
    .field{margin-bottom:1rem}
    label{display:block;font-size:.78rem;font-weight:600;letter-spacing:.05em;text-transform:uppercase;color:var(--muted);margin-bottom:.42rem}
    .input-wrap{position:relative}
    input[type="email"],input[type="password"],input[type="text"],select{
      width:100%;background:var(--white);border:1.5px solid var(--border);border-radius:10px;
      padding:.8rem 1rem;font-family:var(--font-sans);font-size:.92rem;color:var(--ink);
      outline:none;transition:.2s var(--ease)
    }
    select{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23546a5a' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 1rem center}
    input:focus,select:focus{border-color:var(--green-500);box-shadow:0 0 0 3px rgba(53,122,78,.1)}
    input.invalid,select.invalid{border-color:var(--red-500)}
    .toggle-pw{position:absolute;right:.9rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);font-size:.85rem;cursor:pointer;padding:.2rem}
    .toggle-pw:hover{color:var(--green-600)}

    /* Password strength */
    .pw-strength{margin-top:.5rem}
    .pw-bar{height:4px;background:var(--border);border-radius:var(--r-full);overflow:hidden;margin-bottom:.3rem}
    .pw-fill{height:100%;border-radius:var(--r-full);transition:width .3s,background .3s;width:0}
    .pw-label{font-size:.73rem;color:var(--muted)}

    /* Checkbox */
    .check-wrap{display:flex;align-items:flex-start;gap:.7rem;margin-bottom:1.3rem;cursor:pointer}
    .check-wrap input[type="checkbox"]{width:18px;height:18px;accent-color:var(--green-500);flex-shrink:0;margin-top:.1rem;cursor:pointer}
    .check-wrap span{font-size:.83rem;color:var(--muted);line-height:1.55}
    .check-wrap a{color:var(--green-600);text-decoration:underline;text-underline-offset:2px}

    .btn-submit{width:100%;padding:1rem;background:var(--green-500);color:var(--white);border:none;border-radius:var(--r-full);font-family:var(--font-sans);font-size:.98rem;font-weight:600;cursor:pointer;transition:.2s var(--ease);display:flex;align-items:center;justify-content:center;gap:.5rem}
    .btn-submit:hover{background:var(--green-600);transform:translateY(-2px);box-shadow:0 8px 24px rgba(53,122,78,.35)}

    .divider{display:flex;align-items:center;gap:1rem;margin:1.2rem 0;color:var(--muted);font-size:.78rem}
    .divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--border)}
    .signin-link{display:block;text-align:center;padding:.85rem;border:1.5px solid var(--border);border-radius:var(--r-full);font-size:.9rem;color:var(--text);font-weight:500;transition:.2s var(--ease)}
    .signin-link:hover{border-color:var(--green-400);color:var(--green-600);background:var(--green-50)}
    .signin-link strong{color:var(--green-600)}
    .form-footer{margin-top:1.5rem;text-align:center;font-size:.77rem;color:var(--muted)}
    .form-footer a{color:var(--green-600);text-decoration:underline;text-underline-offset:2px}

    @media(max-width:860px){
      body{grid-template-columns:1fr;overflow:auto}
      .left-panel{display:none}
      .right-panel{min-height:100vh;padding:2.5rem 1.5rem}
    }
    @media(max-width:480px){
      .form-row{grid-template-columns:1fr}
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
    <h1>Join the<br/><em>Movement.</em></h1>
    <p class="hero-sub">Create your free resident account and start earning rewards every time you recycle a plastic bottle at Barangay Muzon's BottleBack machine.</p>
    <div class="steps">
      <div class="step"><div class="step-num">1</div><span>Fill in your details below — takes 60 seconds.</span></div>
      <div class="step"><div class="step-num">2</div><span>Deposit plastic bottles at the BottleBack machine.</span></div>
      <div class="step"><div class="step-num">3</div><span>Earn free drinks & biscuits, track your impact.</span></div>
    </div>
    <div class="left-footer">
      <p>BottleBack · Our Lady of Fatima University<br>BSIT Capstone 2027 · Barangay Muzon, Taytay, Rizal<br>Aligned with RA 11898 — EPR Act 2022</p>
    </div>
  </div>
</div>

<!-- ── RIGHT PANEL ── -->
<div class="right-panel">
  <div class="form-wrap">
    <div class="form-eyebrow">Resident Registration</div>
    <div class="form-title">Create Your <em>Account</em></div>
    <p class="form-sub">Already have an account? <a href="login.php">Sign in →</a></p>

    <?php if($errors): ?>
    <div class="alert alert--error">
       <div>Please fix the following:<ul><?php foreach($errors as $e): ?><li><?php echo $e; ?></li><?php endforeach; ?></ul></div>
    </div>
    <?php endif; ?>

    <form method="POST" novalidate id="regForm">

      <div class="form-row">
        <div class="field">
          <label for="first_name">First Name</label>
          <input type="text" id="first_name" name="first_name" placeholder="e.g. Maria"
                 value="<?php echo htmlspecialchars($form['first_name']); ?>" autocomplete="given-name" required>
        </div>
        <div class="field">
          <label for="last_name">Last Name</label>
          <input type="text" id="last_name" name="last_name" placeholder="e.g. Santos"
                 value="<?php echo htmlspecialchars($form['last_name']); ?>" autocomplete="family-name" required>
        </div>
      </div>

      <div class="field">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="you@example.com"
               value="<?php echo htmlspecialchars($form['email']); ?>" autocomplete="email" required>
      </div>

      <div class="field">
        <label for="barangay">Barangay</label>
        <select id="barangay" name="barangay">
          <?php foreach($barangays as $b): ?>
          <option value="<?php echo $b; ?>" <?php echo $form['barangay']===$b?'selected':''; ?>><?php echo $b; ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="field">
        <label for="password">Password <span style="text-transform:none;letter-spacing:0;font-weight:400">(min 8 characters)</span></label>
        <div class="input-wrap">
          <input type="password" id="password" name="password" placeholder="Create a strong password" autocomplete="new-password" required oninput="checkStrength(this.value)">
          <button type="button" class="toggle-pw" onclick="togglePw('password',this)">👁</button>
        </div>
        <div class="pw-strength">
          <div class="pw-bar"><div class="pw-fill" id="pwFill"></div></div>
          <span class="pw-label" id="pwLabel">Enter a password</span>
        </div>
      </div>

      <div class="field">
        <label for="confirm_password">Confirm Password</label>
        <div class="input-wrap">
          <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat your password" autocomplete="new-password" required>
          <button type="button" class="toggle-pw" onclick="togglePw('confirm_password',this)">👁</button>
        </div>
      </div>

      <label class="check-wrap">
        <input type="checkbox" name="agree" <?php echo isset($_POST['agree'])?'checked':''; ?>>
        <span>I agree to the <a href="../pages/about.php" target="_blank">Terms of Use</a> and confirm I am a resident of Taytay, Rizal participating in the BottleBack recycling program.</span>
      </label>

      <button type="submit" class="btn-submit">Create My Account </button>
    </form>

    <div class="divider">already registered?</div>
    <a href="login.php" class="signin-link">Sign in to your <strong>existing account</strong></a>

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

function checkStrength(pw) {
  const fill  = document.getElementById('pwFill');
  const label = document.getElementById('pwLabel');
  let score = 0;
  if (pw.length >= 8)  score++;
  if (pw.length >= 12) score++;
  if (/[A-Z]/.test(pw)) score++;
  if (/[0-9]/.test(pw)) score++;
  if (/[^A-Za-z0-9]/.test(pw)) score++;
  const levels = [
    {w:'0%',   c:'#e5e7eb', t:'Enter a password'},
    {w:'25%',  c:'#ef4444', t:'Weak'},
    {w:'50%',  c:'#f97316', t:'Fair'},
    {w:'75%',  c:'#eab308', t:'Good'},
    {w:'90%',  c:'#22c55e', t:'Strong'},
    {w:'100%', c:'#16a34a', t:'Very strong ✓'},
  ];
  const lvl = levels[Math.min(score, 5)];
  fill.style.width = lvl.w;
  fill.style.background = lvl.c;
  label.textContent = lvl.t;
  label.style.color = lvl.c;
}
</script>
</body>
</html>
