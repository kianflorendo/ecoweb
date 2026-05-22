import { useEffect } from 'react'
import Nav from '../components/Nav'
import Footer from '../components/Footer'

export default function HowItWorks() {
  useEffect(() => { document.title = 'How It Works | BottleBack' }, [])

  const steps = [
    { num: 'Step 1', title: 'Resident Inserts Plastic Bottle', desc: 'The resident places a used PET plastic bottle into the front input slot of the machine. The slot is sized to accept standard plastic bottles commonly found in Barangay Muzon.', tech: 'Physical enclosure with designed input slot' },
    { num: 'Step 2', title: 'IR Sensor Detects Bottle', desc: 'An infrared (IR) sensor positioned inside the input slot detects the presence of an object and sends a signal to the Arduino to begin the validation process.', tech: 'FC-51 Infrared Obstacle Sensor Module' },
    { num: 'Step 3', title: 'Ultrasonic Sensor Validates Size', desc: 'An HC-SR04 ultrasonic sensor measures the distance to the bottle. The Arduino checks whether the reading falls within the acceptable range for a valid plastic bottle.', tech: 'HC-SR04 Ultrasonic Distance Sensor' },
    { num: 'Step 4', title: 'Arduino Processes & Decides', desc: 'Based on sensor readings, the Arduino decides: Accept or Reject. If valid, the servo motor opens the gate. If invalid, the LCD shows an error and the bottle is returned.', tech: 'Arduino Uno / Mega microcontroller (C++ logic)' },
    { num: 'Step 5', title: 'Servo Motor Opens Gate', desc: 'A servo motor rotates to open the acceptance gate, allowing the bottle to fall into the collection bin. It then closes automatically to await the next bottle.', tech: 'SG90 / MG996R Servo Motor' },
    { num: 'Step 6', title: 'LCD Shows Feedback', desc: 'The 16×2 LCD display shows the result — "Bottle Accepted!" or "Invalid Item" — giving the resident clear, immediate visual feedback on the transaction.', tech: '16×2 I2C LCD Display Module' },
    { num: 'Step 7', title: 'Reward Dispensed', desc: 'Upon acceptance, the machine dispenses a reward — a free drink or biscuit — as a tangible incentive. This reward-based mechanism is the core behavior-change driver of the system.', tech: 'Reward dispenser mechanism (drinks / biscuits)' },
    { num: 'Step 8', title: 'Data Sent to Web Dashboard', desc: 'The Arduino sends transaction data via serial communication to the connected PC. A Python bridge script forwards the data to the FastAPI backend, which stores it in SQLite for real-time monitoring.', tech: 'Serial → Python bridge → FastAPI → SQLite' },
  ]
  const bom = [
    ['Arduino Microcontroller', 'Arduino Uno R3 / Mega 2560', '1', 'Main controller — processes sensor inputs and controls all outputs'],
    ['IR Sensor Module', 'FC-51 Infrared Obstacle Sensor', '1–2', 'Detects presence of plastic bottle in the input slot'],
    ['Ultrasonic Sensor', 'HC-SR04', '1', 'Measures distance to validate bottle size; monitors bin fill level'],
    ['Servo Motor', 'SG90 or MG996R', '1', 'Controls the acceptance gate — opens on valid bottle, closes after'],
    ['LCD Display', '16×2 with I2C adapter', '1', 'Shows feedback to the resident — "Accepted" or "Invalid"'],
    ['Buzzer', '5V Active Buzzer', '1', 'Audio feedback for accepted / rejected bottle events'],
    ['LED Indicators', 'Green / Red 5mm LEDs', '2–4', 'Visual accept/reject signal for the user'],
    ['Power Supply', '5V 2A DC Adapter', '1', 'Powers Arduino and all connected peripherals'],
    ['Jumper Wires', 'Male-to-male / Male-to-female', '30+', 'Circuit connections between components'],
    ['Breadboard / PCB', 'Full-size breadboard', '1', 'Prototyping and circuit assembly'],
    ['Machine Enclosure', 'Wood / PVC / Acrylic box', '1', 'Physical housing for the entire system'],
    ['Reward Dispenser', 'Mechanical dispenser tray', '1', 'Holds and releases drinks or biscuits as rewards'],
  ]
  const studies = [
    { title: 'Design of a Plastic Bottle Recycling Machine', authors: 'Arslan & Tahan (2021)', note: 'Shows how reverse vending machines make recycling easier and more accessible by offering rewards, supporting this project\'s core approach.' },
    { title: 'Arduino-Based Waste ATM for Recycling Awareness', authors: 'Jamas, Irwanto & Permata (2024)', note: 'Demonstrates automated plastic bottle collection using Arduino and capacitive/ultrasonic sensors — directly paralleling this project\'s hardware design.' },
    { title: 'Smart Reverse Vending Machine for Plastic Bottles', authors: 'Rao et al. (2023)', note: 'Uses Arduino Uno, IR sensors, and load cell to detect and validate bottles. Users receive instant coin rewards — similar reward-based model.' },
    { title: 'VENDOBIN — IoT-Based Plastic Bottle Disposal Machine', authors: 'Dacay et al., USTP (2023)', note: 'Local Philippine study combining vending machine and garbage bin concepts — alerting authorities when full, tracking user activity. Closest local parallel.' },
    { title: 'Arduino Ballpoint Pen Vending Machine — Plastic Bottle Exchange', authors: 'Boto et al. (2023)', note: 'Philippine study showing high user satisfaction when Arduino dispenses school supplies in exchange for empty plastic bottles — confirms reward-based acceptance.' },
    { title: 'Republic Act No. 11898 — Extended Producer Responsibility Act', authors: 'Philippine Government (2022)', note: 'Provides legal foundation for the project. The proposed machine directly supports RA 11898\'s goals of plastic waste recovery and community recycling.' },
  ]

  return (
    <>
      <Nav />
      <section className="page-hero">
        <div className="container">
          <div className="section-label section-label--light">Technical Overview</div>
          <h1 className="page-hero__title">How It <em>Works</em></h1>
          <p className="page-hero__sub">A complete breakdown of the machine's hardware, software, and process flow — from bottle insertion to reward dispensing.</p>
        </div>
      </section>

      {/* PROCESS FLOW */}
      <section className="section" id="flow">
        <div className="container">
          <div className="section-header">
            <div className="section-label">Step by Step</div>
            <h2 className="section-title">Complete <em>Process Flow</em></h2>
          </div>
          <div className="flow-detail-list">
            {steps.map((s, i) => (
              <div key={s.num} className={`flow-detail-item${i % 2 === 1 ? ' flow-detail-item--alt' : ''}`}>
                <div className="flow-detail-icon"></div>
                <div className="flow-detail-body">
                  <div className="flow-step-label">{s.num}</div>
                  <h3>{s.title}</h3>
                  <p>{s.desc}</p>
                  <div className="flow-tech-tag">{s.tech}</div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* HARDWARE BOM */}
      <section className="section section--dark" id="hardware">
        <div className="container">
          <div className="section-header">
            <div className="section-label section-label--light">Bill of Materials</div>
            <h2 className="section-title section-title--light">Hardware <em>Components</em></h2>
          </div>
          <div className="table-wrap">
            <table className="data-table">
              <thead>
                <tr><th>Component</th><th>Model / Specification</th><th>Qty</th><th>Purpose</th></tr>
              </thead>
              <tbody>
                {bom.map((row, i) => (
                  <tr key={i}>{row.map((cell, j) => <td key={j}>{cell}</td>)}</tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </section>

      {/* SOFTWARE STACK */}
      <section className="section" id="software">
        <div className="container">
          <div className="section-header">
            <div className="section-label">Software Architecture</div>
            <h2 className="section-title">The <em>Software Stack</em></h2>
          </div>
          <div className="software-stack">
            <div className="sw-layer sw-layer--arduino">
              <div className="sw-layer-label">Layer 1 — Arduino Firmware (C++)</div>
              <div className="sw-layer-items">
                <span>IR sensor reading loop</span><span>Ultrasonic distance measurement</span>
                <span>Bottle validation logic (if/else)</span><span>Servo motor control (open / close gate)</span>
                <span>LCD display output ("Accepted" / "Invalid")</span><span>Buzzer & LED feedback</span>
                <span>Serial.println() data output to PC</span>
              </div>
            </div>
            <div className="sw-arrow">↓ &nbsp; Serial COM Port (USB) → Python Bridge &nbsp; ↓</div>
            <div className="sw-layer sw-layer--php">
              <div className="sw-layer-label">Layer 2 — FastAPI Web Application (Python)</div>
              <div className="sw-layer-items">
                <span>POST /api/receive-data — receives data from serial bridge</span>
                <span>GET /api/transactions/stats — live dashboard stats</span>
                <span>GET /api/transactions/recent — recent transactions</span>
                <span>POST /api/contact — contact form submissions</span>
                <span>JWT authentication for users and admin</span>
                <span>React frontend served via Vite dev server</span>
              </div>
            </div>
            <div className="sw-arrow">↓ &nbsp; SQLAlchemy ORM &nbsp; ↓</div>
            <div className="sw-layer sw-layer--db">
              <div className="sw-layer-label">Layer 3 — SQLite Database (bottleback.db)</div>
              <div className="sw-layer-items">
                <span>transactions — one row per bottle event (accepted / rejected)</span>
                <span>machine_status — live bin level & online status</span>
                <span>contact_messages — contact form submissions</span>
                <span>users — resident accounts with bottle/reward totals</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* RELATED STUDIES */}
      <section className="section section--light">
        <div className="container">
          <div className="section-header">
            <h2 className="section-title">Studies that support <em>this project</em></h2>
          </div>
          <div className="research-grid">
            {studies.map(s => (
              <div className="research-card research-card--light" key={s.title}>
                <div className="research-icon"></div>
                <h4>{s.title}</h4>
                <div className="research-authors">{s.authors}</div>
                <p>{s.note}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      <Footer />
    </>
  )
}
