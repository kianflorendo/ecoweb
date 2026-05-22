import { useEffect, useState, useCallback } from 'react'
import AdminLayout from '../../components/AdminLayout'
import { useAdminAuth } from '../../hooks/useAuth'

export default function Users() {
  const { token } = useAdminAuth()
  const [data, setData] = useState(null)
  const [q, setQ] = useState('')
  const [page, setPage] = useState(1)
  const [msg, setMsg] = useState('')

  useEffect(() => { document.title = 'Users — BottleBack Admin' }, [])

  const fetchData = useCallback(async (query = q, p = page) => {
    if (!token) return
    const params = new URLSearchParams({ q: query, page: p, per_page: 20 })
    const res = await fetch(`/api/admin/users?${params}`, { headers: { Authorization: `Bearer ${token}` } })
    if (res.ok) setData(await res.json())
  }, [token, q, page])

  useEffect(() => { fetchData() }, [token, q, page])

  async function handleToggle(id) {
    const res = await fetch(`/api/admin/users/${id}/toggle`, { method: 'PATCH', headers: { Authorization: `Bearer ${token}` } })
    if (res.ok) { setMsg('toggled'); fetchData() }
  }

  async function handleDelete(id, name) {
    if (!confirm(`Delete user ${name}? This cannot be undone.`)) return
    const res = await fetch(`/api/admin/users/${id}`, { method: 'DELETE', headers: { Authorization: `Bearer ${token}` } })
    if (res.ok) { setMsg('deleted'); fetchData() }
  }

  return (
    <AdminLayout>
      <div className="page-header">
        <div className="page-header__text">
          <div className="label">Accounts</div>
          <h1>👥 Registered <em>Users</em></h1>
        </div>
      </div>

      {msg === 'toggled' && <div className="alert alert--success">✅ User status updated.</div>}
      {msg === 'deleted' && <div className="alert alert--success">✅ User deleted.</div>}

      {data && (
        <div className="kpi-grid" style={{ marginBottom: '1.8rem' }}>
          <div className="kpi-card kpi-card--blue"><span className="kpi-icon">👥</span><div><div className="kpi-label">Total Users</div><div className="kpi-value">{data.kpi_total}</div></div></div>
          <div className="kpi-card kpi-card--green"><span className="kpi-icon">✅</span><div><div className="kpi-label">Active</div><div className="kpi-value">{data.kpi_active}</div></div></div>
          <div className="kpi-card kpi-card--teal"><span className="kpi-icon">🆕</span><div><div className="kpi-label">Joined Today</div><div className="kpi-value">{data.kpi_today}</div></div></div>
          <div className="kpi-card kpi-card--yellow"><span className="kpi-icon">⏸️</span><div><div className="kpi-label">Inactive</div><div className="kpi-value">{data.kpi_inactive}</div></div></div>
        </div>
      )}

      <div className="panel">
        <div className="panel-header">
          <span className="panel-title">All Accounts <span style={{ fontSize: '.82rem', fontWeight: 400, color: 'var(--muted)' }}>({data?.total?.toLocaleString() ?? 0} records)</span></span>
        </div>
        <div className="panel-body" style={{ paddingBottom: 0 }}>
          <form onSubmit={e => { e.preventDefault(); setPage(1); fetchData(q, 1) }} className="filter-bar">
            <input type="text" className="form-input search-input" placeholder="🔍 Search name, email, barangay…" value={q} onChange={e => setQ(e.target.value)} />
            <button type="submit" className="btn btn--primary btn--sm">Search</button>
            {q && <button type="button" className="btn btn--ghost btn--sm" onClick={() => { setQ(''); setPage(1) }}>Clear</button>}
          </form>
        </div>

        {data?.items?.length ? (
          <>
            <div className="table-wrap">
              <table>
                <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Barangay</th><th>Bottles</th><th>Rewards</th><th>Joined</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                  {data.items.map(u => (
                    <tr key={u.id}>
                      <td className="td-mono">{u.id}</td>
                      <td>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '.6rem' }}>
                          <div style={{ width: '32px', height: '32px', borderRadius: '50%', background: 'linear-gradient(135deg,var(--green-400),var(--teal-500))', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '.8rem', fontWeight: 700, color: 'var(--white)', flexShrink: 0 }}>
                            {u.first_name[0]?.toUpperCase()}
                          </div>
                          <strong style={{ color: 'var(--ink)' }}>{u.first_name} {u.last_name}</strong>
                        </div>
                      </td>
                      <td style={{ fontSize: '.85rem' }}>{u.email}</td>
                      <td><span className="badge badge--muted">{u.barangay}</span></td>
                      <td style={{ fontWeight: 600, color: 'var(--green-600)' }}>{u.total_bottles.toLocaleString()}</td>
                      <td style={{ fontWeight: 600, color: 'var(--teal-500)' }}>{u.total_rewards.toLocaleString()}</td>
                      <td style={{ fontSize: '.82rem', color: 'var(--muted)' }}>{new Date(u.created_at).toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' })}</td>
                      <td><span className={`badge badge--${u.is_active ? 'green' : 'red'}`}>{u.is_active ? 'Active' : 'Inactive'}</span></td>
                      <td>
                        <div style={{ display: 'flex', gap: '.4rem' }}>
                          <button onClick={() => handleToggle(u.id)} className="btn btn--ghost btn--sm" title={u.is_active ? 'Deactivate' : 'Activate'}>{u.is_active ? '⏸️' : '▶️'}</button>
                          <button onClick={() => handleDelete(u.id, u.first_name)} className="btn btn--danger btn--sm">🗑</button>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
            <div className="panel-body" style={{ paddingTop: '1rem' }}>
              <div className="pagination">
                {Array.from({ length: data.total_pages }, (_, i) => i + 1).map(p => (
                  <button key={p} onClick={() => setPage(p)} className={`page-btn${p === page ? ' page-btn--active' : ''}`}>{p}</button>
                ))}
                <span style={{ fontSize: '.78rem', color: 'var(--muted)', marginLeft: '.5rem' }}>Page {page} of {data.total_pages}</span>
              </div>
            </div>
          </>
        ) : (
          <div className="empty-state"><div className="empty-state__icon">👥</div><h4>No users yet</h4><p>Registered residents will appear here once someone creates an account.</p></div>
        )}
      </div>
    </AdminLayout>
  )
}
