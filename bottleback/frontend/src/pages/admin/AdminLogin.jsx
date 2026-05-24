import { useEffect, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { useAdminAuth } from '../../hooks/useAuth'
import '../../assets/css/auth.css'

export default function AdminLogin() {
  const { isAdmin, adminLogin } = useAdminAuth()
  const navigate = useNavigate()
  const [username, setUsername] = useState('')
  const [password, setPassword] = useState('')
  const [error, setError] = useState('')
  const [loading, setLoading] = useState(false)

  useEffect(() => {
    document.title = 'Admin Login — BottleBack'
    if (isAdmin) navigate('/admin')
  }, [isAdmin, navigate])

  async function handleSubmit(e) {
    e.preventDefault()
    setError(''); setLoading(true)
    try {
      const res = await fetch('/api/admin/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password }),
      })
      const data = await res.json()
      if (res.ok) {
        adminLogin(data.access_token)
        navigate('/admin')
      } else {
        setError(data.detail || 'Invalid username or password.')
      }
    } catch {
      setError('Network error. Please try again.')
    }
    setLoading(false)
  }

  return (
    <div className="admin-login-body">
      <div className="left-bg">
        <div className="auth-orb" style={{ width: '600px', height: '600px', background: 'var(--green-600)', opacity: '.2', top: '-200px', right: '-150px' }}></div>
        <div className="auth-orb" style={{ width: '400px', height: '400px', background: 'var(--teal-500)', opacity: '.15', bottom: '-100px', left: '-100px' }}></div>
        <div className="grid-lines"></div>
      </div>

      <div className="admin-card">
        <div className="auth-logo" style={{ marginBottom: '.5rem' }}>
          <span className="auth-logo-icon">🌱</span>
          <span className="auth-logo-text">Bottle<strong>Back</strong></span>
        </div>
        <div className="admin-badge">Admin Portal</div>
        <h1 style={{ fontFamily: 'var(--font-serif)', color: 'var(--white)', fontSize: '2rem', marginBottom: '.4rem' }}>Sign <em>In</em></h1>
        <p className="form-sub" style={{ color: 'rgba(255,255,255,.5)', marginBottom: '2rem' }}>Access the BottleBack administration panel.</p>

        {error && <div className="admin-error">❌ {error}</div>}

        <form onSubmit={handleSubmit}>
          <div className="admin-field">
            <label className="admin-label" htmlFor="username">Username</label>
            <input className="admin-input" type="text" id="username" placeholder="Enter admin username" value={username} onChange={e => setUsername(e.target.value)} autoComplete="username" required />
          </div>
          <div className="admin-field">
            <label className="admin-label" htmlFor="password">Password</label>
            <input className="admin-input" type="password" id="password" placeholder="••••••••••••" value={password} onChange={e => setPassword(e.target.value)} autoComplete="current-password" required />
          </div>
          <button type="submit" className="admin-btn-submit" disabled={loading}>{loading ? 'Signing in…' : 'Sign In →'}</button>
        </form>

        <div className="admin-back"><Link to="/">← Back to public site</Link></div>
        <div className="admin-hint">
          <strong>Default credentials (dev only):</strong><br />
          Username: <code>admin</code> &nbsp;·&nbsp; Password: <code>bottleback2027</code><br />
          Change these in <code>backend/auth.py</code> before deploying.
        </div>
      </div>
    </div>
  )
}
