import { useEffect } from 'react'
import AdminLayout from '../../components/AdminLayout'
import { useAdminAuth } from '../../hooks/useAuth'

export default function Export() {
  const { token } = useAdminAuth()

  useEffect(() => { document.title = 'Export Data — BottleBack Admin' }, [])

  return (
    <AdminLayout>
      <div className="page-header">
        <div className="page-header__text">
          <div className="label">Data Export</div>
          <h1>📥 Export <em>Data</em></h1>
        </div>
      </div>

      <div className="grid-2">
        <div className="panel">
          <div className="panel-header"><span className="panel-title">♻️ Transactions CSV</span></div>
          <div className="panel-body">
            <p style={{ marginBottom: '1rem' }}>Export all transaction records as a CSV file. Includes ID, bottle count, reward amount, status, node ID, and timestamp.</p>
            <a
              href={`/api/admin/export/transactions`}
              className="btn btn--primary"
              onClick={e => {
                e.preventDefault()
                fetch('/api/admin/export/transactions', { headers: { Authorization: `Bearer ${token}` } })
                  .then(r => r.blob())
                  .then(blob => {
                    const url = URL.createObjectURL(blob)
                    const a = document.createElement('a')
                    a.href = url; a.download = 'transactions.csv'; a.click()
                    URL.revokeObjectURL(url)
                  })
              }}
            >
              📥 Download Transactions CSV
            </a>
          </div>
        </div>

        <div className="panel">
          <div className="panel-header"><span className="panel-title">✉️ Messages CSV</span></div>
          <div className="panel-body">
            <p style={{ marginBottom: '1rem' }}>Export all contact form messages as a CSV file. Includes ID, name, email, subject, and timestamp.</p>
            <a
              href={`/api/admin/export/messages`}
              className="btn btn--primary"
              onClick={e => {
                e.preventDefault()
                fetch('/api/admin/export/messages', { headers: { Authorization: `Bearer ${token}` } })
                  .then(r => r.blob())
                  .then(blob => {
                    const url = URL.createObjectURL(blob)
                    const a = document.createElement('a')
                    a.href = url; a.download = 'messages.csv'; a.click()
                    URL.revokeObjectURL(url)
                  })
              }}
            >
              📥 Download Messages CSV
            </a>
          </div>
        </div>
      </div>
    </AdminLayout>
  )
}
