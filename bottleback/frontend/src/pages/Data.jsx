import { useEffect, useState, useCallback } from 'react'
import Nav from '../components/Nav'
import Footer from '../components/Footer'

function binColor(l) {
  if (l === null || l === undefined) return 'pending'
  if (l >= 90) return 'danger'
  if (l >= 70) return 'warn'
  return 'ok'
}
function binLabel(l) {
  if (l === null || l === undefined) return '—'
  if (l >= 90) return 'FULL'
  if (l >= 70) return 'HIGH'
  return `${l}%`
}

export default function Data() {
  const [stats, setStats] = useState(null)
  const [transactions, setTransactions] = useState([])
  const [connected, setConnected] = useState(false)
  const [lastRefresh, setLastRefresh] = useState(null)

  useEffect(() => { document.title = 'Live Dashboard | BottleBack' }, [])

  const fetchData = useCallback(async () => {
    try {
      const [sRes, tRes] = await Promise.all([
        fetch('/api/transactions/stats'),
        fetch('/api/transactions/recent?limit=15'),
      ])
      if (sRes.ok && tRes.ok) {
        setStats(await sRes.json())
        setTransactions(await tRes.json())
        setConnected(true)
        setLastRefresh(new Date())
      }
    } catch {
      setConnected(false)
    }
  }, [])

  useEffect(() => {
    fetchData()
    const id = setInterval(fetchData, 5000)
    return () => clearInterval(id)
  }, [fetchData])

  const binLevel = stats?.bin_level
  const bc = binColor(binLevel)

  return (
    <>
      <Nav />
      <section className="page-hero">
        <div className="container">
          <div className="section-label section-label--light">Real-Time Monitoring</div>
          <h1 className="page-hero__title">Machine <em>Dashboard</em></h1>
          <p className="page-hero__sub">
            {connected
              ? `Live — Barangay Muzon Node · Last refresh: ${lastRefresh?.toLocaleString(undefined, { timeZone: 'Asia/Manila' })}`
              : '🔌 Database not yet connected — dashboard is ready and waiting for Arduino data.'}
          </p>
        </div>
      </section>

      <section className="section section--dark dashboard-section">
        <div className="container">
          {!connected && (
            <div className="alert-banner alert-banner--info">
              <span>ℹ</span>
              <div>
                <strong>Setup Required</strong>
                Start the FastAPI backend → run seed.py → connect your Arduino.
                All dashboard cards will populate automatically — no code changes needed.
              </div>
            </div>
          )}

          {/* KPI CARDS */}
          <div className="kpi-grid">
            <div className="kpi-card kpi-card--green">
              <div className="kpi-icon"></div>
              <div className="kpi-body">
                <div className="kpi-label">Bottles Accepted Today</div>
                <div className="kpi-value">{connected ? stats?.today_bottles ?? 0 : '—'}</div>
              </div>
            </div>
            <div className="kpi-card kpi-card--yellow">
              <div className="kpi-icon"></div>
              <div className="kpi-body">
                <div className="kpi-label">Rewards Dispensed Today</div>
                <div className="kpi-value">{connected ? stats?.today_rewards ?? 0 : '—'}</div>
              </div>
            </div>
            <div className="kpi-card kpi-card--blue">
              <div className="kpi-icon"></div>
              <div className="kpi-body">
                <div className="kpi-label">Total Bottles (All-Time)</div>
                <div className="kpi-value">{connected ? (stats?.total_bottles ?? 0).toLocaleString() : '—'}</div>
              </div>
            </div>
            <div className={`kpi-card kpi-card--${bc}`}>
              <div className="kpi-icon"></div>
              <div className="kpi-body">
                <div className="kpi-label">Bin Fill Level</div>
                <div className="kpi-value">{binLabel(binLevel)}</div>
              </div>
            </div>
          </div>

          {/* BIN VISUAL */}
          {connected && binLevel !== null && binLevel !== undefined && (
            <div className="bin-visual-wrap">
              <div className="bin-visual">
                <div className="bin-fill" style={{ height: `${binLevel}%` }}></div>
                <div className="bin-label-inside">{binLevel}%</div>
              </div>
              <div className="bin-caption">
                <strong>Collection Bin — Barangay Muzon Node</strong>
                <p>{binLevel >= 90 ? '⚠️ Bin full — please empty before accepting more bottles.' : binLevel >= 70 ? '⚠️ Bin filling up. Plan to empty soon.' : '✅ Bin level is acceptable.'}</p>
              </div>
            </div>
          )}

          {/* TRANSACTIONS TABLE */}
          <div className="data-history">
            <div className="data-history-header">
              <h3>Recent Transactions</h3>
              {connected && transactions.length > 0 && (
                <span className="live-badge"><span className="pulse-dot pulse-dot--sm"></span> Live</span>
              )}
            </div>

            {connected && transactions.length > 0 ? (
              <div className="table-wrap">
                <table className="data-table">
                  <thead>
                    <tr><th>#</th><th>Date &amp; Time</th><th>Bottles</th><th>Reward</th><th>Status</th></tr>
                  </thead>
                  <tbody>
                    {transactions.map(tx => (
                      <tr key={tx.id}>
                        <td>{tx.id}</td>
                        <td>{new Date(tx.created_at).toLocaleString('en-PH', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', timeZone: 'Asia/Manila' })}</td>
                        <td>{tx.bottle_count}</td>
                        <td>{tx.reward_amount}</td>
                        <td><span className={`status-badge status-badge--${tx.status.toLowerCase()}`}>{tx.status}</span></td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            ) : (
              <div className="empty-state">
                <div className="empty-state__icon"></div>
                <h4>No transactions yet</h4>
                <p>Bottle transactions will appear here once the Arduino is connected and the backend is running.</p>
                <div className="db-setup">
                  <h5>Quick Setup Steps</h5>
                  <ol>
                    <li>Navigate to <strong>bottleback/backend/</strong></li>
                    <li>Run <code>pip install -r requirements.txt</code></li>
                    <li>Run <code>uvicorn main:app --reload</code></li>
                    <li>Connect Arduino and run the Python serial bridge script</li>
                    <li>Refresh this page — all data will appear automatically</li>
                  </ol>
                </div>
              </div>
            )}
          </div>
        </div>
      </section>
      <Footer />
    </>
  )
}
