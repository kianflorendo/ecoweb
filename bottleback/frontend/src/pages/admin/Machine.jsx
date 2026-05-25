import { useEffect, useState } from 'react'
import AdminLayout from '../../components/AdminLayout'
import { useAdminAuth } from '../../hooks/useAuth'
import { phDateTime } from '../../utils/date'

function MachineCard({ m, token, onUpdate }) {
  const [binInput, setBinInput] = useState(m.bin_level)

  function binColor(l) { if (l >= 90) return 'danger'; if (l >= 70) return 'warn'; return 'ok' }
  function binLabel(l) { if (l >= 90) return '🔴 FULL'; if (l >= 70) return '🟡 HIGH'; return '🟢 OK' }
  const bc = binColor(m.bin_level)

  async function updateBin() {
    const res = await fetch(`/api/admin/machine/${m.node_id}`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` },
      body: JSON.stringify({ bin_level: parseInt(binInput), node_id: m.node_id }),
    })
    if (res.ok) onUpdate()
  }

  async function toggleOnline() {
    const res = await fetch(`/api/admin/machine/${m.node_id}`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` },
      body: JSON.stringify({ is_online: !m.is_online, node_id: m.node_id }),
    })
    if (res.ok) onUpdate()
  }

  return (
    <div className="panel">
      <div className="panel-header">
        <span className="panel-title">🤖 {m.node_id} — Barangay Muzon</span>
        <span className={`badge badge--${m.is_online ? 'green' : 'red'}`}>{m.is_online ? '● Online' : '○ Offline'}</span>
      </div>
      <div className="panel-body">
        <div style={{ display: 'flex', gap: '3rem', flexWrap: 'wrap', alignItems: 'flex-start' }}>
          <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: '.6rem' }}>
            <div className="bin-visual" style={{ width: '72px', height: '160px' }}>
              <div className={`bin-fill${bc !== 'ok' ? ` bin-fill--${bc}` : ''}`} style={{ height: `${m.bin_level}%` }}></div>
              <div className="bin-label">{m.bin_level}%</div>
            </div>
            <span style={{ fontSize: '.78rem', color: 'var(--muted)' }}>Bin Fill</span>
          </div>
          <div style={{ flex: 1, minWidth: '220px' }}>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem', marginBottom: '1.2rem' }}>
              <div className="kpi-card" style={{ padding: '1rem' }}>
                <span className="kpi-icon" style={{ fontSize: '1.4rem' }}>🗑️</span>
                <div><div className="kpi-label">Fill Level</div><div className="kpi-value" style={{ fontSize: '1.5rem' }}>{m.bin_level}%</div></div>
              </div>
              <div className="kpi-card" style={{ padding: '1rem' }}>
                <span className="kpi-icon" style={{ fontSize: '1.4rem' }}>{m.is_online ? '🟢' : '🔴'}</span>
                <div><div className="kpi-label">Status</div><div className="kpi-value" style={{ fontSize: '1.1rem' }}>{m.is_online ? 'Online' : 'Offline'}</div></div>
              </div>
            </div>
            <div className="progress-bar">
              <div className={`progress-fill${bc !== 'ok' ? ` progress-fill--${bc}` : ''}`} style={{ width: `${m.bin_level}%` }}></div>
            </div>
            <p style={{ fontSize: '.8rem', margin: '.4rem 0 .8rem' }}>{binLabel(m.bin_level)} — Last updated: {phDateTime(m.updated_at)}</p>
            <div style={{ display: 'flex', gap: '.6rem', alignItems: 'flex-end', flexWrap: 'wrap' }}>
              <div className="form-group" style={{ margin: 0 }}>
                <label className="form-label" style={{ marginBottom: '.3rem' }}>Set Bin Level (%)</label>
                <input type="number" className="form-input" style={{ maxWidth: '120px' }} min="0" max="100" value={binInput} onChange={e => setBinInput(e.target.value)} />
              </div>
              <button onClick={updateBin} className="btn btn--primary btn--sm" style={{ marginBottom: 0 }}>Update</button>
            </div>
          </div>
          <div>
            <h4 style={{ marginBottom: '.8rem' }}>Online Status</h4>
            <button onClick={toggleOnline} className={`btn btn--${m.is_online ? 'danger' : 'primary'} btn--sm`}>
              {m.is_online ? 'Set Offline' : 'Set Online'}
            </button>
          </div>
        </div>
      </div>
    </div>
  )
}

export default function Machine() {
  const { token } = useAdminAuth()
  const [machines, setMachines] = useState([])
  const [msg, setMsg] = useState('')

  useEffect(() => { document.title = 'Machine Status — BottleBack Admin' }, [])

  function fetchMachines() {
    fetch('/api/admin/machine', { headers: { Authorization: `Bearer ${token}` } })
      .then(r => r.ok ? r.json() : [])
      .then(setMachines)
  }

  useEffect(() => {
    if (!token) return
    fetchMachines()
    const id = setInterval(fetchMachines, 10000)
    return () => clearInterval(id)
  }, [token])

  function handleUpdate() {
    setMsg('updated')
    fetchMachines()
  }

  return (
    <AdminLayout>
      <div className="page-header">
        <div className="page-header__text">
          <div className="label">Hardware</div>
          <h1>🤖 Machine <em>Status</em></h1>
        </div>
      </div>

      {msg === 'updated' && <div className="alert alert--success">✅ Machine status updated.</div>}

      <div className="panel" style={{ marginBottom: '1.6rem' }}>
        <div className="panel-header"><span className="panel-title">🔌 Arduino Connection Guide</span></div>
        <div className="panel-body">
          <div className="grid-2">
            <div>
              <h4 style={{ marginBottom: '.6rem' }}>API Endpoint</h4>
              <p style={{ fontSize: '.88rem', marginBottom: '.8rem' }}>The Arduino sends POST data via a Python serial bridge to:</p>
              <code style={{ display: 'block', background: 'var(--green-900)', color: 'var(--green-300)', padding: '.9rem 1.1rem', borderRadius: '10px', fontSize: '.82rem', lineHeight: 1.7 }}>
                POST http://localhost:8000/api/receive-data<br /><br />
                Fields:<br />
                &nbsp;bottle_count  — int (default 1)<br />
                &nbsp;reward_amount — int (default 1)<br />
                &nbsp;status        — Accepted | Rejected<br />
                &nbsp;bin_level     — int 0–100<br />
                &nbsp;node_id       — string (default node_001)
              </code>
            </div>
            <div>
              <h4 style={{ marginBottom: '.6rem' }}>Quick Test (curl)</h4>
              <p style={{ fontSize: '.88rem', marginBottom: '.8rem' }}>Simulate an Arduino POST to test the endpoint:</p>
              <code style={{ display: 'block', background: 'var(--green-900)', color: 'var(--green-300)', padding: '.9rem 1.1rem', borderRadius: '10px', fontSize: '.82rem', lineHeight: 1.7, wordBreak: 'break-all' }}>
                curl -X POST \<br />
                &nbsp;http://localhost:8000/api/receive-data \<br />
                &nbsp;-d "bottle_count=1&reward_amount=1<br />
                &nbsp;&status=Accepted&bin_level=25<br />
                &nbsp;&node_id=node_001"
              </code>
            </div>
          </div>
        </div>
      </div>

      {machines.length
        ? machines.map(m => <MachineCard key={m.node_id} m={m} token={token} onUpdate={handleUpdate} />)
        : <div className="panel"><div className="empty-state"><div className="empty-state__icon">🤖</div><h4>No machine nodes found</h4><p>Run seed.py to initialize the machine row.</p></div></div>
      }
    </AdminLayout>
  )
}
