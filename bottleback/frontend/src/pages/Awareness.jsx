import { useEffect } from 'react'
import Nav from '../components/Nav'
import Footer from '../components/Footer'

export default function Awareness() {
  useEffect(() => { document.title = 'Plastic Awareness | BottleBack' }, [])

  const facts = [
    { val: '300M', unit: 'tons', desc: 'of plastic produced globally every year', color: 'red' },
    { val: '9%', unit: 'only', desc: 'of all plastic ever made has been recycled', color: 'orange' },
    { val: '450', unit: 'years', desc: 'for a PET bottle to fully decompose in landfill', color: 'earth' },
    { val: '8M+', unit: 'tons', desc: 'of plastic waste enter the world\'s oceans each year', color: 'blue' },
    { val: '1M', unit: 'bottles', desc: 'plastic bottles bought around the world every minute', color: 'teal' },
    { val: '91%', unit: 'never', desc: 'of plastic never recycled — goes to landfill or the environment', color: 'purple' },
  ]
  const plastics = [
    { code: '1', name: 'PET / PETE', examples: 'Water bottles, soda bottles, juice bottles', accepted: true },
    { code: '2', name: 'HDPE', examples: 'Milk jugs, shampoo bottles, detergent containers', accepted: false },
    { code: '3', name: 'PVC', examples: 'Pipes, some food wrap', accepted: false },
    { code: '4', name: 'LDPE', examples: 'Plastic bags, squeezable bottles', accepted: false },
    { code: '5', name: 'PP', examples: 'Yogurt containers, medicine bottles', accepted: false },
    { code: '6', name: 'PS', examples: 'Styrofoam cups, disposable plates', accepted: false },
  ]

  return (
    <>
      <Nav />
      <section className="page-hero">
        <div className="container">
          <div className="section-label section-label--light">Environmental Education</div>
          <h1 className="page-hero__title">Plastic <em>Awareness</em></h1>
          <p className="page-hero__sub">Understanding the plastic problem in Barangay Muzon and the Philippines is the first step toward solving it.</p>
        </div>
      </section>

      {/* LOCAL PROBLEM */}
      <section className="section">
        <div className="container">
          <div className="about-grid" style={{ gap: '4rem' }}>
            <div className="about-text">
              <div className="section-label">The Local Context</div>
              <h2 className="section-title">The plastic problem in<br /><em>Barangay Muzon</em></h2>
              <p>Barangay Muzon, Taytay, Rizal is a growing community where residents frequently use plastic bottles for drinking water and other beverages. These bottles are often improperly disposed of, contributing to <strong>pollution and clogged drainage systems</strong> throughout the barangay.</p>
              <p>In the Philippines, around <strong>35,580 tons of garbage</strong> are produced every day, with each person generating about half a kilogram of waste daily. A significant portion of this is plastic bottle waste — material that can be recycled but often isn't, simply due to a lack of motivation and accessible collection points.</p>
              <p>The DENR in CALABARZON has urged the public to support the <em>"Beat Plastic Pollution"</em> campaign — and this machine is Barangay Muzon's direct response to that call.</p>
            </div>
            <div className="about-visual">
              <div className="fact-highlight-stack">
                <div className="fact-highlight fact-highlight--red">
                  <div className="fh-num">35,580</div>
                  <div className="fh-label">Tons of garbage produced in the Philippines daily</div>
                </div>
                <div className="fact-highlight fact-highlight--orange">
                  <div className="fh-num">450 yrs</div>
                  <div className="fh-label">Time for one PET bottle to decompose in a landfill</div>
                </div>
                <div className="fact-highlight fact-highlight--green">
                  <div className="fh-num">153</div>
                  <div className="fh-label">Active MRFs in Rizal barangays as of 2024</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* GLOBAL FACTS */}
      <section className="section section--dark">
        <div className="container">
          <div className="section-header">
            <div className="section-label section-label--light">Global Statistics</div>
            <h2 className="section-title section-title--light">Plastic by the <em>numbers</em></h2>
          </div>
          <div className="fact-cards-grid">
            {facts.map(f => (
              <div key={f.val} className={`fact-card fact-card--${f.color}`}>
                <div className="fact-val">{f.val}</div>
                <div className="fact-unit">{f.unit}</div>
                <p>{f.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* LIFECYCLE COMPARISON */}
      <section className="section section--dark" style={{ paddingTop: 0 }}>
        <div className="container">
          <div className="section-header">
            <div className="section-label section-label--light">The Difference We Make</div>
            <h2 className="section-title section-title--light">A bottle's journey —<br /><em>with and without BottleBack</em></h2>
          </div>
          <div className="lifecycle-grid">
            <div className="lifecycle-path lifecycle-path--bad">
              <div className="lifecycle-path__label lifecycle-path__label--bad">Without Recycling</div>
              <div className="lifecycle-steps">
                <div className="lc-step"><span>Purchased &amp; consumed</span></div>
                <div className="lc-arrow">↓</div>
                <div className="lc-step bad"><span>Thrown in street or open trash</span></div>
                <div className="lc-arrow">↓</div>
                <div className="lc-step bad"><span>Clogs drainage in Barangay Muzon</span></div>
                <div className="lc-arrow">↓</div>
                <div className="lc-step bad"><span>Ends up in landfill for 450 years</span></div>
                <div className="lc-arrow">↓</div>
                <div className="lc-step bad"><span>Leaches into waterways &amp; pollutes ecosystems</span></div>
              </div>
            </div>
            <div className="lifecycle-vs">VS</div>
            <div className="lifecycle-path lifecycle-path--good">
              <div className="lifecycle-path__label lifecycle-path__label--good">With BottleBack</div>
              <div className="lifecycle-steps">
                <div className="lc-step"><span>Purchased &amp; consumed</span></div>
                <div className="lc-arrow">↓</div>
                <div className="lc-step good"><span>Inserted into BottleBack machine</span></div>
                <div className="lc-arrow">↓</div>
                <div className="lc-step good"><span>Resident receives free drink or biscuit</span></div>
                <div className="lc-arrow">↓</div>
                <div className="lc-step good"><span>Bottles collected &amp; sent to recycling facility</span></div>
                <div className="lc-arrow">↓</div>
                <div className="lc-step good"><span>Plastic reprocessed into new materials</span></div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* WHY REWARDS WORK */}
      <section className="section">
        <div className="container">
          <div className="section-header">
            <div className="section-label">Why It Works</div>
            <h2 className="section-title">The science behind<br /><em>reward-based recycling</em></h2>
          </div>
          <div className="research-grid">
            <div className="research-card research-card--light">
              <div className="research-icon"></div>
              <h4>Behavioral Psychology</h4>
              <p>Positive reinforcement — receiving a reward immediately after an action — is one of the most effective methods of building lasting habits. Applied to recycling, it transforms an obligation into a desired behavior.</p>
            </div>
            <div className="research-card research-card--light">
              <div className="research-icon"></div>
              <h4>Proven Results Globally</h4>
              <p>Countries with deposit-refund systems (Germany, Norway) achieve PET bottle recycling rates of over 90%, compared to the global average of under 30%. Rewards make the difference.</p>
            </div>
            <div className="research-card research-card--light">
              <div className="research-icon"></div>
              <h4>Local Community Context</h4>
              <p>In Barangay Muzon, practical rewards like free drinks and biscuits are immediately useful to residents — making the incentive highly relevant and motivating for daily participation.</p>
            </div>
          </div>
        </div>
      </section>

      {/* TIPS */}
      <section className="section section--light">
        <div className="container">
          <div className="section-header">
            <div className="section-label">Take Action</div>
            <h2 className="section-title">What <em>you can do</em> as a resident</h2>
          </div>
          <div className="tips-grid">
            {[
              { n: '01', h: 'Use the BottleBack Machine', p: 'The simplest action — bring your used plastic bottles to the machine, deposit them, and earn your reward. Make it a daily habit.' },
              { n: '02', h: 'Segregate Waste at Home', p: 'Separate plastic bottles from biodegradable and other waste. Clean, dry bottles are more easily accepted by the machine and recycling facilities.' },
              { n: '03', h: 'Spread Awareness', p: 'Tell your neighbors, friends, and family about the BottleBack machine and the plastic pollution problem in Barangay Muzon. Awareness multiplies impact.' },
              { n: '04', h: 'Use a Reusable Bottle', p: 'The best plastic bottle is one you never buy. A reusable water bottle saves money, reduces waste, and helps keep the barangay clean.' },
              { n: '05', h: 'Join Barangay Clean-Up Drives', p: 'Participate in community clean-up activities. Collected plastic bottles can be deposited into BottleBack machines for proper processing.' },
              { n: '06', h: 'Support RA 11898', p: 'Know your rights and the law. The Extended Producer Responsibility Act of 2022 supports recycling initiatives like this one — be part of the solution.' },
            ].map(t => (
              <div className="tip-card" key={t.n}>
                <span className="tip-num">{t.n}</span>
                <h4>{t.h}</h4>
                <p>{t.p}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* PLASTIC TYPES */}
      <section className="section">
        <div className="container">
          <div className="section-header">
            <div className="section-label">What the Machine Accepts</div>
            <h2 className="section-title">Know your <em>plastic types</em></h2>
            <p className="section-intro">BottleBack is designed to accept <strong>PET (Type 1)</strong> plastic bottles — the most common water and beverage bottle type in Barangay Muzon and across the Philippines.</p>
          </div>
          <div className="plastic-types-grid">
            {plastics.map(p => (
              <div key={p.code} className={`plastic-card plastic-card--${p.accepted ? 'accepted' : 'not-accepted'}`}>
                <div className="plastic-code">{p.code}</div>
                <div className="plastic-body">
                  <strong>{p.name}</strong>
                  <p>{p.examples}</p>
                  <span className={`plastic-badge plastic-badge--${p.accepted ? 'yes' : 'no'}`}>
                    {p.accepted ? 'Accepted by BottleBack' : 'Not accepted'}
                  </span>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      <Footer />
    </>
  )
}
