<?php

$page_title = "Contact | BottleBack";
$success = false; $errors = [];
$fd = ['name'=>'','email'=>'','subject'=>'','message'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $fd['name']    = trim($_POST['name']    ?? '');
  $fd['email']   = trim($_POST['email']   ?? '');
  $fd['subject'] = trim($_POST['subject'] ?? '');
  $fd['message'] = trim($_POST['message'] ?? '');
  if (!$fd['name'])    $errors[] = 'Name is required.';
  if (!$fd['email'])   $errors[] = 'Email is required.';
  elseif (!filter_var($fd['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';
  if (!$fd['subject']) $errors[] = 'Subject is required.';
  if (!$fd['message']) $errors[] = 'Message is required.';
  if (empty($errors)) {
    try {
      $conn = new mysqli('localhost','root','','bottleback');
      if (!$conn->connect_error) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name,email,subject,message) VALUES (?,?,?,?)");
        $stmt->bind_param('ssss',$fd['name'],$fd['email'],$fd['subject'],$fd['message']);
        $stmt->execute(); $stmt->close(); $conn->close();
      }
    } catch(Exception $e){ /* silently skip if DB not ready */ }
    $success = true;
    $fd = ['name'=>'','email'=>'','subject'=>'','message'=>''];
  }
}
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
    <div class="section-label section-label--light">Get in Touch</div>
    <h1 class="page-hero__title">Contact <em>Us</em></h1>
    <p class="page-hero__sub">Questions about the BottleBack project? Interested in learning more or collaborating? Send us a message.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="contact-grid">
      <div class="contact-info">
        <h3>About this <em>project</em></h3>
        <p>BottleBack is a BSIT Capstone Project from the College of Computer Studies, Our Lady of Fatima University, Antipolo City, aimed at addressing plastic bottle waste in Barangay Muzon, Taytay, Rizal.</p>
        <div class="contact-cards">
          <div class="contact-card">
            <span></span>
            <div>
              <strong>Students &amp; Researchers</strong>
              <p>Curious about our methodology, hardware design, or sensor setup? We're happy to share insights for your own research.</p>
            </div>
          </div>
          <div class="contact-card">
            <span></span>
            <div>
              <strong>Barangay Muzon Residents</strong>
              <p>Want to know where the machine will be placed or how to use it? Reach out and we'll keep you updated.</p>
            </div>
          </div>
          <div class="contact-card">
            <span></span>
            <div>
              <strong>Barangay Officials &amp; LGUs</strong>
              <p>Interested in integrating this machine into your waste management program or partnering with us?</p>
            </div>
          </div>
          <div class="contact-card">
            <span></span>
            <div>
              <strong>Media &amp; Advocates</strong>
              <p>Covering environmental technology or community innovation? We'd love to share our story.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="contact-form-wrap">
        <?php if ($success): ?>
        <div class="form-success">
          <div class="form-success__icon"></div>
          <h3>Message sent!</h3>
          <p>Thank you for reaching out to the BottleBack research team. We'll get back to you as soon as possible.</p>
          <a href="contact.php" class="btn btn--outline">Send another message</a>
        </div>
        <?php else: ?>
        <?php if (!empty($errors)): ?>
        <div class="form-errors">
          <ul><?php foreach($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul>
        </div>
        <?php endif; ?>
        <form class="eco-form" method="POST" action="contact.php">
          <div class="form-row">
            <div class="form-group">
              <label for="name">Your Name *</label>
              <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($fd['name']); ?>" placeholder="Juan dela Cruz" required/>
            </div>
            <div class="form-group">
              <label for="email">Email Address *</label>
              <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($fd['email']); ?>" placeholder="juan@email.com" required/>
            </div>
          </div>
          <div class="form-group">
            <label for="subject">Subject *</label>
            <select id="subject" name="subject" required>
              <option value="" disabled <?php echo !$fd['subject']?'selected':''; ?>>Choose a topic...</option>
              <option value="general"       <?php echo $fd['subject']==='general'?'selected':'';?>>General Inquiry</option>
              <option value="hardware"      <?php echo $fd['subject']==='hardware'?'selected':'';?>>Arduino / Hardware Questions</option>
              <option value="software"      <?php echo $fd['subject']==='software'?'selected':'';?>>Software / Dashboard Questions</option>
              <option value="research"      <?php echo $fd['subject']==='research'?'selected':'';?>>Research Collaboration</option>
              <option value="barangay"      <?php echo $fd['subject']==='barangay'?'selected':'';?>>Barangay / Community Deployment</option>
              <option value="media"         <?php echo $fd['subject']==='media'?'selected':'';?>>Media / Press Inquiry</option>
              <option value="feedback"      <?php echo $fd['subject']==='feedback'?'selected':'';?>>Feedback / Suggestions</option>
            </select>
          </div>
          <div class="form-group">
            <label for="message">Message *</label>
            <textarea id="message" name="message" rows="6" placeholder="Tell us more about your inquiry..." required><?php echo htmlspecialchars($fd['message']); ?></textarea>
          </div>
          <button type="submit" class="btn btn--primary btn--full">Send Message</button>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php include '../includes/footer.php'; ?>
<script src="../js/main.js"></script>
</body>
</html>
