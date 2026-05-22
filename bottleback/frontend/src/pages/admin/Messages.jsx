import { useEffect, useState, useCallback } from 'react'
import AdminLayout from '../../components/AdminLayout'
import { useAdminAuth } from '../../hooks/useAuth'

export default function Messages() {
  const { token } = useAdminAuth()
  const [data, setData] = useState(null)
  const [q, setQ] = useState('')
  const [page, setPage] = useState(1)
  const [viewMsg, setViewMsg] = useState(null)
  const [notice, setNotice] = useState('')

  useEffect(() => { document.title = 'Messages — BottleBack Admin' }, [])

  const fetchData = useCallback(async (query = q, p = page) => {
    if (!token) return
    const params = new URLSearchParams({ q: query, page: p, per_page: 15 })
    const res = await fetch(`/api/admin/messages?${params}`, { headers: { Authorization: `Bearer ${token}` } })
    if (res.ok) setData(await res.json())
  }, [token, q, page])

  useEffect(() => { fetchData() }, [token, q, page])

  async function handleDelete(id) {
    if (!confirm('Delete this message?')) return
    const res = await fetch(`/api/admin/messages/${id}`, { method: 'DELETE', headers: { Authorization: `Bearer ${token}` } })
    if (res.ok) { setNotice('deleted'); setViewMsg(null); fetchData() }
  }

  async function handleView(id) {
    const res = await fetch(`/api/admin/messages/${id}`, { headers: { Authorization: `Bearer ${token}` } })
    if (res.ok) setViewMsg(await res.json())
  }

  return (
    <AdminLayout>
      <div className="page-header">
        <div className="page-header__text">
          <div className="label">Inbox</div>
          <h1>✉️ Contact <em>Messages</em></h1>
        </div>
        <a href={`/api/admin/export/messages?token=${token}`} className="btn btn--outline btn--sm">📥 Export CSV</a>
      </div>

      {notice === 'deleted' && <div className="alert alert--success">✅ Message deleted.</div>}

      {/* VIEW MODAL */}
      {viewMsg && (
        <div className="panel" style={{ borderColor: 'var(--green-300)', marginBottom: '1.6rem' }}>
          <div className="panel-header" style={{ background: 'var(--green-50)' }}>
            <span className="panel-title">📨 Message #{viewMsg.id}</span>
            <button onClick={() => setViewMsg(null)} className="btn btn--ghost btn--sm">✕ Close</button>
          </div>
          <div className="panel-body">
            <div className="grid-2" style={{ marginBottom: '1rem' }}>
              <div><p className="form-label">From</p><p style={{ fontWeight: 600, color: 'var(--ink)' }}>{viewMsg.name}</p><p style={{ fontSize: '.85rem' }}>{viewMsg.email}</p></div>
              <div><p className="form-label">Received</p><p style={{ fontSize: '.9rem' }}>{new Date(viewMsg.created_at).toLocaleString('en-PH', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Manila' })}</p></div>
            </div>
            <div style={{ marginBottom: '1rem' }}><p className="form-label">Subject</p><p style={{ fontWeight: 600, color: 'var(--ink)' }}>{viewMsg.subject}</p></div>
            <div><p className="form-label">Message</p><div style={{ background: 'var(--off-white)', border: '1px solid var(--border)', borderRadius: '10px', padding: '1rem', fontSize: '.92rem', lineHeight: 1.7, whiteSpace: 'pre-wrap' }}>{viewMsg.message}</div></div>
            <div style={{ marginTop: '1rem', display: 'flex', gap: '.8rem' }}>
              <a href={`mailto:${viewMsg.email}?subject=Re: ${encodeURIComponent(viewMsg.subject)}`} className="btn btn--primary btn--sm">📧 Reply via Email</a>
              <button onClick={() => handleDelete(viewMsg.id)} className="btn btn--danger btn--sm">🗑 Delete</button>
            </div>
          </div>
        </div>
      )}

      <div className="panel">
        <div className="panel-header">
          <span className="panel-title">All Messages <span style={{ fontSize: '.82rem', fontWeight: 400, color: 'var(--muted)' }}>({data?.total?.toLocaleString() ?? 0} total)</span></span>
        </div>
        <div className="panel-body" style={{ paddingBottom: 0 }}>
          <form onSubmit={e => { e.preventDefault(); setPage(1); fetchData(q, 1) }} className="filter-bar">
            <input type="text" className="form-input search-input" placeholder="🔍 Search by name, email, subject…" value={q} onChange={e => setQ(e.target.value)} />
            <button type="submit" className="btn btn--primary btn--sm">Search</button>
            {q && <button type="button" className="btn btn--ghost btn--sm" onClick={() => { setQ(''); setPage(1) }}>Clear</button>}
          </form>
        </div>

        {data?.items?.length ? (
          <>
            <div className="table-wrap">
              <table>
                <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Subject</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody>
                  {data.items.map(m => (
                    <tr key={m.id}>
                      <td className="td-mono">{m.id}</td>
                      <td><strong style={{ color: 'var(--ink)' }}>{m.name}</strong></td>
                      <td style={{ fontSize: '.85rem' }}>{m.email}</td>
                      <td>{m.subject.length > 40 ? m.subject.slice(0, 40) + '…' : m.subject}</td>
                      <td style={{ fontSize: '.82rem', color: 'var(--muted)' }}>{new Date(m.created_at).toLocaleString('en-PH', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Manila' })}</td>
                      <td>
                        <div style={{ display: 'flex', gap: '.4rem' }}>
                          <button onClick={() => handleView(m.id)} className="btn btn--outline btn--sm">👁 View</button>
                          <a href={`mailto:${m.email}`} className="btn btn--ghost btn--sm">✉️</a>
                          <button onClick={() => handleDelete(m.id)} className="btn btn--danger btn--sm">🗑</button>
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
              </div>
            </div>
          </>
        ) : (
          <div className="empty-state"><div className="empty-state__icon">📭</div><h4>No messages yet</h4><p>Contact form submissions will appear here.</p></div>
        )}
      </div>
    </AdminLayout>
  )
}
