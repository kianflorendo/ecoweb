<?php

session_start();

define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'bottleback2027'); 

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = trim($_POST['password'] ?? '');
    if ($u === ADMIN_USER && $p === ADMIN_PASS) {
        $_SESSION['bb_admin'] = true;
        $_SESSION['bb_admin_time'] = time();
        header('Location: index.php'); exit;
    } else {
        $error = 'Invalid username or password.';
    }
}

if (!empty($_SESSION['bb_admin'])) { header('Location: index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login — BottleBack</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --green-900:#0b2016;--green-800:#163122;--green-700:#1f4530;
      --green-600:#2a5c3f;--green-500:#357a4e;--green-400:#4fa36a;
      --green-300:#73c48a;--green-200:#a8dbb8;--green-100:#d5f0de;
      --teal-700:#0d4d52;--teal-500:#178c96;--teal-300:#4ec9d4;
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
    body{font-family:var(--font-sans);background:var(--green-900);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem;position:relative;overflow:hidden}
    .bg{position:fixed;inset:0;pointer-events:none}
    .orb{position:absolute;border-radius:50%;filter:blur(100px)}
    .orb1{width:600px;height:600px;background:var(--green-600);opacity:.2;top:-200px;right:-150px}
    .orb2{width:400px;height:400px;background:var(--teal-500);opacity:.15;bottom:-100px;left:-100px}
    .grid-lines{position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.02) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.02) 1px,transparent 1px);background-size:50px 50px}
    .card{position:relative;z-index:2;background:rgba(22,49,34,.85);border:1px solid rgba(255,255,255,.1);border-radius:24px;padding:3rem;width:100%;max-width:420px;backdrop-filter:blur(20px);box-shadow:var(--shadow-xl);animation:fadeUp .6s var(--ease) both}
    @keyframes fadeUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}
    .logo{display:flex;align-items:center;gap:.6rem;margin-bottom:.5rem}
    .logo-icon{font-size:1.8rem}
    .logo-text{font-family:var(--font-sans);font-size:1.2rem;font-weight:500;color:rgba(255,255,255,.9)}
    .logo-text strong{color:var(--green-300)}
    .badge{display:inline-flex;align-items:center;gap:.5rem;font-size:.72rem;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:var(--green-300);background:rgba(115,196,138,.12);border:1px solid rgba(115,196,138,.3);padding:.3em .9em;border-radius:var(--r-full);margin-bottom:1.8rem}
    h1{font-family:var(--font-serif);color:var(--white);font-size:2rem;margin-bottom:.4rem}
    h1 em{color:var(--green-300);font-style:italic}
    .sub{color:rgba(255,255,255,.5);font-size:.88rem;margin-bottom:2rem}
    .field{margin-bottom:1.2rem}
    label{display:block;font-size:.8rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:rgba(255,255,255,.5);margin-bottom:.5rem}
    input{width:100%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:10px;padding:.85rem 1.1rem;font-family:var(--font-sans);font-size:.95rem;color:var(--white);outline:none;transition:.2s var(--ease)}
    input::placeholder{color:rgba(255,255,255,.25)}
    input:focus{border-color:var(--green-400);background:rgba(255,255,255,.09);box-shadow:0 0 0 3px rgba(79,163,106,.15)}
    .btn{width:100%;padding:.95rem;background:var(--green-500);color:var(--white);border:none;border-radius:var(--r-full);font-family:var(--font-sans);font-size:.95rem;font-weight:600;cursor:pointer;transition:.2s var(--ease);margin-top:.4rem}
    .btn:hover{background:var(--green-400);transform:translateY(-2px);box-shadow:0 8px 24px rgba(79,163,106,.35)}
    .error{background:rgba(220,38,38,.15);border:1px solid rgba(220,38,38,.4);border-radius:10px;padding:.8rem 1rem;font-size:.88rem;color:#fca5a5;margin-bottom:1.2rem;display:flex;align-items:center;gap:.5rem}
    .back{text-align:center;margin-top:1.5rem;font-size:.83rem;color:rgba(255,255,255,.35)}
    .back a{color:var(--green-300);text-decoration:underline;text-underline-offset:3px}
    .hint{margin-top:1.5rem;padding:1rem;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:10px;font-size:.78rem;color:rgba(255,255,255,.35);line-height:1.7}
    .hint strong{color:rgba(255,255,255,.5)}
  </style>
</head>
<body>
<div class="bg">
  <div class="orb orb1"></div>
  <div class="orb orb2"></div>
  <div class="grid-lines"></div>
</div>

<div class="card">
  <div class="logo">
    <span class="logo-icon"></span>
    <span class="logo-text">Bottle<strong>Back</strong></span>
  </div>
  <div class="badge"> Admin Portal</div>
  <h1>Sign <em>In</em></h1>
  <p class="sub">Access the BottleBack administration panel.</p>

  <?php if ($error): ?>
  <div class="error"> <?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="field">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" placeholder="Enter admin username" autocomplete="username" required>
    </div>
    <div class="field">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="••••••••••••" autocomplete="current-password" required>
    </div>
    <button type="submit" class="btn">Sign In →</button>
  </form>

  <div class="back"><a href="../index.php">← Back to public site</a></div>

  <div class="hint">
    <strong>Default credentials (dev only):</strong><br>
    Username: <code>admin</code> &nbsp;·&nbsp; Password: <code>bottleback2027</code><br>
    Change these in <code>admin/login.php</code> before deploying.
  </div>
</div>
</body>
</html>
