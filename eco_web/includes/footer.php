<?php
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
$base = ($current_dir === 'pages') ? '../' : '';
$pb   = ($current_dir === 'pages') ? '' : 'pages/';
?>
<footer class="footer">
  <div class="footer__inner container">
    <div class="footer__brand">
      <a href="<?php echo $base; ?>index.php" class="navbar__logo" style="display:flex;margin-bottom:.9rem">
        <span class="logo-leaf"></span>
        <span class="logo-text" style="color:#fff">Bottle<strong style="color:#73c48a">Back</strong></span>
      </a>
      <p>An Arduino-Based Plastic Bottle Vending Machine to Support Environmental Awareness for Barangay Muzon, Taytay Rizal.</p>
      <p style="margin-top:.6rem;font-size:.8rem;color:rgba(255,255,255,.35);">Our Lady of Fatima University<br/>College of Computer Studies · Antipolo City<br/>BSIT Capstone Project ·</p>
      <div class="footer__badges">
        <span> Arduino</span>
        <span> Brgy. Muzon</span>
        <span> Recycle</span>
        <span> Earn Rewards</span>
      </div>
    </div>
    <div class="footer__links">
      <h4>Quick Links</h4>
      <ul>
        <li><a href="<?php echo $base; ?>index.php">Home</a></li>
        <li><a href="<?php echo $pb; ?>about.php">About the Project</a></li>
        <li><a href="<?php echo $pb; ?>how-it-works.php">How It Works</a></li>
        <li><a href="<?php echo $pb; ?>data.php">Live Data Dashboard</a></li>
        <li><a href="<?php echo $pb; ?>awareness.php">Plastic Awareness</a></li>
        <li><a href="<?php echo $pb; ?>contact.php">Contact</a></li>
      </ul>
    </div>
    <div class="footer__links">
      <h4>The Researchers</h4>
      <ul>
        <li><a href="<?php echo $pb; ?>about.php">Angeles, Alyza Mae</a></li>
        <li><a href="<?php echo $pb; ?>about.php">Despabiladeras, Marnes</a></li>
        <li><a href="<?php echo $pb; ?>about.php">Ellema, Jessica A.</a></li>
        <li><a href="<?php echo $pb; ?>about.php">San Marcos, Nick Anjelo</a></li>
        <li><a href="<?php echo $pb; ?>about.php">Soriano, Lemuel Jaaziah</a></li>
      </ul>
    </div>
    <div class="footer__mission">
      <h4>Project Mission</h4>
      <p>To promote environmental awareness and increase plastic bottle recycling rates in Barangay Muzon, Taytay, Rizal through an Arduino-based reward system — making recycling simple, interactive, and rewarding for every resident.</p>
      <p style="margin-top:.7rem;font-size:.82rem;color:rgba(255,255,255,.3);">Aligned with RA 11898 — Extended Producer<br/>Responsibility Act of 2022</p>
    </div>
  </div>
  <div class="footer__bottom">
    <div class="container">
      <p>&copy; <?php echo date('Y'); ?> BottleBack — An Arduino-Based Plastic Bottle Vending Machine · Our Lady of Fatima University · Barangay Muzon, Taytay, Rizal</p>
    </div>
  </div>
</footer>
