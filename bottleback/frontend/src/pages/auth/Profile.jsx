import { useEffect, useState } from 'react'
import { Link, useNavigate, useSearchParams } from 'react-router-dom'
import { useAuth } from '../../hooks/useAuth'
import Nav from '../../components/Nav'
import Footer from '../../components/Footer'

export default function Profile() {
  const { user, loading, logout, token } = useAuth()
  const navigate = useNavigate()
  const [params] = useSearchParams()
  const [community, setCommunity] = useState(null)

  useEffect(() => {
    document.title = 'My Profile — BottleBack'
    if (!loading && !user) navigate('/login')
  }, [user, loading, navigate])

  useEffect(() => {
    if (!token) return
    fetch('/api/users/me/community', { headers: { Authorization: `Bearer ${token}` } })
      .then(r => r.ok ? r.json() : null)
      .then(setCommunity)
  }, [token])

  if (loading || !user) return null

  const initial = user.first_name?.[0]?.toUpperCase() || 'R'
  const welcome = params.get('welcome')
  const machine = community?.machine

  function handleLogout() { logout(); navigate('/') }

  return (
    <>
      <Nav />
      <section className="profile-hero">
        <div className="container">
          <div className="profile-identity">
            <div className="profile-avatar">{initial}</div>
            <div>
              <div className="profile-name">{user.first_name} <em>{user.last_name}</em></div>
              <div className="profile-meta">
                <span>✉️ {user.email}</span>
                <span>📍 Brgy. {user.barangay}</span>
                <span>📅 Joined {new Date(user.created_at).toLocaleDateString('en-PH', { month: 'short', year: 'numeric', timeZone: 'Asia/Manila' })}</span>
              </div>
            </div>
          </div>
          <div className="profile-actions">
            <Link to="/edit-profile" className="btn btn--ghost btn--sm">✏️ Edit Profile</Link>
            <button onClick={handleLogout} className="btn btn--outline btn--sm" style={{ borderColor: 'rgba(255,255,255,.3)', color: 'rgba(255,255,255,.7)' }}>Sign Out</button>
          </div>
        </div>
      </section>

      <section className="section section--light" style={{ paddingTop: '2.5rem', paddingBottom: '3rem' }}>
        <div className="container">
          {welcome && (
            <div className="welcome-banner">
              <div className="welcome-banner__icon">🎉</div>
              <div>
                <h3>Welcome to BottleBack, {user.first_name}!</h3>
                <p>Your account is ready. Start recycling at the Barangay Muzon machine to earn your first reward.</p>
              </div>
            </div>
          )}

          <div className="stats-row">
            <div className="stat-box"><div className="stat-box__icon">♻️</div><div className="stat-box__val stat-box__val--green">{user.total_bottles.toLocaleString()}</div><div className="stat-box__label">My Bottles</div></div>
            <div className="stat-box"><div className="stat-box__icon">🎁</div><div className="stat-box__val stat-box__val--teal">{user.total_rewards.toLocaleString()}</div><div className="stat-box__label">My Rewards</div></div>
            <div className="stat-box"><div className="stat-box__icon">🏘️</div><div className="stat-box__val">{community ? community.community_bottles.toLocaleString() : '—'}</div><div className="stat-box__label">Community Total</div></div>
            <div className="stat-box">
              <div className="stat-box__icon">{machine?.is_online ? '🟢' : '🔴'}</div>
              <div className="stat-box__val" style={{ fontSize: '1.3rem', paddingTop: '.3rem' }}>{machine?.is_online ? 'Online' : 'Offline'}</div>
              <div className="stat-box__label">Machine Status</div>
            </div>
          </div>

          <div className="profile-grid">
            <div style={{ display: 'flex', flexDirection: 'column', gap: '1.4rem' }}>
              <div className="card">
                <div className="card-header">
                  <span className="card-title">📋 Account Details</span>
                  <Link to="/edit-profile" className="btn btn--outline btn--sm">Edit</Link>
                </div>
                <div className="card-body">
                  {[
                    ['Full Name', `${user.first_name} ${user.last_name}`],
                    ['Email', user.email],
                    ['Barangay', user.barangay],
                    ['Joined', new Date(user.created_at).toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric', timeZone: 'Asia/Manila' })],
                    ['Last Login', user.last_login ? new Date(user.last_login).toLocaleString('en-PH', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Manila' }) : 'First login'],
                  ].map(([k, v]) => (
                    <div className="info-row" key={k}><span className="key">{k}</span><span className="val">{v}</span></div>
                  ))}
                </div>
              </div>

              {machine && (
                <div className="machine-status-card">
                  <div className="ms-header">
                    <span style={{ display: 'flex', alignItems: 'center', gap: '.5rem' }}>
                      <span className="ms-dot"></span>node_001 — Barangay Muzon
                    </span>
                    <span style={{ color: 'var(--green-300)', fontWeight: 600 }}>{machine.is_online ? 'ONLINE' : 'OFFLINE'}</span>
                  </div>
                  <div className="ms-stat"><span>🗑️ Bin Fill</span><strong>{machine.bin_level}%</strong></div>
                  <div className="ms-stat"><span>♻️ Community Bottles</span><strong>{community?.community_bottles?.toLocaleString()}</strong></div>
                  <div className="ms-stat"><span>🤖 Arduino</span><strong>{machine.is_online ? 'Connected' : 'Awaiting'}</strong></div>
                  <div style={{ marginTop: '.9rem', background: 'rgba(255,255,255,.06)', borderRadius: 'var(--r-full)', height: '8px', overflow: 'hidden' }}>
                    <div style={{ height: '100%', width: `${machine.bin_level}%`, background: machine.bin_level >= 90 ? '#f87171' : machine.bin_level >= 70 ? '#fbbf24' : 'var(--green-400)', borderRadius: 'var(--r-full)', transition: 'width 1s' }}></div>
                  </div>
                  <p style={{ fontSize: '.76rem', color: 'rgba(255,255,255,.35)', marginTop: '.5rem', textAlign: 'center' }}>
                    {machine.bin_level >= 90 ? '⚠️ Bin full — machine may not accept bottles' : machine.bin_level >= 70 ? '⚠️ Bin filling up soon' : '✅ Machine ready to accept bottles'}
                  </p>
                </div>
              )}
            </div>

            <div className="card">
              <div className="card-header">
                <span className="card-title">🏘️ Community Activity</span>
                <Link to="/data" className="btn btn--outline btn--sm">View Dashboard →</Link>
              </div>
              <div className="card-body">
                {community?.recent_transactions?.length ? (
                  <>
                    <p style={{ fontSize: '.8rem', color: 'var(--muted)', marginBottom: '1rem' }}>Most recent transactions across all users at the machine:</p>
                    {community.recent_transactions.map(tx => (
                      <div className="tx-row" key={tx.id}>
                        <div>
                          <span className={`badge-sm badge-sm--${tx.status === 'Accepted' ? 'green' : 'red'}`}>{tx.status}</span>
                          <span style={{ marginLeft: '.5rem', color: 'var(--text)' }}>{tx.bottle_count} bottle{tx.bottle_count !== 1 ? 's' : ''}</span>
                          {tx.status === 'Accepted' && <span style={{ color: 'var(--muted)', fontSize: '.82rem' }}> → 🎁 {tx.reward_amount} reward</span>}
                        </div>
                        <span className="tx-time">{new Date(tx.created_at).toLocaleDateString('en-PH', { month: 'short', day: 'numeric', timeZone: 'Asia/Manila' })} {new Date(tx.created_at).toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Manila' })}</span>
                      </div>
                    ))}
                  </>
                ) : (
                  <div style={{ textAlign: 'center', padding: '2.5rem 1rem' }}>
                    <div style={{ fontSize: '2rem', marginBottom: '.7rem' }}>♻️</div>
                    <h4 style={{ color: 'var(--ink)', marginBottom: '.4rem' }}>No activity yet</h4>
                    <p style={{ fontSize: '.88rem', maxWidth: '300px', margin: '0 auto' }}>Machine transactions will appear here once the Arduino is connected.</p>
                  </div>
                )}
              </div>
            </div>
          </div>

          {/* HOW TO EARN */}
          <div className="card" style={{ borderColor: 'var(--green-200)', background: 'var(--green-50)' }}>
            <div className="card-header" style={{ background: 'var(--green-100)', borderColor: 'var(--green-200)' }}>
              <span className="card-title" style={{ color: 'var(--green-800)' }}>🌱 How to Earn Rewards</span>
            </div>
            <div className="card-body">
              <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit,minmax(180px,1fr))', gap: '1.2rem' }}>
                {[
                  ['♻️', 'Step 1: Get Your Bottles', 'Bring your used plastic PET bottles to the BottleBack machine in Barangay Muzon.'],
                  ['🔍', 'Step 2: Insert & Validate', 'Insert bottles into the input slot one at a time. IR and ultrasonic sensors check each one.'],
                  ['🎁', 'Step 3: Collect Your Reward', 'Each accepted bottle earns you a free drink or biscuit, dispensed right from the machine.'],
                  ['🌿', 'Step 4: Make an Impact', 'Your contribution helps reduce plastic waste and keeps Barangay Muzon clean.'],
                ].map(([icon, title, desc]) => (
                  <div key={title} style={{ textAlign: 'center', padding: '.8rem' }}>
                    <div style={{ fontSize: '2rem', marginBottom: '.5rem' }}>{icon}</div>
                    <strong style={{ display: 'block', color: 'var(--green-800)', fontSize: '.88rem', marginBottom: '.3rem' }}>{title}</strong>
                    <p style={{ fontSize: '.82rem', color: 'var(--muted)', lineHeight: 1.55 }}>{desc}</p>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      </section>
      <Footer />
    </>
  )
}
