<?php

$page_title = "BottleBack | An Arduino-Based Plastic Bottle Vending Machine to Support Environmental Awareness for Barangay Muzon, Taytay Rizal";
$active_page = "home";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo $page_title; ?></title>
  <link rel="stylesheet" href="css/style.css"/>
  <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-solid-rounded/css/uicons-solid-rounded.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
<?php include 'includes/nav.php'; ?>

<!-- ── HERO ── -->
<section class="hero">
  <div class="hero__bg">
    <div class="hero__orb orb1"></div>
    <div class="hero__orb orb2"></div>
    <div class="hero__orb orb3"></div>
    <div class="hero__grid-lines"></div>
  </div>
  <div class="hero__content">
    <div class="hero__eyebrow">
      <span class="pulse-dot"></span>
      Our Lady of Fatima University &nbsp;·&nbsp; College of Computer Studies &nbsp;·&nbsp; BSIT Capstone 2026
    </div>
    <h1 class="hero__title">
      Recycle a Bottle.<br/>
      <em>Earn a Reward.</em><br/>
      Help Barangay Muzon.
    </h1>
    <p class="hero__sub">
      An Arduino-Based Plastic Bottle Vending Machine to Support Environmental Awareness
      for Barangay Muzon, Taytay Rizal — making recycling simple, interactive, and rewarding.
    </p>
    <div class="hero__cta">
      <a href="pages/about.php" class="btn btn--primary">About the Project</a>
      <a href="pages/how-it-works.php" class="btn btn--ghost">How It Works →</a>
    </div>
    <div class="hero__machine-card">
      <div class="machine-card__screen">
        <div class="machine-screen__header">
          <span class="msh-dot msh-dot--green"></span>
          <span>BottleBack — Barangay Muzon</span>
          <span class="msh-status">READY</span>
        </div>
        <div class="machine-screen__body">
          <div class="mscreen-row">
            <span>Bottles collected today</span>
            <strong class="mscreen-val" id="counterDisplay">0</strong>
          </div>
          <div class="mscreen-row">
            <span>Rewards dispensed</span>
            <strong class="mscreen-val" id="pointsDisplay">0</strong>
          </div>
          <div class="mscreen-row">
            <span>Bin capacity</span>
            <strong class="mscreen-val mscreen-val--ok">OK</strong>
          </div>
        </div>
        <div class="machine-screen__footer">Insert plastic bottle to begin ▶</div>
      </div>
    </div>
  </div>
  <div class="hero__scroll"><span>Scroll</span><div class="hero__scroll-line"></div></div>
</section>

<!-- ── STATS ── -->
<section class="stats-banner">
  <div class="stats-banner__inner">
    <div class="stat-item">
      <span class="stat-num">35,580</span>
      <span class="stat-label">Tons of garbage produced in PH daily</span>
    </div>
    <div class="stat-divider"></div>
    <div class="stat-item">
      <span class="stat-num">450 yrs</span>
      <span class="stat-label">For 1 PET bottle to fully decompose</span>
    </div>
    <div class="stat-divider"></div>
    <div class="stat-item">
      <span class="stat-num">RA 11898</span>
      <span class="stat-label">PH Extended Producer Responsibility Act 2022</span>
    </div>
    <div class="stat-divider"></div>
    <div class="stat-item">
      <span class="stat-num">153</span>
      <span class="stat-label">Active MRFs in Rizal barangays (2024)</span>
    </div>
  </div>
</section>

