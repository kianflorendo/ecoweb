import { useEffect, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { useAuth } from '../../hooks/useAuth'
import Nav from '../../components/Nav'
import Footer from '../../components/Footer'

const BARANGAYS = ['Muzon','Dolores','San Juan','Sta. Ana','San Isidro','Pagala','Kalinawan','Manga','Sampaloc','Sta. Cruz']

export default function EditProfile() {
  const { user, loading, token } = useAuth()
  const navigate = useNavigate()
  const [form, setForm] = useState({ first_name: '', last_name: '', barangay: 'Muzon', password: '', confirm: '' })
  const [errors, setErrors] = useState([])
  const [success, setSuccess] = useState(false)
  const [submitting, setSubmitting] = useState(false)

  useEffect(() => {
    document.title = 'Edit Profile — BottleBack'
    if (!loading && !user) navigate('/login')
    if (user) setForm(f => ({ ...f, first_name: user.first_name, last_name: user.last_name, barangay: user.barangay }))
  }, [user, loading, navigate])

  function set(k) { return e => setForm(f => ({ ...f, [k]: e.target.value })) }

  async function handleSubmit(e) {
    e.preventDefault()
    const errs = []
    if (!form.first_name.trim()) errs.push('First name is required.')
    if (!form.last_name.trim()) errs.push('Last name is required.')
    if (form.password && form.password.length < 8) errs.push('Password must be at least 8 characters.')
    if (form.password && form.password !== form.confirm) errs.push('Passwords do not match.')
    if (errs.length) { setErrors(errs); return }
    setErrors([]); setSubmitting(true)
    const payload = { first_name: form.first_name, last_name: form.last_name, barangay: form.barangay }
    if (form.password) payload.password = form.password
    try {
      const res = await fetch('/api/users/me', {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` },
        body: JSON.stringify(payload),
      })
      if (res.ok) {
        setSuccess(true)
        setTimeout(() => navigate('/profile'), 1500)
      } else {
        const d = await res.json()
        setErrors([d.detail || 'Update failed.'])
      }
    } catch {
      setErrors(['Network error.'])
    }
    setSubmitting(false)
  }

  if (loading || !user) return null

  return (
    <>
      <Nav />
      <section className="page-hero">
        <div className="container">
          <h1 className="page-hero__title">Edit <em>Profile</em></h1>
          <p className="page-hero__sub">Update your name, barangay, or change your password.</p>
        </div>
      </section>
      <section className="section section--light" style={{ paddingTop: '2.5rem' }}>
        <div className="container" style={{ maxWidth: '640px' }}>
          {success && <div style={{ background: 'var(--green-100)', border: '1px solid var(--green-300)', borderRadius: '10px', padding: '1rem 1.2rem', marginBottom: '1.4rem', color: 'var(--green-700)', fontSize: '.9rem' }}>✅ Profile updated! Redirecting…</div>}
          {errors.length > 0 && <div className="form-errors"><ul>{errors.map(e => <li key={e}>{e}</li>)}</ul></div>}

          <div className="card">
            <div className="card-header"><span className="card-title">✏️ Edit Account Details</span></div>
            <div className="card-body">
              <form onSubmit={handleSubmit} style={{ display: 'flex', flexDirection: 'column', gap: '1.2rem' }}>
                <div className="form-row">
                  <div className="form-group">
                    <label className="form-group label" style={{ fontSize: '.83rem', fontWeight: 600, color: 'var(--ink)', marginBottom: '.4rem', display: 'block' }}>First Name *</label>
                    <input style={{ fontFamily: 'var(--font-sans)', fontSize: '.93rem', padding: '.82rem 1rem', border: '1.5px solid var(--border)', borderRadius: 'var(--r-sm)', background: 'var(--white)', color: 'var(--ink)', outline: 'none', width: '100%' }} type="text" value={form.first_name} onChange={set('first_name')} required />
                  </div>
                  <div className="form-group">
                    <label style={{ fontSize: '.83rem', fontWeight: 600, color: 'var(--ink)', marginBottom: '.4rem', display: 'block' }}>Last Name *</label>
                    <input style={{ fontFamily: 'var(--font-sans)', fontSize: '.93rem', padding: '.82rem 1rem', border: '1.5px solid var(--border)', borderRadius: 'var(--r-sm)', background: 'var(--white)', color: 'var(--ink)', outline: 'none', width: '100%' }} type="text" value={form.last_name} onChange={set('last_name')} required />
                  </div>
                </div>
                <div className="form-group">
                  <label style={{ fontSize: '.83rem', fontWeight: 600, color: 'var(--ink)', marginBottom: '.4rem', display: 'block' }}>Barangay</label>
                  <select style={{ fontFamily: 'var(--font-sans)', fontSize: '.93rem', padding: '.82rem 1rem', border: '1.5px solid var(--border)', borderRadius: 'var(--r-sm)', background: 'var(--white)', color: 'var(--ink)', outline: 'none', width: '100%', appearance: 'none' }} value={form.barangay} onChange={set('barangay')}>
                    {BARANGAYS.map(b => <option key={b} value={b}>{b}</option>)}
                  </select>
                </div>
                <hr style={{ border: 'none', borderTop: '1px solid var(--border)' }} />
                <p style={{ fontSize: '.83rem', color: 'var(--muted)' }}>Leave password blank to keep your current password.</p>
                <div className="form-row">
                  <div className="form-group">
                    <label style={{ fontSize: '.83rem', fontWeight: 600, color: 'var(--ink)', marginBottom: '.4rem', display: 'block' }}>New Password</label>
                    <input style={{ fontFamily: 'var(--font-sans)', fontSize: '.93rem', padding: '.82rem 1rem', border: '1.5px solid var(--border)', borderRadius: 'var(--r-sm)', background: 'var(--white)', color: 'var(--ink)', outline: 'none', width: '100%' }} type="password" value={form.password} onChange={set('password')} placeholder="New password (optional)" />
                  </div>
                  <div className="form-group">
                    <label style={{ fontSize: '.83rem', fontWeight: 600, color: 'var(--ink)', marginBottom: '.4rem', display: 'block' }}>Confirm Password</label>
                    <input style={{ fontFamily: 'var(--font-sans)', fontSize: '.93rem', padding: '.82rem 1rem', border: '1.5px solid var(--border)', borderRadius: 'var(--r-sm)', background: 'var(--white)', color: 'var(--ink)', outline: 'none', width: '100%' }} type="password" value={form.confirm} onChange={set('confirm')} placeholder="Repeat new password" />
                  </div>
                </div>
                <div style={{ display: 'flex', gap: '.8rem', flexWrap: 'wrap' }}>
                  <button type="submit" className="btn btn--primary" disabled={submitting}>{submitting ? 'Saving…' : 'Save Changes'}</button>
                  <Link to="/profile" className="btn btn--outline">Cancel</Link>
                </div>
              </form>
            </div>
          </div>
        </div>
      </section>
      <Footer />
    </>
  )
}
