import { Link } from 'react-router-dom'

export default function Footer() {
  const year = new Date().getFullYear()
  return (
    <footer className="footer">
      <div className="footer__inner container">
        <div className="footer__brand">
          <Link to="/" className="navbar__logo" style={{ display: 'flex', marginBottom: '.9rem' }}>
            <span className="logo-text" style={{ color: '#fff' }}>Bottle<strong style={{ color: '#73c48a' }}>Back</strong></span>
          </Link>
          <p>An Arduino-Based Plastic Bottle Vending Machine to Support Environmental Awareness for Barangay Muzon, Taytay Rizal.</p>
          <p style={{ marginTop: '.6rem', fontSize: '.8rem', color: 'rgba(255,255,255,.35)' }}>
            Our Lady of Fatima University<br />
            College of Computer Studies · Antipolo City<br />
            BSIT Capstone Project ·
          </p>
          <div className="footer__badges">
            <span>Arduino</span>
            <span>Brgy. Muzon</span>
            <span>Recycle</span>
            <span>Earn Rewards</span>
          </div>
        </div>
        <div className="footer__links">
          <h4>Quick Links</h4>
          <ul>
            <li><Link to="/">Home</Link></li>
            <li><Link to="/about">About the Project</Link></li>
            <li><Link to="/how-it-works">How It Works</Link></li>
            <li><Link to="/data">Live Data Dashboard</Link></li>
            <li><Link to="/awareness">Plastic Awareness</Link></li>
            <li><Link to="/contact">Contact</Link></li>
          </ul>
        </div>
        <div className="footer__links">
          <h4>The Researchers</h4>
          <ul>
            <li><Link to="/about">Angeles, Alyza Mae</Link></li>
            <li><Link to="/about">Despabiladeras, Marnes</Link></li>
            <li><Link to="/about">Ellema, Jessica A.</Link></li>
            <li><Link to="/about">San Marcos, Nick Anjelo</Link></li>
            <li><Link to="/about">Soriano, Lemuel Jaaziah</Link></li>
          </ul>
        </div>
        <div className="footer__mission">
          <h4>Project Mission</h4>
          <p>To promote environmental awareness and increase plastic bottle recycling rates in Barangay Muzon, Taytay, Rizal through an Arduino-based reward system — making recycling simple, interactive, and rewarding for every resident.</p>
          <p style={{ marginTop: '.7rem', fontSize: '.82rem', color: 'rgba(255,255,255,.3)' }}>
            Aligned with RA 11898 — Extended Producer<br />Responsibility Act of 2022
          </p>
        </div>
      </div>
      <div className="footer__bottom">
        <div className="container">
          <p>&copy; {year} BottleBack — An Arduino-Based Plastic Bottle Vending Machine · Our Lady of Fatima University · Barangay Muzon, Taytay, Rizal</p>
        </div>
      </div>
    </footer>
  )
}
