<?php 

$page_title = "How It Works | BottleBack"; ?>
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
    <div class="section-label section-label--light">Technical Overview</div>
    <h1 class="page-hero__title">How It <em>Works</em></h1>
    <p class="page-hero__sub">A complete breakdown of the machine's hardware, software, and process flow — from bottle insertion to reward dispensing.</p>
  </div>
</section>

<!-- PROCESS FLOW -->
<section class="section" id="flow">
  <div class="container">
    <div class="section-header">
      <div class="section-label">Step by Step</div>
      <h2 class="section-title">Complete <em>Process Flow</em></h2>
    </div>
    <div class="flow-detail-list">
      <?php
      $steps = [
        ['icon'=>'','num'=>'Step 1','title'=>'Resident Inserts Plastic Bottle',
         'desc'=>'The resident places a used PET plastic bottle into the front input slot of the machine. The slot is sized to accept standard plastic bottles commonly found in Barangay Muzon.',
         'tech'=>'Physical enclosure with designed input slot'],
        ['icon'=>'','num'=>'Step 2','title'=>'IR Sensor Detects Bottle',
         'desc'=>'An infrared (IR) sensor positioned inside the input slot detects the presence of an object and sends a signal to the Arduino to begin the validation process.',
         'tech'=>'FC-51 Infrared Obstacle Sensor Module'],
        ['icon'=>'','num'=>'Step 3','title'=>'Ultrasonic Sensor Validates Size',
         'desc'=>'An HC-SR04 ultrasonic sensor measures the distance to the bottle. The Arduino checks whether the reading falls within the acceptable range for a valid plastic bottle.',
         'tech'=>'HC-SR04 Ultrasonic Distance Sensor'],
        ['icon'=>'','num'=>'Step 4','title'=>'Arduino Processes & Decides',
         'desc'=>'Based on sensor readings, the Arduino decides: Accept or Reject. If valid, the servo motor opens the gate. If invalid, the LCD shows an error and the bottle is returned.',
         'tech'=>'Arduino Uno / Mega microcontroller (C++ logic)'],
        ['icon'=>'','num'=>'Step 5','title'=>'Servo Motor Opens Gate',
         'desc'=>'A servo motor rotates to open the acceptance gate, allowing the bottle to fall into the collection bin. It then closes automatically to await the next bottle.',
         'tech'=>'SG90 / MG996R Servo Motor'],
        ['icon'=>'','num'=>'Step 6','title'=>'LCD Shows Feedback',
         'desc'=>'The 16×2 LCD display shows the result — "Bottle Accepted!" or "Invalid Item" — giving the resident clear, immediate visual feedback on the transaction.',
         'tech'=>'16×2 I2C LCD Display Module'],
        ['icon'=>'','num'=>'Step 7','title'=>'Reward Dispensed',
         'desc'=>'Upon acceptance, the machine dispenses a reward — a free drink or biscuit — as a tangible incentive. This reward-based mechanism is the core behavior-change driver of the system.',
         'tech'=>'Reward dispenser mechanism (drinks / biscuits)'],
        ['icon'=>'','num'=>'Step 8','title'=>'Data Sent to Web Dashboard',
         'desc'=>'The Arduino sends transaction data via serial communication to the connected PC running XAMPP. A PHP script receives the data and stores it in MySQL for real-time monitoring on the web dashboard.',
         'tech'=>'Serial → PHP receive_data.php → MySQL (XAMPP)'],
      ];
      foreach ($steps as $i => $s): ?>
      <div class="flow-detail-item <?php echo $i%2===1?'flow-detail-item--alt':''; ?>">
        <div class="flow-detail-icon"><?php echo $s['icon']; ?></div>
        <div class="flow-detail-body">
          <div class="flow-step-label"><?php echo $s['num']; ?></div>
          <h3><?php echo $s['title']; ?></h3>
          <p><?php echo $s['desc']; ?></p>
          <div class="flow-tech-tag"> <?php echo $s['tech']; ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- HARDWARE BOM -->
<section class="section section--dark" id="hardware">
  <div class="container">
    <div class="section-header">
      <div class="section-label section-label--light">Bill of Materials</div>
      <h2 class="section-title section-title--light">Hardware <em>Components</em></h2>
    </div>
    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr><th>Component</th><th>Model / Specification</th><th>Qty</th><th>Purpose</th></tr>
        </thead>
        <tbody>
          <?php
          $bom = [
            ['Arduino Microcontroller','Arduino Uno R3 / Mega 2560','1','Main controller — processes sensor inputs and controls all outputs'],
            ['IR Sensor Module','FC-51 Infrared Obstacle Sensor','1–2','Detects presence of plastic bottle in the input slot'],
            ['Ultrasonic Sensor','HC-SR04','1','Measures distance to validate bottle size; monitors bin fill level'],
            ['Servo Motor','SG90 or MG996R','1','Controls the acceptance gate — opens on valid bottle, closes after'],
            ['LCD Display','16×2 with I2C adapter','1','Shows feedback to the resident — "Accepted" or "Invalid"'],
            ['Buzzer','5V Active Buzzer','1','Audio feedback for accepted / rejected bottle events'],
            ['LED Indicators','Green / Red 5mm LEDs','2–4','Visual accept/reject signal for the user'],
            ['Power Supply','5V 2A DC Adapter','1','Powers Arduino and all connected peripherals'],
            ['Jumper Wires','Male-to-male / Male-to-female','30+','Circuit connections between components'],
            ['Breadboard / PCB','Full-size breadboard','1','Prototyping and circuit assembly'],
            ['Machine Enclosure','Wood / PVC / Acrylic box','1','Physical housing for the entire system'],
            ['Reward Dispenser','Mechanical dispenser tray','1','Holds and releases drinks or biscuits as rewards'],
          ];
          foreach ($bom as $row): ?>
          <tr><?php foreach ($row as $cell): ?><td><?php echo $cell; ?></td><?php endforeach; ?></tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- SOFTWARE STACK -->
