<?php 

$page_title = "Plastic Awareness | BottleBack"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo $page_title; ?></title>
  <link rel="stylesheet" href="../css/style.css"/>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
<?php include '../includes/nav.php'; ?>

<section class="page-hero">
  <div class="container">
    <div class="section-label section-label--light">Environmental Education</div>
    <h1 class="page-hero__title">Plastic <em>Awareness</em></h1>
    <p class="page-hero__sub">Understanding the plastic problem in Barangay Muzon and the Philippines is the first step toward solving it.</p>
  </div>
</section>

<!-- THE LOCAL PROBLEM -->
<section class="section">
  <div class="container">
    <div class="about-grid" style="gap:4rem">
      <div class="about-text">
        <div class="section-label">The Local Context</div>
        <h2 class="section-title">The plastic problem in<br/><em>Barangay Muzon</em></h2>
        <p>Barangay Muzon, Taytay, Rizal is a growing community where residents frequently use plastic bottles for drinking water and other beverages. These bottles are often improperly disposed of, contributing to <strong>pollution and clogged drainage systems</strong> throughout the barangay.</p>
        <p>In the Philippines, around <strong>35,580 tons of garbage</strong> are produced every day, with each person generating about half a kilogram of waste daily. A significant portion of this is plastic bottle waste — material that can be recycled but often isn't, simply due to a lack of motivation and accessible collection points.</p>
        <p>The DENR in CALABARZON has urged the public to support the <em>"Beat Plastic Pollution"</em> campaign — and this machine is Barangay Muzon's direct response to that call.</p>
      </div>
      <div class="about-visual">
        <div class="fact-highlight-stack">
          <div class="fact-highlight fact-highlight--red">
            <div class="fh-num">35,580</div>
            <div class="fh-label">Tons of garbage produced in the Philippines daily</div>
          </div>
          <div class="fact-highlight fact-highlight--orange">
            <div class="fh-num">450 yrs</div>
            <div class="fh-label">Time for one PET bottle to decompose in a landfill</div>
          </div>
          <div class="fact-highlight fact-highlight--green">
            <div class="fh-num">153</div>
            <div class="fh-label">Active MRFs in Rizal barangays as of 2024</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- GLOBAL FACTS -->
<section class="section section--dark">
  <div class="container">
    <div class="section-header">
      <div class="section-label section-label--light">Global Statistics</div>
      <h2 class="section-title section-title--light">Plastic by the <em>numbers</em></h2>
    </div>
    <div class="fact-cards-grid">
      <?php
      $facts = [
        ['val'=>'300M','unit'=>'tons','desc'=>'of plastic produced globally every year','color'=>'red'],
        ['val'=>'9%',  'unit'=>'only','desc'=>'of all plastic ever made has been recycled','color'=>'orange'],
        ['val'=>'450', 'unit'=>'years','desc'=>'for a PET bottle to fully decompose in landfill','color'=>'earth'],
        ['val'=>'8M+', 'unit'=>'tons','desc'=>'of plastic waste enter the world\'s oceans each year','color'=>'blue'],
        ['val'=>'1M',  'unit'=>'bottles','desc'=>'plastic bottles bought around the world every minute','color'=>'teal'],
        ['val'=>'91%', 'unit'=>'never','desc'=>'of plastic never recycled — goes to landfill or the environment','color'=>'purple'],
      ];
      foreach ($facts as $f): ?>
      <div class="fact-card fact-card--<?php echo $f['color']; ?>">
        <div class="fact-val"><?php echo $f['val']; ?></div>
        <div class="fact-unit"><?php echo $f['unit']; ?></div>
        <p><?php echo $f['desc']; ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- LIFECYCLE COMPARISON -->
<section class="section section--dark" style="padding-top:0">
  <div class="container">
    <div class="section-header">
      <div class="section-label section-label--light">The Difference We Make</div>
      <h2 class="section-title section-title--light">A bottle's journey —<br/><em>with and without BottleBack</em></h2>
    </div>
    <div class="lifecycle-grid">
      <div class="lifecycle-path lifecycle-path--bad">
        <div class="lifecycle-path__label lifecycle-path__label--bad">Without Recycling</div>
        <div class="lifecycle-steps">
          <div class="lc-step"><span>Purchased &amp; consumed</span></div>
          <div class="lc-arrow">↓</div>
          <div class="lc-step bad"><span>Thrown in street or open trash</span></div>
          <div class="lc-arrow">↓</div>
          <div class="lc-step bad"><span>Clogs drainage in Barangay Muzon</span></div>
          <div class="lc-arrow">↓</div>
          <div class="lc-step bad"><span>Ends up in landfill for 450 years</span></div>
          <div class="lc-arrow">↓</div>
          <div class="lc-step bad"><span>Leaches into waterways &amp; pollutes ecosystems</span></div>
        </div>
      </div>
      <div class="lifecycle-vs">VS</div>
      <div class="lifecycle-path lifecycle-path--good">
        <div class="lifecycle-path__label lifecycle-path__label--good">With BottleBack</div>
        <div class="lifecycle-steps">
          <div class="lc-step"><span>Purchased &amp; consumed</span></div>
          <div class="lc-arrow">↓</div>
          <div class="lc-step good"><span>Inserted into BottleBack machine</span></div>
          <div class="lc-arrow">↓</div>
          <div class="lc-step good"><span>Resident receives free drink or biscuit</span></div>
          <div class="lc-arrow">↓</div>
          <div class="lc-step good"><span>Bottles collected &amp; sent to recycling facility</span></div>
          <div class="lc-arrow">↓</div>
          <div class="lc-step good"><span>Plastic reprocessed into new materials</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- WHY REWARDS WORK -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <div class="section-label">Why It Works</div>
      <h2 class="section-title">The science behind<br/><em>reward-based recycling</em></h2>
    </div>
    <div class="research-grid">
      <div class="research-card research-card--light">
        <div class="research-icon"></div>
        <h4>Behavioral Psychology</h4>
        <p>Positive reinforcement — receiving a reward immediately after an action — is one of the most effective methods of building lasting habits. Applied to recycling, it transforms an obligation into a desired behavior.</p>
      </div>
      <div class="research-card research-card--light">
        <div class="research-icon"></div>
        <h4>Proven Results Globally</h4>
        <p>Countries with deposit-refund systems (Germany, Norway) achieve PET bottle recycling rates of over 90%, compared to the global average of under 30%. Rewards make the difference.</p>
      </div>
      <div class="research-card research-card--light">
        <div class="research-icon"></div>
        <h4>Local Community Context</h4>
        <p>In Barangay Muzon, practical rewards like free drinks and biscuits are immediately useful to residents — making the incentive highly relevant and motivating for daily participation.</p>
      </div>
    </div>
  </div>
