import { useEffect, useRef, useState } from 'react'
import { Link, useLocation, useNavigate } from 'react-router-dom'
import { useAuth } from '../hooks/useAuth'

export default function Nav() {
  const { user, logout } = useAuth()
  const location = useLocation()
  const navigate = useNavigate()
  const navRef = useRef(null)
  const [menuOpen, setMenuOpen] = useState(false)
  const [dropOpen, setDropOpen] = useState(false)

  useEffect(() => {
    const el = navRef.current
    if (!el) return
    const handler = () => el.classList.toggle('scrolled', window.scrollY > 55)
    window.addEventListener('scroll', handler, { passive: true })
    handler()
    return () => window.removeEventListener('scroll', handler)
  }, [])

  useEffect(() => {
    setMenuOpen(false)
    setDropOpen(false)
  }, [location.pathname])

  const links = [
    { to: '/', label: 'Home' },
    { to: '/about', label: 'About' },
    { to: '/how-it-works', label: 'How It Works' },
    { to: '/data', label: 'Live Data' },
    { to: '/awareness', label: 'Awareness' },
    { to: '/contact', label: 'Contact' },
  ]

  function handleLogout() {
    logout()
    navigate('/')
  }

  const initial = user?.first_name ? user.first_name[0].toUpperCase() : ''

  return (
    <nav className="navbar" id="navbar" ref={navRef}>
      <div className="navbar__inner">
        <Link to="/" className="navbar__logo">
          <i className="fi fi-sr-save-the-planet" style={{ fontSize: 28, color: '#22c55e', display: 'flex', alignItems: 'center' }}></i>
          <span className="logo-text">Bottle<strong>Back</strong></span>
        </Link>

        <button
          className="navbar__toggle"
          id="navToggle"
          aria-label="Toggle navigation"
          aria-expanded={menuOpen}
          onClick={() => setMenuOpen(o => !o)}
        >
          <span style={menuOpen ? { transform: 'translateY(7px) rotate(45deg)' } : {}}></span>
          <span style={menuOpen ? { opacity: 0 } : {}}></span>
          <span style={menuOpen ? { transform: 'translateY(-7px) rotate(-45deg)' } : {}}></span>
        </button>

        <ul className={`navbar__links${menuOpen ? ' open' : ''}`} id="navLinks">
          {links.map(l => (
            <li key={l.to}>
              <Link
                to={l.to}
                className={`nav-link${location.pathname === l.to ? ' nav-link--active' : ''}`}
              >
                {l.label}
              </Link>
            </li>
          ))}

          {user ? (
            <li className="nav-user-wrap" style={{ position: 'relative' }}>
              <button
                className={`nav-user-btn${dropOpen ? ' open' : ''}`}
                id="userMenuBtn"
                onClick={() => setDropOpen(o => !o)}
                aria-label="User menu"
              >
                <span className="nav-avatar">{initial}</span>
                <span className="nav-username">{user.first_name}</span>
                <span className="nav-caret">▾</span>
              </button>
              <div className={`nav-dropdown${dropOpen ? ' open' : ''}`} id="userDropdown">
                <Link to="/profile" className="nav-dropdown__item">My Profile</Link>
                <Link to="/edit-profile" className="nav-dropdown__item">Edit Profile</Link>
                <div className="nav-dropdown__divider"></div>
                <button
                  onClick={handleLogout}
                  className="nav-dropdown__item nav-dropdown__item--danger"
                  style={{ background: 'none', border: 'none', cursor: 'pointer', width: '100%', textAlign: 'left' }}
                >
                  Sign Out
                </button>
              </div>
            </li>
          ) : (
            <>
              <li>
                <Link to="/login" className="nav-link" style={{ display: 'inline-flex', alignItems: 'center', gap: '.4rem' }}>
                  Sign In
                </Link>
              </li>
              <li>
                <Link to="/register" className="btn btn--primary btn--sm" style={{ fontSize: '.82rem', padding: '.5em 1.2em' }}>
                  Join Free
                </Link>
              </li>
            </>
          )}
        </ul>
      </div>
    </nav>
  )
}
