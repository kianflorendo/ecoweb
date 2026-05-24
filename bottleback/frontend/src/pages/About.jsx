
import { useEffect } from 'react'
import { Link } from 'react-router-dom'
import Nav from '../components/Nav'
import Footer from '../components/Footer'

export default function About() {
  useEffect(() => { document.title = 'About the Project | BottleBack' }, [])

  const researchers = [
    'Angeles, Alyza Mae', 'Despabiladeras, Marnes', 'Ellema, Jessica A.',
    'San Marcos, Nick Anjelo', 'Soriano, Lemuel Jaaziah',
  ]
  const objectives = [
    { num: '1', title: 'Assess Feasibility', desc: 'Assess the feasibility of using an Arduino-based vending machine for the collection of plastic bottles in a community setting like Barangay Muzon, Taytay, Rizal.' },
    { num: '2', title: 'Determine Sensor Accuracy', desc: 'Determine the accuracy and reliability of sensor-based detection in identifying and counting plastic bottles inserted into the machine.' },
    { num: '3', title: 'Analyze Reward Effectiveness', desc: 'Analyze the effectiveness of a reward-based mechanism — specifically free drinks and biscuits — in encouraging individuals to participate in recycling activities.' },
    { num: '4', title: 'Evaluate Environmental Awareness', desc: 'Evaluate the level of environmental awareness and participation in recycling among residents before and after the implementation of the vending machine.' },
    { num: '5', title: 'Measure Overall Effectiveness', desc: 'Measure the overall effectiveness of the vending machine in promoting sustainable waste management practices within Barangay Muzon, Taytay, Rizal.' },
  ]
  const sigs = [
    { who: 'Residents', desc: 'The machine encourages recycling by offering free products in exchange for plastic bottles, fostering responsible waste management habits.' },
    { who: 'Barangay Officials', desc: 'Supports local waste management efforts by providing an efficient system for collecting and managing plastic waste within the barangay.' },
    { who: 'Environmental Advocates', desc: 'The project provides a model for other communities to implement similar incentive-based recycling initiatives.' },
    { who: 'Local Businesses', desc: 'Offers businesses an opportunity to contribute to environmental sustainability while engaging directly with the local community.' },
    { who: 'College of Computer Studies — OLFU', desc: 'Strengthens the department\'s academic programs by integrating practical, technology-driven solutions and community-based innovation.' },
    { who: 'Researchers & Future Researchers', desc: 'Serves as a useful reference for studies related to recycling, environmental awareness, and the use of Arduino technology in community settings.' },
  ]
  const terms = [
    { term: 'Arduino', def: 'A small microcontroller board that controls the vending machine\'s sensors, counting system, and reward dispenser automatically.' },
    { term: 'Plastic Bottle Vending Machine', def: 'An automated device that accepts used plastic bottles from residents and gives small rewards such as drinks or biscuits in return.' },
    { term: 'Sensor', def: 'An electronic component that detects when a plastic bottle is inserted into the machine and helps count it accurately for proper reward issuance.' },
    { term: 'Reward-based Mechanism', def: 'A system that motivates residents to recycle by offering free items or incentives in exchange for depositing plastic bottles.' },
    { term: 'Reverse Vending Machine (RVM)', def: 'A special type of vending machine where people deposit empty containers, and the machine provides a reward or incentive in return.' },
    { term: 'Environmental Awareness', def: 'Understanding why it is important to keep the community clean, reduce plastic pollution, and properly dispose of waste materials.' },
    { term: 'IR Sensor (Infrared Sensor)', def: 'A sensor that detects the presence of a plastic bottle as it enters the machine, helping verify that a bottle was deposited.' },
    { term: 'Ultrasonic Sensor', def: 'A sensor that measures distance and is used to detect when the machine\'s storage bin is already full, and to validate bottle size.' },
    { term: 'Incentive', def: 'A reward given to encourage residents to participate in recycling activities and develop environmentally responsible habits.' },
    { term: 'Barangay Muzon', def: 'The specific community in Taytay, Rizal where the study was conducted and the vending machine was deployed and tested.' },
  ]

  return (
    <>
      <Nav />
      <section className="page-hero">
        <div className="container">
          <div className="section-label section-label--light">BSIT Capstone — June 2027</div>
          <h1 className="page-hero__title">About <em>the Project</em></h1>
          <p className="page-hero__sub">An Arduino-Based Plastic Bottle Vending Machine to Support Environmental Awareness for Barangay Muzon, Taytay Rizal</p>
          <p className="page-hero__meta">Our Lady of Fatima University &nbsp;·&nbsp; College of Computer Studies &nbsp;·&nbsp; Antipolo City</p>
        </div>
      </section>

      {/* RESEARCHERS */}
      <section className="section section--dark">
        <div className="container">
          <div className="section-header">
            <div className="section-label section-label--light">The Research Team</div>
            <h2 className="section-title section-title--light">Presented <em>by</em></h2>
          </div>
          <div className="researchers-grid">
            {researchers.map(name => (
              <div className="researcher-card" key={name}>
                <div className="researcher-icon"></div>
                <div className="researcher-name">{name}</div>
                <div className="researcher-dept">BSIT — OLFU Antipolo City</div>
              </div>
            ))}
          </div>
          <p className="researchers-note">In partial fulfillment of the requirements for the degree <strong>Bachelor of Science in Information Technology</strong></p>
        </div>
      </section>

      {/* BACKGROUND */}
      <section className="section">
        <div className="container">
          <div className="about-grid" style={{ gap: '4rem' }}>
            <div className="about-text">
              <h2 className="section-title">Background of <em>the Study</em></h2>
              <p>Barangay Muzon, Taytay, Rizal is a lively and growing community where residents go about their daily routines, often using plastic bottles and other modern conveniences. As the community continues to develop, there is a growing interest in finding creative ways to engage residents in activities that benefit both them and the environment.</p>
              <p>Plastic bottles are often improperly disposed of, contributing to pollution and clogged drainage systems throughout the barangay. A major challenge in the community is the <strong>lack of convenience and motivation</strong> for residents to recycle. Many residents are willing to help keep the barangay clean but often find it difficult to store and bring recyclables to proper disposal points.</p>
              <p>Technology has opened up new possibilities to make recycling more fun, interactive, and rewarding. Incentive-based and interactive projects not only capture people's interest but also help build habits that can last a lifetime.</p>
            </div>
            <div className="about-visual">
              <div className="info-blocks">
                <div className="info-block info-block--green">
                  <h4>General Objective</h4>
                  <p>To examine the effectiveness of an Arduino-based plastic bottle vending machine as a tool for promoting recycling behavior among residents of Barangay Muzon, Taytay, Rizal.</p>
                </div>
                <div className="info-block info-block--blue">
                  <h4>Study Location</h4>
                  <p>Barangay Muzon, Taytay, Rizal — a growing residential community where plastic bottle mismanagement contributes to local pollution and drainage problems.</p>
                </div>
                <div className="info-block info-block--earth">
                  <h4>Reward Mechanism</h4>
                  <p>Residents receive <strong>free drinks or biscuits</strong> in exchange for each valid plastic bottle deposited — a practical, tangible incentive proven to encourage participation.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* OBJECTIVES */}
      <section className="section section--light">
        <div className="container">
          <div className="section-header">
            <h2 className="section-title">Specific <em>Objectives of the Study</em></h2>
          </div>
          <div className="objectives-list">
            {objectives.map(o => (
              <div className="objective-item" key={o.num}>
                <div className="objective-num">{o.num}</div>
                <div className="objective-body">
                  <h4>{o.title}</h4>
                  <p>{o.desc}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* SCOPE & LIMITATIONS */}
      <section className="section section--dark">
        <div className="container">
          <div className="section-header">
            <h2 className="section-title section-title--light">What this study <em>covers</em></h2>
          </div>
          <div className="scope-grid">
            <div className="scope-col">
              <h3 className="scope-head scope-head--green">Scope of the Study</h3>
              <ul className="scope-list">
                <li>Examines the use of an Arduino-based vending machine as a tool for encouraging recycling through rewards</li>
                <li>Evaluates how accurately sensors detect and count plastic bottles, affecting reward reliability</li>
                <li>Assesses whether incentives (free drinks or biscuits) motivate residents to participate in recycling</li>
                <li>Collects data within a specific community environment to determine participation and user engagement</li>
                <li>Explores residents' perceptions of recycling and their willingness to engage with technology-based solutions</li>
              </ul>
            </div>
            <div className="scope-col">
              <h3 className="scope-head scope-head--red">Limitations of the Study</h3>
              <ul className="scope-list scope-list--limit">
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

      {/* SIGNIFICANCE */}
      <section className="section">
        <div className="container">
          <div className="section-header">
            <h2 className="section-title">Who benefits from <em>this project</em></h2>
          </div>
          <div className="significance-grid">
            {sigs.map(s => (
              <div className="significance-card" key={s.who}>
                <div className="sig-icon"></div>
                <strong>{s.who}</strong>
                <p>{s.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* TERMS */}
      <section className="section section--light">
        <div className="container">
          <div className="section-header">
            <h2 className="section-title">Key <em>Terms</em></h2>
          </div>
          <div className="terms-grid">
            {terms.map(t => (
              <div className="term-card" key={t.term}>
                <strong>{t.term}</strong>
                <p>{t.def}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      <Footer />
    </>
  )
}