</section>

<!-- TIPS -->
<section class="section section--light">
  <div class="container">
    <div class="section-header">
      <div class="section-label">Take Action</div>
      <h2 class="section-title">What <em>you can do</em> as a resident</h2>
    </div>
    <div class="tips-grid">
      <div class="tip-card">
        <span class="tip-num">01</span>
        <h4>Use the BottleBack Machine</h4>
        <p>The simplest action — bring your used plastic bottles to the machine, deposit them, and earn your reward. Make it a daily habit.</p>
      </div>
      <div class="tip-card">
        <span class="tip-num">02</span>
        <h4>Segregate Waste at Home</h4>
        <p>Separate plastic bottles from biodegradable and other waste. Clean, dry bottles are more easily accepted by the machine and recycling facilities.</p>
      </div>
      <div class="tip-card">
        <span class="tip-num">03</span>
        <h4>Spread Awareness</h4>
        <p>Tell your neighbors, friends, and family about the BottleBack machine and the plastic pollution problem in Barangay Muzon. Awareness multiplies impact.</p>
      </div>
      <div class="tip-card">
        <span class="tip-num">04</span>
        <h4>Use a Reusable Bottle</h4>
        <p>The best plastic bottle is one you never buy. A reusable water bottle saves money, reduces waste, and helps keep the barangay clean.</p>
      </div>
      <div class="tip-card">
        <span class="tip-num">05</span>
        <h4>Join Barangay Clean-Up Drives</h4>
        <p>Participate in community clean-up activities. Collected plastic bottles can be deposited into BottleBack machines for proper processing.</p>
      </div>
      <div class="tip-card">
        <span class="tip-num">06</span>
        <h4>Support RA 11898</h4>
        <p>Know your rights and the law. The Extended Producer Responsibility Act of 2022 supports recycling initiatives like this one — be part of the solution.</p>
      </div>
    </div>
  </div>
</section>

<!-- PLASTIC TYPE GUIDE -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <div class="section-label">What the Machine Accepts</div>
      <h2 class="section-title">Know your <em>plastic types</em></h2>
      <p class="section-intro">BottleBack is designed to accept <strong>PET (Type 1)</strong> plastic bottles — the most common water and beverage bottle type in Barangay Muzon and across the Philippines.</p>
    </div>
    <div class="plastic-types-grid">
      <?php
      $plastics = [
        ['code'=>'1','name'=>'PET / PETE','examples'=>'Water bottles, soda bottles, juice bottles','accepted'=>true],
        ['code'=>'2','name'=>'HDPE',      'examples'=>'Milk jugs, shampoo bottles, detergent containers','accepted'=>false],
        ['code'=>'3','name'=>'PVC',       'examples'=>'Pipes, some food wrap','accepted'=>false],
        ['code'=>'4','name'=>'LDPE',      'examples'=>'Plastic bags, squeezable bottles','accepted'=>false],
        ['code'=>'5','name'=>'PP',        'examples'=>'Yogurt containers, medicine bottles','accepted'=>false],
        ['code'=>'6','name'=>'PS',        'examples'=>'Styrofoam cups, disposable plates','accepted'=>false],
      ];
      foreach ($plastics as $p): ?>
      <div class="plastic-card <?php echo $p['accepted']?'plastic-card--accepted':'plastic-card--not-accepted'; ?>">
        <div class="plastic-code"><?php echo $p['code']; ?></div>
        <div class="plastic-body">
          <strong><?php echo $p['name']; ?></strong>
          <p><?php echo $p['examples']; ?></p>
          <span class="plastic-badge <?php echo $p['accepted']?'plastic-badge--yes':'plastic-badge--no'; ?>">
            <?php echo $p['accepted']?'Accepted by BottleBack':'Not accepted'; ?>
          </span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include '../includes/footer.php'; ?>
<script src="../js/main.js"></script>
</body>
</html>
