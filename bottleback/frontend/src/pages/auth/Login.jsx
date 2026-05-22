import { useEffect, useState } from 'react'
import { Link, useNavigate, useSearchParams } from 'react-router-dom'
import { useAuth } from '../../hooks/useAuth'
import '../../assets/css/auth.css'

export default function Login() {
  const { user, login } = useAuth()
  const navigate = useNavigate()
  const [params] = useSearchParams()
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [showPw, setShowPw] = useState(false)
  const [errors, setErrors] = useState([])
  const [loading, setLoading] = useState(false)

  useEffect(() => {
    document.title = 'Sign In — BottleBack'
    if (user) navigate('/profile')
  }, [user, navigate])

  async function handleSubmit(e) {
    e.preventDefault()
    setErrors([])
    const errs = []
    if (!email) errs.push('Email is required.')
    if (!password) errs.push('Password is required.')
    if (errs.length) { setErrors(errs); return }
    setLoading(true)
    try {
      const res = await fetch('/api/users/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password }),
      })
      const data = await res.json()
      if (res.ok) {
        login(data.access_token)
        navigate(params.get('redirect') || '/profile')
      } else {
        setErrors([data.detail || 'Login failed.'])
      }
    } catch {
      setErrors(['Network error. Please try again.'])
    }
    setLoading(false)
  }

  return (
    <div className="auth-body">
      {/* LEFT */}
      <div className="left-panel">
        <div className="left-bg">
          <div className="auth-orb auth-orb1"></div>
          <div className="auth-orb auth-orb2"></div>
          <div className="auth-orb auth-orb3"></div>
          <div className="grid-lines"></div>
        </div>
        <div className="left-content">
          <Link to="/" className="nav-back">← Back to BottleBack</Link>
          <div className="auth-logo">
            <span className="auth-logo-icon">🌱</span>
            <span className="auth-logo-text">Bottle<strong>Back</strong></span>
          </div>
          <h1 className="auth-h1">Recycle.<br />Earn. <em>Track.</em></h1>
          <p className="hero-sub">Join Barangay Muzon's smart recycling program. Deposit plastic bottles, earn rewards, and monitor your impact — all in one place.</p>
          <div className="perks">
            <div className="perk"><div className="perk-icon">🍶</div><div className="perk-text"><strong>Track Your Bottles</strong><span>See every bottle you've deposited and all-time totals.</span></div></div>
            <div className="perk"><div className="perk-icon">🎁</div><div className="perk-text"><strong>Monitor Your Rewards</strong><span>Keep tabs on drinks and biscuits you've earned.</span></div></div>
            <div className="perk"><div className="perk-icon">🌿</div><div className="perk-text"><strong>See Your Impact</strong><span>Know exactly how much you've contributed to a cleaner Muzon.</span></div></div>
          </div>
          <div className="left-footer">
            <p>BottleBack · Our Lady of Fatima University<br />College of Computer Studies · BSIT Capstone 2027<br />Barangay Muzon, Taytay, Rizal</p>
          </div>
        </div>
      </div>

      {/* RIGHT */}
      <div className="right-panel">
        <div className="form-wrap">
          <div className="form-eyebrow">Resident Portal</div>
          <div className="form-title">Welcome <em>Back</em></div>
          <p className="form-sub">Don't have an account? <Link to="/register">Create one free →</Link></p>

          {params.get('timeout') && <div className="alert-auth alert-info">⏱ Your session expired. Please sign in again.</div>}
          {params.get('logout') && <div className="alert-auth alert-success">✅ You have been signed out successfully.</div>}
          {params.get('registered') && <div className="alert-auth alert-success">✅ Account created! Please sign in.</div>}
          {errors.length > 0 && <div className="alert-auth alert-error"><ul>{errors.map(e => <li key={e}>{e}</li>)}</ul></div>}

          <form onSubmit={handleSubmit} noValidate>
            <div className="field">
              <div className="field-label"><label className="auth-label" htmlFor="email">Email Address</label></div>
              <input className={`auth-input${errors.length ? ' invalid' : ''}`} type="email" id="email" placeholder="you@example.com" value={email} onChange={e => setEmail(e.target.value)} autoComplete="email" required />
            </div>
            <div className="field">
              <div className="field-label">
                <label className="auth-label" htmlFor="password">Password</label>
              </div>
              <div className="input-wrap">
                <input className={`auth-input${errors.length ? ' invalid' : ''}`} type={showPw ? 'text' : 'password'} id="password" placeholder="••••••••••" value={password} onChange={e => setPassword(e.target.value)} autoComplete="current-password" required />
                <button type="button" className="toggle-pw" onClick={() => setShowPw(v => !v)}>{showPw ? '🙈' : '👁'}</button>
              </div>
            </div>
            <button type="submit" className="btn-submit" disabled={loading}>{loading ? 'Signing in…' : 'Sign In →'}</button>
          </form>

          <div className="auth-divider">or</div>
          <Link to="/register" className="create-link">New here? <strong>Create a free account</strong></Link>
          <div className="form-footer"><Link to="/">← Return to public site</Link></div>
        </div>
      </div>
    </div>
  )
}
