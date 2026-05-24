<?php 
require_once '../config.php'; 

$page_title = "About the Project | BottleBack"; 
?>
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
    <div class="section-label section-label--light">BSIT Capstone — June 2027</div>
    <h1 class="page-hero__title">About <em>the Project</em></h1>
    <p class="page-hero__sub">An Arduino-Based Plastic Bottle Vending Machine to Support Environmental Awareness for Barangay Muzon, Taytay Rizal</p>
    <p class="page-hero__meta">Our Lady of Fatima University &nbsp;·&nbsp; College of Computer Studies &nbsp;·&nbsp; Antipolo City</p>
  </div>
</section>

<!-- RESEARCHERS -->
<section class="section section--dark">
  <div class="container">
    <div class="section-header">
      <div class="section-label section-label--light">The Research Team</div>
      <h2 class="section-title section-title--light">Presented <em>by</em></h2>
    </div>
    <div class="researchers-grid">
      <?php
      $researchers = [
        ['name'=>'Angeles, Alyza Mae',      'icon'=>''],
        ['name'=>'Despabiladeras, Marnes',   'icon'=>''],
        ['name'=>'Ellema, Jessica A.',        'icon'=>''],
        ['name'=>'San Marcos, Nick Anjelo',  'icon'=>''],
        ['name'=>'Soriano, Lemuel Jaaziah',  'icon'=>''],
      ];
      foreach ($researchers as $r): ?>
      <div class="researcher-card">
        <div class="researcher-icon"><?php echo $r['icon']; ?></div>
        <div class="researcher-name"><?php echo $r['name']; ?></div>
        <div class="researcher-dept">BSIT — OLFU Antipolo City</div>
      </div>
      <?php endforeach; ?>
    </div>
    <p class="researchers-note">In partial fulfillment of the requirements for the degree <strong>Bachelor of Science in Information Technology</strong></p>
  </div>
</section>

<!-- INTRODUCTION -->
<section class="section">
  <div class="container">
    <div class="about-grid" style="gap:4rem">
      <div class="about-text">
       
        <h2 class="section-title">Background of <em>the Study</em></h2>
        <p>Barangay Muzon, Taytay, Rizal is a lively and growing community where residents go about their daily routines, often using plastic bottles and other modern conveniences. As the community continues to develop, there is a growing interest in finding creative ways to engage residents in activities that benefit both them and the environment.</p>
        <p>Plastic bottles are often improperly disposed of, contributing to pollution and clogged drainage systems throughout the barangay. A major challenge in the community is the <strong>lack of convenience and motivation</strong> for residents to recycle. Many residents are willing to help keep the barangay clean but often find it difficult to store and bring recyclables to proper disposal points.</p>
        <p>Technology has opened up new possibilities to make recycling more fun, interactive, and rewarding. Incentive-based and interactive projects not only capture people's interest but also help build habits that can last a lifetime.</p>
      </div>
      <div class="about-visual">
        <div class="info-blocks">
          <div class="info-block info-block--green">
            <h4> General Objective</h4>
            <p>To examine the effectiveness of an Arduino-based plastic bottle vending machine as a tool for promoting recycling behavior among residents of Barangay Muzon, Taytay, Rizal.</p>
          </div>
          <div class="info-block info-block--blue">
            <h4> Study Location</h4>
            <p>Barangay Muzon, Taytay, Rizal — a growing residential community where plastic bottle mismanagement contributes to local pollution and drainage problems.</p>
          </div>
          <div class="info-block info-block--earth">
            <h4> Reward Mechanism</h4>
            <p>Residents receive <strong>free drinks or biscuits</strong> in exchange for each valid plastic bottle deposited — a practical, tangible incentive proven to encourage participation.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- OBJECTIVES -->
<section class="section section--light">
  <div class="container">
    <div class="section-header">
      
      <h2 class="section-title">Specific <em>Objectives of the Study</em></h2>
    </div>
    <div class="objectives-list">
      <?php
      $objectives = [
        ['num'=>'1','title'=>'Assess Feasibility',
         'desc'=>'Assess the feasibility of using an Arduino-based vending machine for the collection of plastic bottles in a community setting like Barangay Muzon, Taytay, Rizal.'],
        ['num'=>'2','title'=>'Determine Sensor Accuracy',
         'desc'=>'Determine the accuracy and reliability of sensor-based detection in identifying and counting plastic bottles inserted into the machine.'],
        ['num'=>'3','title'=>'Analyze Reward Effectiveness',
         'desc'=>'Analyze the effectiveness of a reward-based mechanism — specifically free drinks and biscuits — in encouraging individuals to participate in recycling activities.'],
        ['num'=>'4','title'=>'Evaluate Environmental Awareness',
         'desc'=>'Evaluate the level of environmental awareness and participation in recycling among residents before and after the implementation of the vending machine.'],
        ['num'=>'5','title'=>'Measure Overall Effectiveness',
         'desc'=>'Measure the overall effectiveness of the vending machine in promoting sustainable waste management practices within Barangay Muzon, Taytay, Rizal.'],
      ];
      foreach ($objectives as $o): ?>
      <div class="objective-item">
        <div class="objective-num"><?php echo $o['num']; ?></div>
        <div class="objective-body">
          <h4><?php echo $o['title']; ?></h4>
          <p><?php echo $o['desc']; ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- SCOPE & LIMITATIONS -->