<section class="section" id="software">
  <div class="container">
    <div class="section-header">
      <div class="section-label">Software Architecture</div>
      <h2 class="section-title">The <em>Software Stack</em></h2>
    </div>
    <div class="software-stack">
      <div class="sw-layer sw-layer--arduino">
        <div class="sw-layer-label"> Layer 1 — Arduino Firmware (C++)</div>
        <div class="sw-layer-items">
          <span>IR sensor reading loop</span>
          <span>Ultrasonic distance measurement</span>
          <span>Bottle validation logic (if/else)</span>
          <span>Servo motor control (open / close gate)</span>
          <span>LCD display output ("Accepted" / "Invalid")</span>
          <span>Buzzer & LED feedback</span>
          <span>Serial.println() data output to PC</span>
        </div>
      </div>
      <div class="sw-arrow">↓ &nbsp; Serial COM Port (USB) &nbsp; ↓</div>
      <div class="sw-layer sw-layer--php">
        <div class="sw-layer-label"> Layer 2 — PHP Web Application (XAMPP / Apache)</div>
        <div class="sw-layer-items">
          <span>api/receive_data.php — receives POST from serial bridge</span>
          <span>pages/data.php — live dashboard display</span>
          <span>pages/about.php — project overview</span>
          <span>pages/how-it-works.php — technical details</span>
          <span>pages/awareness.php — environmental education</span>
          <span>pages/contact.php — contact form with validation</span>
        </div>
      </div>
      <div class="sw-arrow">↓ &nbsp; MySQLi Queries &nbsp; ↓</div>
      <div class="sw-layer sw-layer--db">
        <div class="sw-layer-label"> Layer 3 — myPhp Database (bottleback)</div>
        <div class="sw-layer-items">
          <span>transactions — one row per bottle event (accepted / rejected)</span>
          <span>machine_status — live bin level & online status</span>
          <span>contact_messages — contact form submissions</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- RELATED STUDIES HIGHLIGHT -->
<section class="section section--light">
  <div class="container">
    <div class="section-header">
      
      <h2 class="section-title">Studies that support <em>this project</em></h2>
    </div>
    <div class="research-grid">
      <?php
      $studies = [
        ['icon'=>'','title'=>'Design of a Plastic Bottle Recycling Machine',
         'authors'=>'Arslan & Tahan (2021)',
         'note'=>'Shows how reverse vending machines make recycling easier and more accessible by offering rewards, supporting this project\'s core approach.'],
        ['icon'=>'','title'=>'Arduino-Based Waste ATM for Recycling Awareness',
         'authors'=>'Jamas, Irwanto & Permata (2024)',
         'note'=>'Demonstrates automated plastic bottle collection using Arduino and capacitive/ultrasonic sensors — directly paralleling this project\'s hardware design.'],
        ['icon'=>'','title'=>'Smart Reverse Vending Machine for Plastic Bottles',
         'authors'=>'Rao et al. (2023)',
         'note'=>'Uses Arduino Uno, IR sensors, and load cell to detect and validate bottles. Users receive instant coin rewards — similar reward-based model.'],
        ['icon'=>'','title'=>'VENDOBIN — IoT-Based Plastic Bottle Disposal Machine',
         'authors'=>'Dacay et al., USTP (2023)',
         'note'=>'Local Philippine study combining vending machine and garbage bin concepts — alerting authorities when full, tracking user activity. Closest local parallel.'],
        ['icon'=>'','title'=>'Arduino Ballpoint Pen Vending Machine — Plastic Bottle Exchange',
         'authors'=>'Boto et al. (2023)',
         'note'=>'Philippine study showing high user satisfaction when Arduino dispenses school supplies in exchange for empty plastic bottles — confirms reward-based acceptance.'],
        ['icon'=>'','title'=>'Republic Act No. 11898 — Extended Producer Responsibility Act',
         'authors'=>'Philippine Government (2022)',
         'note'=>'Provides legal foundation for the project. The proposed machine directly supports RA 11898\'s goals of plastic waste recovery and community recycling.'],
      ];
      foreach ($studies as $s): ?>
      <div class="research-card">
        <div class="research-icon"><?php echo $s['icon']; ?></div>
        <h4><?php echo $s['title']; ?></h4>
        <div class="research-authors"><?php echo $s['authors']; ?></div>
        <p><?php echo $s['note']; ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include '../includes/footer.php'; ?>
<script src="../js/main.js"></script>
</body>
</html>
