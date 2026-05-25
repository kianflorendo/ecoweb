import { useEffect, useState, useCallback } from 'react'
import AdminLayout from '../../components/AdminLayout'
import { useAdminAuth } from '../../hooks/useAuth'
import { phDateTime } from '../../utils/date'

export default function Transactions() {
  const { token } = useAdminAuth()
  const [data, setData] = useState(null)
  const [status, setStatus] = useState('')
  const [q, setQ] = useState('')
  const [page, setPage] = useState(1)
  const [msg, setMsg] = useState('')

  useEffect(() => { document.title = 'Transactions — BottleBack Admin' }, [])

  const fetchData = useCallback(async (s = status, query = q, p = page) => {
    if (!token) return
    const params = new URLSearchParams({ status: s, q: query, page: p, per_page: 20 })
    const res = await fetch(`/api/admin/transactions?${params}`, { headers: { Authorization: `Bearer ${token}` } })
    if (res.ok) setData(await res.json())
  }, [token, status, q, page])

  useEffect(() => { fetchData() }, [token, status, q, page])

  async function handleDelete(id) {
    if (!confirm(`Delete transaction #${id}?`)) return
    const res = await fetch(`/api/admin/transactions/${id}`, { method: 'DELETE', headers: { Authorization: `Bearer ${token}` } })
    if (res.ok) { setMsg('success'); fetchData() }
    else setMsg('error')
  }

  function handleFilter(e) {
    e.preventDefault()
    setPage(1)
    fetchData(status, q, 1)
  }

  return (
    <AdminLayout>
      <div className="page-header">
        <div className="page-header__text">
          <div className="label">Records</div>
          <h1>🔄 <em>Transactions</em></h1>
        </div>
      </div>

      {msg === 'success' && <div className="alert alert--success">✅ Transaction deleted.</div>}
      {msg === 'error' && <div className="alert alert--error">❌ Could not delete transaction.</div>}

      <div className="panel">
        <div className="panel-header">
          <span className="panel-title">All Transactions <span style={{ fontSize: '.82rem', fontWeight: 400, color: 'var(--muted)' }}>({data?.total?.toLocaleString() ?? 0} records)</span></span>
        </div>
        <div className="panel-body" style={{ paddingBottom: 0 }}>
          <form onSubmit={handleFilter} className="filter-bar">
            <input type="text" className="form-input search-input" placeholder="🔍 Search by node…" value={q} onChange={e => setQ(e.target.value)} />
            <select className="form-select" style={{ maxWidth: '160px' }} value={status} onChange={e => setStatus(e.target.value)}>
              <option value="">All Statuses</option>
              <option value="Accepted">✅ Accepted</option>
              <option value="Rejected">❌ Rejected</option>
            </select>
            <button type="submit" className="btn btn--primary btn--sm">Filter</button>
            {(q || status) && <button type="button" className="btn btn--ghost btn--sm" onClick={() => { setQ(''); setStatus(''); setPage(1) }}>Clear</button>}
          </form>
        </div>

        {data?.items?.length ? (
          <>
            <div className="table-wrap">
              <table>
                <thead><tr><th>#</th><th>Date &amp; Time</th><th>Bottles</th><th>Reward</th><th>Node</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                  {data.items.map(tx => (
                    <tr key={tx.id}>
                      <td className="td-mono">{tx.id}</td>
                      <td>{phDateTime(tx.created_at, { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' })}</td>
                      <td>{tx.bottle_count}</td>
                      <td>{tx.reward_amount}</td>
                      <td><span className="badge badge--muted">{tx.node_id}</span></td>
                      <td><span className={`badge badge--${tx.status === 'Accepted' ? 'green' : 'red'}`}>{tx.status}</span></td>
                      <td><button onClick={() => handleDelete(tx.id)} className="btn btn--danger btn--sm">🗑</button></td>
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
          <div className="empty-state"><div className="empty-state__icon">📋</div><h4>No transactions found</h4><p>Connect your Arduino or adjust your filters.</p></div>
        )}
      </div>
    </AdminLayout>
  )
}