<section class="section section--dark">
  <div class="container">
    <div class="section-header">
      
      <h2 class="section-title section-title--light">What this study <em>covers</em></h2>
    </div>
    <div class="scope-grid">
      <div class="scope-col">
        <h3 class="scope-head scope-head--green">Scope of the Study</h3>
        <ul class="scope-list">
          <li>Examines the use of an Arduino-based vending machine as a tool for encouraging recycling through rewards</li>
          <li>Evaluates how accurately sensors detect and count plastic bottles, affecting reward reliability</li>
          <li>Assesses whether incentives (free drinks or biscuits) motivate residents to participate in recycling</li>
          <li>Collects data within a specific community environment to determine participation and user engagement</li>
          <li>Explores residents' perceptions of recycling and their willingness to engage with technology-based solutions</li>
        </ul>
      </div>
      <div class="scope-col">
        <h3 class="scope-head scope-head--red">Limitations of the Study</h3>
        <ul class="scope-list scope-list--limit">
          <li>Accepts only plastic bottles of certain sizes and types — cannot generalize to all recyclable materials</li>
          <li>Conducted on a small-scale prototype; does not reflect large-scale or commercial implementation</li>
          <li>Sensor accuracy may be affected by crushed, damaged, or wet bottles</li>
          <li>Rewards limited to drinks and biscuits during the study period, which may affect long-term motivation</li>
          <li>Does not cover extended maintenance, cost analysis, or long-term adoption beyond the evaluation period</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- SIGNIFICANCE -->
<section class="section">
  <div class="container">
    <div class="section-header">
      
      <h2 class="section-title">Who benefits from <em>this project</em></h2>
    </div>
    <div class="significance-grid">
      <?php
      $sigs = [
        ['icon'=>'','who'=>'Residents',
         'desc'=>'The machine encourages recycling by offering free products in exchange for plastic bottles, fostering responsible waste management habits.'],
        ['icon'=>'','who'=>'Barangay Officials',
         'desc'=>'Supports local waste management efforts by providing an efficient system for collecting and managing plastic waste within the barangay.'],
        ['icon'=>'','who'=>'Environmental Advocates',
         'desc'=>'The project provides a model for other communities to implement similar incentive-based recycling initiatives.'],
        ['icon'=>'','who'=>'Local Businesses',
         'desc'=>'Offers businesses an opportunity to contribute to environmental sustainability while engaging directly with the local community.'],
        ['icon'=>'','who'=>'College of Computer Studies — OLFU',
         'desc'=>'Strengthens the department\'s academic programs by integrating practical, technology-driven solutions and community-based innovation.'],
        ['icon'=>'','who'=>'Researchers &amp; Future Researchers',
         'desc'=>'Serves as a useful reference for studies related to recycling, environmental awareness, and the use of Arduino technology in community settings.'],
      ];
      foreach ($sigs as $s): ?>
      <div class="significance-card">
        <div class="sig-icon"><?php echo $s['icon']; ?></div>
        <strong><?php echo $s['who']; ?></strong>
        <p><?php echo $s['desc']; ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- DEFINITION OF TERMS -->
<section class="section section--light">
  <div class="container">
    <div class="section-header">
     
      <h2 class="section-title">Key <em>Terms</em></h2>
    </div>
    <div class="terms-grid">
      <?php
      $terms = [
        ['term'=>'Arduino',              'def'=>'A small microcontroller board that controls the vending machine\'s sensors, counting system, and reward dispenser automatically.'],
        ['term'=>'Plastic Bottle Vending Machine','def'=>'An automated device that accepts used plastic bottles from residents and gives small rewards such as drinks or biscuits in return.'],
        ['term'=>'Sensor',               'def'=>'An electronic component that detects when a plastic bottle is inserted into the machine and helps count it accurately for proper reward issuance.'],
        ['term'=>'Reward-based Mechanism','def'=>'A system that motivates residents to recycle by offering free items or incentives in exchange for depositing plastic bottles.'],
        ['term'=>'Reverse Vending Machine (RVM)','def'=>'A special type of vending machine where people deposit empty containers, and the machine provides a reward or incentive in return.'],
        ['term'=>'Environmental Awareness','def'=>'Understanding why it is important to keep the community clean, reduce plastic pollution, and properly dispose of waste materials.'],
        ['term'=>'IR Sensor (Infrared Sensor)','def'=>'A sensor that detects the presence of a plastic bottle as it enters the machine, helping verify that a bottle was deposited.'],
        ['term'=>'Ultrasonic Sensor',    'def'=>'A sensor that measures distance and is used to detect when the machine\'s storage bin is already full, and to validate bottle size.'],
        ['term'=>'Incentive',            'def'=>'A reward given to encourage residents to participate in recycling activities and develop environmentally responsible habits.'],
        ['term'=>'Barangay Muzon',       'def'=>'The specific community in Taytay, Rizal where the study was conducted and the vending machine was deployed and tested.'],
      ];
      foreach ($terms as $t): ?>
      <div class="term-card">
        <strong><?php echo $t['term']; ?></strong>
        <p><?php echo $t['def']; ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include '../includes/footer.php'; ?>
<script src="../js/main.js"></script>
</body>
</html>
