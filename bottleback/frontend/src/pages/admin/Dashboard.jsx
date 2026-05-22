import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import AdminLayout from '../../components/AdminLayout'
import { useAdminAuth } from '../../hooks/useAuth'

export default function Dashboard() {
  const { token } = useAdminAuth()
  const [data, setData] = useState(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    document.title = 'Dashboard — BottleBack Admin'
    if (!token) return
    fetch('/api/admin/stats', { headers: { Authorization: `Bearer ${token}` } })
      .then(r => r.ok ? r.json() : null)
      .then(d => { setData(d); setLoading(false) })
      .catch(() => setLoading(false))
  }, [token])

  const now = new Date().toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Manila' })
  const connected = !!data

  function binColor(l) { if (l >= 90) return 'danger'; if (l >= 70) return 'warn'; return 'ok' }

  return (
    <AdminLayout>
      <div className="page-header">
        <div className="page-header__text">
          <div className="label">Overview</div>
          <h1>Admin <em>Dashboard</em></h1>
        </div>
        <div style={{ display: 'flex', gap: '.8rem', alignItems: 'center', flexWrap: 'wrap' }}>
          <span style={{ fontSize: '.82rem', color: 'var(--muted)' }}>{now}</span>
          <span className={`badge badge--${connected ? 'green' : 'red'}`}>● DB {connected ? 'Connected' : 'Offline'}</span>
        </div>
      </div>

      {!connected && !loading && (
        <div className="alert alert--warn">
          <span>⚠️</span>
          <div><strong>Database not connected.</strong><p>Start the FastAPI backend and make sure bottleback.db is initialized. Stats will appear automatically.</p></div>
        </div>
      )}

      {/* KPI CARDS */}
      <div className="kpi-grid">
        {[
          { label: 'Bottles Today', val: data?.today_bottles, icon: '♻️', color: 'green' },
          { label: 'Rewards Today', val: data?.today_rewards, icon: '🎁', color: 'yellow' },
          { label: 'Total Accepted', val: data?.total_bottles?.toLocaleString(), icon: '✅', color: 'blue' },
          { label: 'Bin Fill Level', val: data ? `${data.bin_level}%` : '—', icon: '🗑️', color: data ? (binColor(data.bin_level) === 'ok' ? 'teal' : binColor(data.bin_level)) : 'teal' },
          { label: 'Total Rewards', val: data?.total_rewards?.toLocaleString(), icon: '🏆', color: '' },
          { label: 'Total Rejected', val: data?.rejected?.toLocaleString(), icon: '❌', color: 'red' },
        ].map(k => (
          <div key={k.label} className={`kpi-card${k.color ? ` kpi-card--${k.color}` : ''}`}>
            <span className="kpi-icon">{k.icon}</span>
            <div className="kpi-body">
              <div className="kpi-label">{k.label}</div>
              <div className="kpi-value">{connected ? (k.val ?? '0') : '—'}</div>
            </div>
          </div>
        ))}
      </div>

      {/* CHARTS + BIN */}
      <div className="grid-2" style={{ marginBottom: '1.6rem' }}>
        {/* 7-DAY CHART */}
        <div className="panel">
          <div className="panel-header"><span className="panel-title">📊 Bottles Accepted — Last 7 Days</span></div>
          <div className="panel-body">
            {connected && data?.daily_data && Object.values(data.daily_data).some(v => v > 0) ? (
              <BarChart data={data.daily_data} />
            ) : (
              <div className="empty-state" style={{ padding: '2rem' }}>
                <div className="empty-state__icon">📊</div>
                <p>No data yet — connect Arduino to begin.</p>
              </div>
            )}
          </div>
        </div>

        {/* BIN STATUS */}
        <div className="panel">
          <div className="panel-header">
            <span className="panel-title">🗑️ Bin Fill Status</span>
            <Link to="/admin/machine" className="btn btn--ghost btn--sm">View Machine →</Link>
          </div>
          <div className="panel-body">
            {connected ? (
              <div className="bin-wrap">
                <div>
                  <div className="bin-visual">
                    <div className={`bin-fill${data.bin_level >= 90 ? ' bin-fill--danger' : data.bin_level >= 70 ? ' bin-fill--warn' : ''}`} style={{ height: `${data.bin_level}%` }}></div>
                    <div className="bin-label">{data.bin_level}%</div>
                  </div>
                </div>
                <div>
                  <h4 style={{ marginBottom: '.4rem' }}>node_001 — Barangay Muzon</h4>
                  <div className="progress-bar" style={{ width: '200px' }}>
                    <div className={`progress-fill${data.bin_level >= 90 ? ' progress-fill--danger' : data.bin_level >= 70 ? ' progress-fill--warn' : ''}`} style={{ width: `${data.bin_level}%` }}></div>
                  </div>
                  <p style={{ fontSize: '.85rem', marginTop: '.4rem' }}>
                    {data.bin_level >= 90 ? '🔴 <strong>Bin full</strong> — empty immediately.' : data.bin_level >= 70 ? '🟡 <strong>Bin filling</strong> — plan to empty soon.' : '🟢 Bin level is acceptable.'}
                  </p>
                  <p style={{ fontSize: '.78rem', marginTop: '.6rem', color: 'var(--muted)' }}>Accepted: {data.total_bottles?.toLocaleString()} bottles · Rejected: {data.rejected?.toLocaleString()}</p>
                </div>
              </div>
            ) : (
              <div className="empty-state" style={{ padding: '2rem' }}><div className="empty-state__icon">🔌</div><p>Awaiting database connection.</p></div>
            )}
          </div>
        </div>
      </div>

      {/* RECENT TRANSACTIONS */}
      <div className="panel">
        <div className="panel-header">
          <span className="panel-title">🔄 Recent Transactions</span>
          <Link to="/admin/transactions" className="btn btn--outline btn--sm">View All →</Link>
        </div>
        {connected && data?.recent_transactions?.length ? (
          <div className="table-wrap">
            <table>
              <thead><tr><th>#</th><th>Date &amp; Time</th><th>Bottles</th><th>Reward</th><th>Node</th><th>Status</th></tr></thead>
              <tbody>
                {data.recent_transactions.map(tx => (
                  <tr key={tx.id}>
                    <td className="td-mono">{tx.id}</td>
                    <td>{new Date(tx.created_at).toLocaleString('en-PH', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', timeZone: 'Asia/Manila' })}</td>
                    <td>{tx.bottle_count}</td>
                    <td>{tx.reward_amount}</td>
                    <td><span className="badge badge--muted">{tx.node_id}</span></td>
                    <td><span className={`badge badge--${tx.status === 'Accepted' ? 'green' : 'red'}`}>{tx.status}</span></td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        ) : (
          <div className="empty-state"><div className="empty-state__icon">📋</div><h4>No transactions yet</h4><p>Transactions will appear here once Arduino is connected.</p></div>
        )}
      </div>
    </AdminLayout>
  )
}

function BarChart({ data }) {
  const vals = Object.values(data)
  const labels = Object.keys(data).map(d => {
    const dt = new Date(d)
    return dt.toLocaleDateString('en-PH', { month: 'short', day: 'numeric', timeZone: 'Asia/Manila' })
  })
  const max = Math.max(...vals, 1)
  return (
    <div style={{ display: 'flex', alignItems: 'flex-end', gap: '10px', height: '140px', marginTop: '.5rem' }}>
      {vals.map((v, i) => {
        const h = Math.max((v / max) * 120, v > 0 ? 8 : 2)
        return (
          <div key={labels[i]} style={{ flex: 1, display: 'flex', flexDirection: 'column', alignItems: 'center', gap: '4px' }}>
            <span style={{ fontSize: '.72rem', color: 'var(--muted)', fontWeight: 700 }}>{v || ''}</span>
            <div style={{ width: '100%', height: `${h}px`, background: 'var(--green-400)', borderRadius: '6px 6px 0 0', transition: 'height .6s' }}></div>
            <span style={{ fontSize: '.68rem', color: 'var(--muted)', textAlign: 'center', lineHeight: 1.2 }}>{labels[i]}</span>
          </div>
        )
      })}
    </div>
  )
}