<!-- ── ABOUT ── -->
<section class="section about-section">
  <div class="container">
    <div class="about-grid">
      <div class="about-text">
        <div class="section-label">The Capstone Project</div>
        <h2 class="section-title">An Arduino-Based<br/><em>Plastic Bottle Vending Machine</em><br/>to Support Environmental Awareness<br/>for Barangay Muzon, Taytay Rizal</h2>
        <p>Presented to the Faculty of the <strong>College of Computer Studies, Our Lady of Fatima University</strong>, Antipolo City — this capstone project proposes a Reverse Vending Machine (RVM) that accepts used plastic bottles from community residents and rewards them with free products such as drinks or biscuits.</p>
        <p>Barangay Muzon, Taytay, Rizal is a growing community where plastic bottles are frequently improperly disposed of, contributing to pollution and clogged drainage. This machine aims to change that — making recycling easy, rewarding, and habit-forming.</p>
        <a href="pages/about.php" class="btn btn--outline">Full Project Overview →</a>
      </div>
      <div class="about-visual">
        <div class="project-badge-stack">
          <div class="project-badge">
            <span class="pb-icon"></span>
            <div><strong>Our Lady of Fatima University</strong><p>College of Computer Studies, Antipolo City — BSIT Program, 2026</p></div>
          </div>
          <div class="project-badge project-badge--accent">
            <span class="pb-icon"></span>
            <div><strong>Barangay Muzon, Taytay Rizal</strong><p>Target community where the machine will be deployed and evaluated</p></div>
          </div>
          <div class="project-badge">
            <span class="pb-icon"></span>
            <div><strong>Arduino-Powered</strong><p>IR &amp; ultrasonic sensors, servo motor, LCD display — all controlled by Arduino</p></div>
          </div>
          <div class="project-badge project-badge--accent">
            <span class="pb-icon"></span>
            <div><strong>Rewards: Drinks &amp; Biscuits</strong><p>Each accepted bottle earns the resident a free drink or biscuit as incentive</p></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── RESEARCHERS ── -->
<section class="section section--dark researchers-section">
  <div class="container">
    <div class="section-header">
      <div class="section-label section-label--light">The Research Team</div>
      <h2 class="section-title section-title--light">Meet the <em>Researchers</em></h2>
    </div>
    <div class="researchers-grid">
      <?php
      $researchers = [
        ['name'=>'Angeles, Alyza Mae',         'icon'=>''],
        ['name'=>'Despabiladeras, Marnes',      'icon'=>''],
        ['name'=>'Ellema, Jessica A.',           'icon'=>''],
        ['name'=>'San Marcos, Nick Anjelo',     'icon'=>''],
        ['name'=>'Soriano, Lemuel Jaaziah',     'icon'=>''],
      ];
      foreach ($researchers as $r): ?>
      <div class="researcher-card">
        <div class="researcher-icon"><?php echo $r['icon']; ?></div>
        <div class="researcher-name"><?php echo $r['name']; ?></div>
        <div class="researcher-dept">BSIT — OLFU Antipolo</div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── HOW IT WORKS SUMMARY ── -->
<section class="section how-section">
  <div class="container">
    <div class="section-header">
      <div class="section-label">Process Flow</div>
      <h2 class="section-title">How the Machine <em>Works</em></h2>
      <p class="section-intro">The machine uses sensor-based detection and an Arduino microcontroller to validate, count, and reward residents for every plastic bottle deposited.</p>
    </div>
    <div class="steps-flow">
      <div class="step-card">
        <div class="step-num">01</div>
        <div class="step-icon"></div>
        <h4>Insert Bottle</h4>
        <p>Resident places a used plastic bottle into the machine's input slot.</p>
      </div>
      <div class="step-arrow">→</div>
      <div class="step-card">
        <div class="step-num">02</div>
        <div class="step-icon"></div>
        <h4>IR Sensor Detects</h4>
        <p>Infrared sensor detects the bottle's presence and triggers the validation sequence.</p>
      </div>
      <div class="step-arrow">→</div>
      <div class="step-card">
        <div class="step-num">03</div>
        <div class="step-icon"></div>
        <h4>Ultrasonic Validates</h4>
        <p>HC-SR04 ultrasonic sensor verifies the bottle's size and confirms it as a valid PET plastic bottle.</p>
      </div>
      <div class="step-arrow">→</div>
      <div class="step-card">
        <div class="step-num">04</div>
        <div class="step-icon"></div>
        <h4>Arduino Decides</h4>
        <p>Arduino processes sensor data and decides: accept or reject. LCD displays the result to the user.</p>
      </div>
      <div class="step-arrow">→</div>
      <div class="step-card">
        <div class="step-num">05</div>
        <div class="step-icon"></div>
        <h4>Reward Dispensed</h4>
        <p>Accepted bottles earn the resident a free drink or biscuit dispensed from the machine.</p>
      </div>
    </div>
    <div class="steps-cta">
      <a href="pages/how-it-works.php" class="btn btn--primary">Full Technical Breakdown →</a>
    </div>
  </div>
