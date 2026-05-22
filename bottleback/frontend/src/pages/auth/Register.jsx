import { useEffect, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { useAuth } from '../../hooks/useAuth'
import '../../assets/css/auth.css'

const BARANGAYS = ['Muzon','Dolores','San Juan','Sta. Ana','San Isidro','Pagala','Kalinawan','Manga','Sampaloc','Sta. Cruz']

export default function Register() {
  const { user, login } = useAuth()
  const navigate = useNavigate()
  const [form, setForm] = useState({ first_name: '', last_name: '', email: '', barangay: 'Muzon', password: '', confirm: '' })
  const [agree, setAgree] = useState(false)
  const [showPw, setShowPw] = useState(false)
  const [showConfirm, setShowConfirm] = useState(false)
  const [pwStrength, setPwStrength] = useState({ w: '0%', c: '#e5e7eb', t: 'Enter a password' })
  const [errors, setErrors] = useState([])
  const [loading, setLoading] = useState(false)

  useEffect(() => {
    document.title = 'Create Account — BottleBack'
    if (user) navigate('/profile')
  }, [user, navigate])

  function checkStrength(pw) {
    let score = 0
    if (pw.length >= 8) score++
    if (pw.length >= 12) score++
    if (/[A-Z]/.test(pw)) score++
    if (/[0-9]/.test(pw)) score++
    if (/[^A-Za-z0-9]/.test(pw)) score++
    const levels = [
      { w: '0%', c: '#e5e7eb', t: 'Enter a password' },
      { w: '25%', c: '#ef4444', t: 'Weak' },
      { w: '50%', c: '#f97316', t: 'Fair' },
      { w: '75%', c: '#eab308', t: 'Good' },
      { w: '90%', c: '#22c55e', t: 'Strong' },
      { w: '100%', c: '#16a34a', t: 'Very strong ✓' },
    ]
    setPwStrength(levels[Math.min(score, 5)])
  }

  function set(k) { return e => { setForm(f => ({ ...f, [k]: e.target.value })); if (k === 'password') checkStrength(e.target.value) } }

  async function handleSubmit(e) {
    e.preventDefault()
    const errs = []
    if (!form.first_name) errs.push('First name is required.')
    if (!form.last_name) errs.push('Last name is required.')
    if (!form.email || !/\S+@\S+\.\S+/.test(form.email)) errs.push('A valid email address is required.')
    if (form.password.length < 8) errs.push('Password must be at least 8 characters.')
    if (form.password !== form.confirm) errs.push('Passwords do not match.')
    if (!form.barangay) errs.push('Barangay is required.')
    if (!agree) errs.push('You must agree to the terms to continue.')
    if (errs.length) { setErrors(errs); return }
    setErrors([]); setLoading(true)
    try {
      const res = await fetch('/api/users/register', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ first_name: form.first_name, last_name: form.last_name, email: form.email, password: form.password, barangay: form.barangay }),
      })
      const data = await res.json()
      if (res.ok) {
        login(data.access_token)
        navigate('/profile?welcome=1')
      } else {
        setErrors([data.detail || 'Registration failed.'])
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
          <div className="auth-orb auth-orb1" style={{ background: 'var(--teal-700)', top: '-200px', left: '-150px', width: '650px', height: '650px' }}></div>
          <div className="auth-orb auth-orb2" style={{ background: 'var(--green-600)', bottom: '-100px', right: '-100px', width: '500px', height: '500px' }}></div>
          <div className="auth-orb auth-orb3" style={{ background: 'var(--earth-400)', top: '55%', left: '55%', width: '230px', height: '230px' }}></div>
          <div className="grid-lines"></div>
        </div>
        <div className="left-content">
          <Link to="/" className="nav-back">← Back to BottleBack</Link>
          <div className="auth-logo"><span className="auth-logo-icon">🌱</span><span className="auth-logo-text">Bottle<strong>Back</strong></span></div>
          <h1 className="auth-h1">Join the<br /><em>Movement.</em></h1>
          <p className="hero-sub">Create your free resident account and start earning rewards every time you recycle a plastic bottle at Barangay Muzon's BottleBack machine.</p>
          <div className="reg-steps">
            <div className="reg-step"><div className="reg-step-num">1</div><span>Fill in your details below — takes 60 seconds.</span></div>
            <div className="reg-step"><div className="reg-step-num">2</div><span>Deposit plastic bottles at the BottleBack machine.</span></div>
            <div className="reg-step"><div className="reg-step-num">3</div><span>Earn free drinks &amp; biscuits, track your impact.</span></div>
          </div>
          <div className="left-footer">
            <p>BottleBack · Our Lady of Fatima University<br />BSIT Capstone 2027 · Barangay Muzon, Taytay, Rizal<br />Aligned with RA 11898 — EPR Act 2022</p>
          </div>
        </div>
      </div>

      {/* RIGHT */}
      <div className="right-panel">
        <div className="form-wrap">
          <div className="form-eyebrow">Resident Registration</div>
          <div className="form-title">Create Your <em>Account</em></div>
          <p className="form-sub">Already have an account? <Link to="/login">Sign in →</Link></p>

          {errors.length > 0 && (
            <div className="alert-auth alert-error">
              Please fix the following:<ul>{errors.map(e => <li key={e}>{e}</li>)}</ul>
            </div>
          )}

          <form onSubmit={handleSubmit} noValidate>
            <div className="auth-form-row">
              <div className="field">
                <label className="auth-label" htmlFor="first_name">First Name</label>
                <input className="auth-input" type="text" id="first_name" placeholder="e.g. Maria" value={form.first_name} onChange={set('first_name')} autoComplete="given-name" required />
              </div>
              <div className="field">
                <label className="auth-label" htmlFor="last_name">Last Name</label>
                <input className="auth-input" type="text" id="last_name" placeholder="e.g. Santos" value={form.last_name} onChange={set('last_name')} autoComplete="family-name" required />
              </div>
            </div>
            <div className="field">
              <label className="auth-label" htmlFor="email">Email Address</label>
              <input className="auth-input" type="email" id="email" placeholder="you@example.com" value={form.email} onChange={set('email')} autoComplete="email" required />
            </div>
            <div className="field">
              <label className="auth-label" htmlFor="barangay">Barangay</label>
              <select className="auth-select" id="barangay" value={form.barangay} onChange={set('barangay')}>
                {BARANGAYS.map(b => <option key={b} value={b}>{b}</option>)}
              </select>
            </div>
            <div className="field">
              <label className="auth-label" htmlFor="password">Password <span style={{ textTransform: 'none', letterSpacing: 0, fontWeight: 400 }}>(min 8 characters)</span></label>
              <div className="input-wrap">
                <input className="auth-input" type={showPw ? 'text' : 'password'} id="password" placeholder="Create a strong password" value={form.password} onChange={set('password')} autoComplete="new-password" required />
                <button type="button" className="toggle-pw" onClick={() => setShowPw(v => !v)}>{showPw ? '🙈' : '👁'}</button>
              </div>
              <div className="pw-strength">
                <div className="pw-bar"><div className="pw-fill" style={{ width: pwStrength.w, background: pwStrength.c }}></div></div>
                <span className="pw-label" style={{ color: pwStrength.c }}>{pwStrength.t}</span>
              </div>
            </div>
            <div className="field">
              <label className="auth-label" htmlFor="confirm">Confirm Password</label>
              <div className="input-wrap">
                <input className="auth-input" type={showConfirm ? 'text' : 'password'} id="confirm" placeholder="Repeat your password" value={form.confirm} onChange={set('confirm')} autoComplete="new-password" required />
                <button type="button" className="toggle-pw" onClick={() => setShowConfirm(v => !v)}>{showConfirm ? '🙈' : '👁'}</button>
              </div>
            </div>
            <label className="check-wrap">
              <input type="checkbox" checked={agree} onChange={e => setAgree(e.target.checked)} />
              <span>I agree to the <Link to="/about" target="_blank">Terms of Use</Link> and confirm I am a resident of Taytay, Rizal participating in the BottleBack recycling program.</span>
            </label>
            <button type="submit" className="btn-submit" disabled={loading}>{loading ? 'Creating account…' : 'Create My Account 🌱'}</button>
          </form>

          <div className="auth-divider">already registered?</div>
          <Link to="/login" className="create-link">Sign in to your <strong>existing account</strong></Link>
          <div className="form-footer"><Link to="/">← Return to public site</Link></div>
        </div>
      </div>
    </div>
  )
}
