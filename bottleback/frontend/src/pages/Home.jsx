import { useEffect, useRef, useState } from 'react'
import { Link } from 'react-router-dom'
import Nav from '../components/Nav'
import Footer from '../components/Footer'

export default function Home() {
  const counterRef = useRef(null)
  const pointsRef = useRef(null)
  const [bottles, setBottles] = useState(0)
  const [points, setPoints] = useState(0)

  useEffect(() => {
    document.title = 'BottleBack | An Arduino-Based Plastic Bottle Vending Machine'
    let b = 0, p = 0
    function tick() {
      const interval = Math.random() * 8000 + 4000
      setTimeout(() => {
        b++; p += Math.floor(Math.random() * 3) + 1
        setBottles(b); setPoints(p)
        tick()
      }, interval)
    }
    tick()

    // Scroll reveal
    const targets = document.querySelectorAll(
      '.step-card,.project-badge,.research-card,.significance-card,.tip-card,.kpi-card,.info-block'
    )
    if ('IntersectionObserver' in window && targets.length) {
      const obs = new IntersectionObserver(entries => {
        entries.forEach((e, i) => {
          if (e.isIntersecting) {
            e.target.style.animation = `slideUp .5s ${parseFloat(e.target.dataset.delay || 0)}s ease both`
            obs.unobserve(e.target)
          }
        })
      }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' })
      targets.forEach((el, i) => {
        el.style.opacity = '0'
        el.dataset.delay = (i % 6) * 0.07
        obs.observe(el)
      })
    }
  }, [])

  const researchers = [
    'Angeles, Alyza Mae', 'Despabiladeras, Marnes', 'Ellema, Jessica A.',
    'San Marcos, Nick Anjelo', 'Soriano, Lemuel Jaaziah',
  ]
  const beneficiaries = [
    { who: 'Residents', benefit: 'Earn free products by recycling plastic bottles, building responsible waste management habits.' },
    { who: 'Barangay Officials', benefit: 'Gain an efficient system for collecting and managing plastic waste at the community level.' },
    { who: 'Environmental Advocates', benefit: 'A replicable model other communities can adopt for similar recycling initiatives.' },
    { who: 'Local Businesses', benefit: 'Opportunity to contribute to sustainability while engaging meaningfully with the community.' },
    { who: 'CCS Department (OLFU)', benefit: 'Strengthens academic programs through practical, technology-driven community-based innovation.' },
    { who: 'Future Researchers', benefit: 'A reference for future studies integrating technology into community-based recycling programs.' },
  ]

  return (
    <>
      <Nav />

      {/* HERO */}
      <section className="hero">
        <div className="hero__bg">
          <div className="hero__orb orb1"></div>
          <div className="hero__orb orb2"></div>
          <div className="hero__orb orb3"></div>
          <div className="hero__grid-lines"></div>
        </div>
        <div className="hero__content">
          <div className="hero__eyebrow">
            <span className="pulse-dot"></span>
            Our Lady of Fatima University &nbsp;·&nbsp; College of Computer Studies &nbsp;·&nbsp; BSIT Capstone 2026
          </div>
          <h1 className="hero__title">
            Recycle a Bottle.<br />
            <em>Earn a Reward.</em><br />
            Help Barangay Muzon.
          </h1>
          <p className="hero__sub">
            An Arduino-Based Plastic Bottle Vending Machine to Support Environmental Awareness
            for Barangay Muzon, Taytay Rizal — making recycling simple, interactive, and rewarding.
          </p>
          <div className="hero__cta">
            <Link to="/about" className="btn btn--primary">About the Project</Link>
            <Link to="/how-it-works" className="btn btn--ghost">How It Works →</Link>
          </div>
          <div className="hero__machine-card">
            <div className="machine-card__screen">
              <div className="machine-screen__header">
                <span className="msh-dot msh-dot--green"></span>
                <span>BottleBack — Barangay Muzon</span>
                <span className="msh-status">READY</span>
              </div>
              <div className="machine-screen__body">
                <div className="mscreen-row">
                  <span>Bottles collected today</span>
                  <strong className="mscreen-val">{bottles}</strong>
                </div>
                <div className="mscreen-row">
                  <span>Rewards dispensed</span>
                  <strong className="mscreen-val">{points}</strong>
                </div>
                <div className="mscreen-row">
                  <span>Bin capacity</span>
                  <strong className="mscreen-val mscreen-val--ok">OK</strong>
                </div>
              </div>
              <div className="machine-screen__footer">Insert plastic bottle to begin ▶</div>
            </div>
          </div>
        </div>
        <div className="hero__scroll"><span>Scroll</span><div className="hero__scroll-line"></div></div>
      </section>

      {/* STATS BANNER */}
      <section className="stats-banner">
        <div className="stats-banner__inner">
          <div className="stat-item"><span className="stat-num">35,580</span><span className="stat-label">Tons of garbage produced in PH daily</span></div>
          <div className="stat-divider"></div>
          <div className="stat-item"><span className="stat-num">450 yrs</span><span className="stat-label">For 1 PET bottle to fully decompose</span></div>
          <div className="stat-divider"></div>
          <div className="stat-item"><span className="stat-num">RA 11898</span><span className="stat-label">PH Extended Producer Responsibility Act 2022</span></div>
          <div className="stat-divider"></div>
          <div className="stat-item"><span className="stat-num">153</span><span className="stat-label">Active MRFs in Rizal barangays (2024)</span></div>
        </div>
      </section>

      {/* ABOUT */}
      <section className="section about-section">
        <div className="container">
          <div className="about-grid">
            <div className="about-text">
              <div className="section-label">The Capstone Project</div>
              <h2 className="section-title">An Arduino-Based<br /><em>Plastic Bottle Vending Machine</em><br />to Support Environmental Awareness<br />for Barangay Muzon, Taytay Rizal</h2>
              <p>Presented to the Faculty of the <strong>College of Computer Studies, Our Lady of Fatima University</strong>, Antipolo City — this capstone project proposes a Reverse Vending Machine (RVM) that accepts used plastic bottles from community residents and rewards them with free products such as drinks or biscuits.</p>
              <p>Barangay Muzon, Taytay, Rizal is a growing community where plastic bottles are frequently improperly disposed of, contributing to pollution and clogged drainage. This machine aims to change that — making recycling easy, rewarding, and habit-forming.</p>
              <Link to="/about" className="btn btn--outline">Full Project Overview →</Link>
            </div>
            <div className="about-visual">
              <div className="project-badge-stack">
                <div className="project-badge"><span className="pb-icon"></span><div><strong>Our Lady of Fatima University</strong><p>College of Computer Studies, Antipolo City — BSIT Program, 2026</p></div></div>
                <div className="project-badge project-badge--accent"><span className="pb-icon"></span><div><strong>Barangay Muzon, Taytay Rizal</strong><p>Target community where the machine will be deployed and evaluated</p></div></div>
                <div className="project-badge"><span className="pb-icon"></span><div><strong>Arduino-Powered</strong><p>IR &amp; ultrasonic sensors, servo motor, LCD display — all controlled by Arduino</p></div></div>
                <div className="project-badge project-badge--accent"><span className="pb-icon"></span><div><strong>Rewards: Drinks &amp; Biscuits</strong><p>Each accepted bottle earns the resident a free drink or biscuit as incentive</p></div></div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* RESEARCHERS */}
      <section className="section section--dark researchers-section">
        <div className="container">
          <div className="section-header">
            <div className="section-label section-label--light">The Research Team</div>
            <h2 className="section-title section-title--light">Meet the <em>Researchers</em></h2>
          </div>
          <div className="researchers-grid">
            {researchers.map(name => (
              <div className="researcher-card" key={name}>
                <div className="researcher-icon"></div>
                <div className="researcher-name">{name}</div>
                <div className="researcher-dept">BSIT — OLFU Antipolo</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* HOW IT WORKS SUMMARY */}
      <section className="section how-section">
        <div className="container">
          <div className="section-header">
            <div className="section-label">Process Flow</div>
            <h2 className="section-title">How the Machine <em>Works</em></h2>
            <p className="section-intro">The machine uses sensor-based detection and an Arduino microcontroller to validate, count, and reward residents for every plastic bottle deposited.</p>
          </div>
          <div className="steps-flow">
            {[
              { n: '01', h: 'Insert Bottle', p: 'Resident places a used plastic bottle into the machine\'s input slot.' },
              { n: '02', h: 'IR Sensor Detects', p: 'Infrared sensor detects the bottle\'s presence and triggers the validation sequence.' },
              { n: '03', h: 'Ultrasonic Validates', p: 'HC-SR04 ultrasonic sensor verifies the bottle\'s size and confirms it as a valid PET plastic bottle.' },
              { n: '04', h: 'Arduino Decides', p: 'Arduino processes sensor data and decides: accept or reject. LCD displays the result to the user.' },
              { n: '05', h: 'Reward Dispensed', p: 'Accepted bottles earn the resident a free drink or biscuit dispensed from the machine.' },
            ].map((s, i) => (
              <>
                <div className="step-card" key={s.n}>
                  <div className="step-num">{s.n}</div>
                  <div className="step-icon"></div>
                  <h4>{s.h}</h4>
                  <p>{s.p}</p>
                </div>
                {i < 4 && <div className="step-arrow" key={`arr${i}`}>→</div>}
              </>
            ))}
          </div>
          <div className="steps-cta">
            <Link to="/how-it-works" className="btn btn--primary">Full Technical Breakdown →</Link>
          </div>
        </div>
      </section>

      {/* SIGNIFICANCE */}
      <section className="section section--light">
        <div className="container">
          <div className="section-header">
            <div className="section-label">Significance of the Study</div>
            <h2 className="section-title">Who benefits from <em>this project?</em></h2>
          </div>
          <div className="significance-grid">
            {beneficiaries.map(b => (
              <div className="significance-card" key={b.who}>
                <div className="sig-icon"></div>
                <strong>{b.who}</strong>
                <p>{b.benefit}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* DASHBOARD TEASER */}
      <section className="section section--teal machine-live-section">
        <div className="container">
          <div className="machine-live-grid">
            <div className="machine-live-text">
              <div className="section-label section-label--light">Web Dashboard</div>
              <h2 className="section-title section-title--light">Track every bottle,<br /><em>in real time</em></h2>
              <p>The web dashboard logs each machine transaction — bottles accepted, rewards given, and bin fill level — updated automatically whenever the Arduino sends data via the FastAPI backend.</p>
              <Link to="/data" className="btn btn--primary">Open Dashboard →</Link>
            </div>
            <div className="machine-live-visual">
              <div className="mini-dashboard">
                <div className="mini-dash-title">Machine Stats — Today</div>
                <div className="mini-stat-row"><span className="mini-label">Bottles Accepted</span><span className="mini-val mini-val--green">—</span></div>
                <div className="mini-stat-row"><span className="mini-label">Rewards Dispensed</span><span className="mini-val mini-val--yellow">—</span></div>
                <div className="mini-stat-row"><span className="mini-label">Bin Fill Level</span><span className="mini-val mini-val--blue">—</span></div>
                <div className="mini-stat-row"><span className="mini-label">Arduino Status</span><span className="mini-val mini-val--orange">Pending</span></div>
                <div className="mini-dash-note">Awaiting Arduino connection</div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="cta-section">
        <div className="container">
          <div className="cta-box">
            <div className="cta-icon"></div>
            <h2>One machine.<br /><em>A cleaner Muzon.</em></h2>
            <p>Every plastic bottle collected brings Barangay Muzon one step closer to a cleaner, more sustainable community. Learn more about the project or get in touch.</p>
            <div className="cta-btns">
              <Link to="/about" className="btn btn--primary btn--large">About the Project</Link>
              <Link to="/contact" className="btn btn--ghost btn--large">Contact Us</Link>
            </div>
          </div>
        </div>
      </section>

      <Footer />
    </>
  )
}