</section>

<!-- ── SIGNIFICANCE ── -->
<section class="section section--light">
  <div class="container">
    <div class="section-header">
      <div class="section-label">Significance of the Study</div>
      <h2 class="section-title">Who benefits from <em>this project?</em></h2>
    </div>
    <div class="significance-grid">
      <?php
      $beneficiaries = [
        ['icon'=>'','who'=>'Residents',            'benefit'=>'Earn free products by recycling plastic bottles, building responsible waste management habits.'],
        ['icon'=>'','who'=>'Barangay Officials',   'benefit'=>'Gain an efficient system for collecting and managing plastic waste at the community level.'],
        ['icon'=>'','who'=>'Environmental Advocates','benefit'=>'A replicable model other communities can adopt for similar recycling initiatives.'],
        ['icon'=>'','who'=>'Local Businesses',      'benefit'=>'Opportunity to contribute to sustainability while engaging meaningfully with the community.'],
        ['icon'=>'','who'=>'CCS Department (OLFU)', 'benefit'=>'Strengthens academic programs through practical, technology-driven community-based innovation.'],
        ['icon'=>'','who'=>'Future Researchers',    'benefit'=>'A reference for future studies integrating technology into community-based recycling programs.'],
      ];
      foreach ($beneficiaries as $b): ?>
      <div class="significance-card">
        <div class="sig-icon"><?php echo $b['icon']; ?></div>
        <strong><?php echo $b['who']; ?></strong>
        <p><?php echo $b['benefit']; ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── DASHBOARD TEASER ── -->
<section class="section section--teal machine-live-section">
  <div class="container">
    <div class="machine-live-grid">
      <div class="machine-live-text">
        <div class="section-label section-label--light">Web Dashboard</div>
        <h2 class="section-title section-title--light">Track every bottle,<br/><em>in real time</em></h2>
        <p>The web dashboard logs each machine transaction — bottles accepted, rewards given, and bin fill level — updated automatically whenever the Arduino sends data via serial communication to XAMPP.</p>
        <a href="pages/data.php" class="btn btn--primary">Open Dashboard →</a>
      </div>
      <div class="machine-live-visual">
        <div class="mini-dashboard">
          <div class="mini-dash-title"> Machine Stats — Today</div>
          <div class="mini-stat-row">
            <span class="mini-label"> Bottles Accepted</span>
            <span class="mini-val mini-val--green">—</span>
          </div>
          <div class="mini-stat-row">
            <span class="mini-label"> Rewards Dispensed</span>
            <span class="mini-val mini-val--yellow">—</span>
          </div>
          <div class="mini-stat-row">
            <span class="mini-label"> Bin Fill Level</span>
            <span class="mini-val mini-val--blue">—</span>
          </div>
          <div class="mini-stat-row">
            <span class="mini-label"> Arduino Status</span>
            <span class="mini-val mini-val--orange">Pending</span>
          </div>
          <div class="mini-dash-note"> Awaiting Arduino connection</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── CTA ── -->
<section class="cta-section">
  <div class="container">
    <div class="cta-box">
      <div class="cta-icon"></div>
      <h2>One machine.<br/><em>A cleaner Muzon.</em></h2>
      <p>Every plastic bottle collected brings Barangay Muzon one step closer to a cleaner, more sustainable community. Learn more about the project or get in touch.</p>
      <div class="cta-btns">
        <a href="pages/about.php" class="btn btn--primary btn--large">About the Project</a>
        <a href="pages/contact.php" class="btn btn--ghost btn--large">Contact Us</a>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="js/main.js"></script>
</body>
</html>
