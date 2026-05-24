import { useEffect, useState } from 'react'
import AdminLayout from '../../components/AdminLayout'
import { useAdminAuth } from '../../hooks/useAuth'

export default function Settings() {
  const { token } = useAdminAuth()
  const [pwForm, setPwForm] = useState({ current: '', newPw: '', confirm: '' })
  const [clearTx, setClearTx] = useState('')
  const [clearMsg, setClearMsg] = useState('')
  const [alerts, setAlerts] = useState([])

  useEffect(() => { document.title = 'Settings — BottleBack Admin' }, [])

  function addAlert(type, text) {
    const id = Date.now()
    setAlerts(a => [...a, { id, type, text }])
    setTimeout(() => setAlerts(a => a.filter(x => x.id !== id)), 4000)
  }

  async function handleChangePass(e) {
    e.preventDefault()
    if (pwForm.newPw.length < 8) { addAlert('error', 'New password must be at least 8 characters.'); return }
    if (pwForm.newPw !== pwForm.confirm) { addAlert('error', 'Passwords do not match.'); return }
    addAlert('info', 'Password change is managed in backend/auth.py for security.')
    setPwForm({ current: '', newPw: '', confirm: '' })
  }

  async function handleClearTransactions(e) {
    e.preventDefault()
    if (clearTx !== 'DELETE') { addAlert('error', 'Type DELETE exactly to confirm.'); return }
    if (!confirm('This will DELETE ALL transactions. Are you sure?')) return
    const res = await fetch('/api/admin/transactions', { method: 'DELETE', headers: { Authorization: `Bearer ${token}` } })
    if (res.ok) { addAlert('success', 'All transactions cleared.'); setClearTx('') }
  }

  async function handleClearMessages(e) {
    e.preventDefault()
    if (clearMsg !== 'DELETE') { addAlert('error', 'Type DELETE exactly to confirm.'); return }
    if (!confirm('This will DELETE ALL messages. Are you sure?')) return
    const res = await fetch('/api/admin/messages', { method: 'DELETE', headers: { Authorization: `Bearer ${token}` } })
    if (res.ok) { addAlert('success', 'All contact messages cleared.'); setClearMsg('') }
  }

  const alertColors = { success: 'var(--green-100)', error: 'var(--red-100)', info: 'var(--blue-100)' }
  const alertBorders = { success: 'var(--green-500)', error: 'var(--red-500)', info: 'var(--blue-500)' }
  const alertTextColors = { success: 'var(--green-600)', error: 'var(--red-500)', info: 'var(--blue-500)' }

  return (
    <AdminLayout>
      <div className="page-header">
        <div className="page-header__text">
          <div className="label">Configuration</div>
          <h1>⚙️ Admin <em>Settings</em></h1>
        </div>
      </div>

      {alerts.map(a => (
        <div key={a.id} className="alert" style={{ background: alertColors[a.type], borderLeftColor: alertBorders[a.type], color: alertTextColors[a.type] }}>
          {a.type === 'success' ? '✅' : a.type === 'error' ? '❌' : 'ℹ️'} {a.text}
        </div>
      ))}

      <div className="grid-2">
        {/* CHANGE PASSWORD */}
        <div className="panel">
          <div className="panel-header"><span className="panel-title">🔑 Change Admin Password</span></div>
          <div className="panel-body">
            <form onSubmit={handleChangePass}>
              <div className="form-group"><label className="form-label">Current Password</label><input type="password" className="form-input" value={pwForm.current} onChange={e => setPwForm(f => ({ ...f, current: e.target.value }))} required /></div>
              <div className="form-group"><label className="form-label">New Password (min 8 chars)</label><input type="password" className="form-input" value={pwForm.newPw} onChange={e => setPwForm(f => ({ ...f, newPw: e.target.value }))} minLength={8} required /></div>
              <div className="form-group"><label className="form-label">Confirm New Password</label><input type="password" className="form-input" value={pwForm.confirm} onChange={e => setPwForm(f => ({ ...f, confirm: e.target.value }))} required /></div>
              <button type="submit" className="btn btn--primary">Update Password</button>
            </form>
          </div>
        </div>

        {/* DB INFO */}
        <div className="panel">
          <div className="panel-header"><span className="panel-title">🗄️ Database Info</span></div>
          <div className="panel-body">
            <table style={{ width: '100%', fontSize: '.88rem' }}>
              {[['Engine', 'SQLite (SQLAlchemy)'], ['File', 'bottleback.db'], ['ORM', 'SQLAlchemy 2.0'], ['Status', '● Connected']].map(([k, v]) => (
                <tr key={k} style={{ borderBottom: '1px solid var(--border)' }}><td style={{ padding: '.6rem 0', color: 'var(--muted)' }}>{k}</td><td><code>{v}</code></td></tr>
              ))}
            </table>
            <div className="alert alert--info" style={{ marginTop: '1rem' }}>
              <span>ℹ</span>
              <p>Edit DB path in <code>backend/database.py</code>. Admin credentials are in <code>backend/auth.py</code>.</p>
            </div>
          </div>
        </div>
      </div>

      {/* DANGER ZONE */}
      <div className="panel" style={{ borderColor: '#fca5a5' }}>
        <div className="panel-header" style={{ background: '#fff5f5', borderColor: '#fca5a5' }}>
          <span className="panel-title" style={{ color: 'var(--red-500)' }}>⚠️ Danger Zone</span>
        </div>
        <div className="panel-body">
          <div className="alert alert--error" style={{ marginBottom: '1.4rem' }}>
            <span>🚨</span>
            <p>These actions are <strong>irreversible</strong>. They permanently delete data from the database. Type <code>DELETE</code> exactly to confirm.</p>
          </div>
          <div className="grid-2">
            <div style={{ border: '1px solid #fca5a5', borderRadius: 'var(--r-md)', padding: '1.2rem' }}>
              <h4 style={{ color: 'var(--red-500)', marginBottom: '.4rem' }}>Clear All Transactions</h4>
              <p style={{ fontSize: '.85rem', marginBottom: '1rem' }}>Permanently deletes all bottle transaction records. Machine stats will reset to zero.</p>
              <form onSubmit={handleClearTransactions}>
                <div className="form-group"><label className="form-label">Type DELETE to confirm</label><input type="text" className="form-input" placeholder="DELETE" value={clearTx} onChange={e => setClearTx(e.target.value)} required /></div>
                <button type="submit" className="btn btn--danger">🗑 Clear All Transactions</button>
              </form>
            </div>
            <div style={{ border: '1px solid #fca5a5', borderRadius: 'var(--r-md)', padding: '1.2rem' }}>
              <h4 style={{ color: 'var(--red-500)', marginBottom: '.4rem' }}>Clear All Messages</h4>
              <p style={{ fontSize: '.85rem', marginBottom: '1rem' }}>Permanently deletes all contact form messages from the inbox.</p>
              <form onSubmit={handleClearMessages}>
                <div className="form-group"><label className="form-label">Type DELETE to confirm</label><input type="text" className="form-input" placeholder="DELETE" value={clearMsg} onChange={e => setClearMsg(e.target.value)} required /></div>
                <button type="submit" className="btn btn--danger">🗑 Clear All Messages</button>
              </form>
            </div>
          </div>
        </div>
      </div>

      {/* ADMIN INFO */}
      <div className="panel">
        <div className="panel-header"><span className="panel-title">ℹ Admin Panel Info</span></div>
        <div className="panel-body">
          <div className="grid-2">
            <div>
              <h4 style={{ marginBottom: '.6rem' }}>File Structure</h4>
              <code style={{ display: 'block', background: 'var(--green-900)', color: 'var(--green-300)', padding: '.9rem 1.1rem', borderRadius: '10px', fontSize: '.78rem', lineHeight: 1.9 }}>
                bottleback/<br />
                ├── backend/<br />
                │&nbsp;&nbsp; ├── main.py<br />
                │&nbsp;&nbsp; ├── auth.py&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;← credentials<br />
                │&nbsp;&nbsp; ├── models.py<br />
                │&nbsp;&nbsp; ├── schemas.py<br />
                │&nbsp;&nbsp; └── routers/<br />
                └── frontend/<br />
                &nbsp;&nbsp;&nbsp;&nbsp;└── src/pages/admin/
              </code>
            </div>
            <div>
              <h4 style={{ marginBottom: '.6rem' }}>Access URL</h4>
              <p style={{ fontSize: '.88rem', marginBottom: '.8rem' }}>Navigate to the admin panel at:</p>
              <code style={{ display: 'block', background: 'var(--green-900)', color: 'var(--green-300)', padding: '.9rem 1.1rem', borderRadius: '10px', fontSize: '.82rem' }}>
                http://localhost:5173/admin/login
              </code>
              <p style={{ fontSize: '.82rem', marginTop: '.8rem', color: 'var(--muted)' }}>Default credentials are in <code>backend/auth.py</code>. Change before deploying.</p>
            </div>
          </div>
        </div>
      </div>
    </AdminLayout>
  )
}
