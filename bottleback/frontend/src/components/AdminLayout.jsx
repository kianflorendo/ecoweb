import { useState, useEffect } from 'react'
import { Link, useLocation, useNavigate } from 'react-router-dom'
import { useAdminAuth } from '../hooks/useAuth'
import '../assets/css/admin.css'

const navItems = [
  { to: '/admin', label: 'Dashboard', icon: '📊' },
  { to: '/admin/transactions', label: 'Transactions', icon: '🔄' },
  { to: '/admin/machine', label: 'Machine Status', icon: '🤖' },
  { to: '/admin/users', label: 'Users', icon: '👥' },
  { to: '/admin/messages', label: 'Messages', icon: '✉️' },
  { to: '/admin/settings', label: 'Settings', icon: '⚙️' },
]

const titles = {
  '/admin': 'Dashboard',
  '/admin/transactions': 'Transactions',
  '/admin/machine': 'Machine Status',
  '/admin/users': 'Users',
  '/admin/messages': 'Contact Messages',
  '/admin/settings': 'Settings',
  '/admin/export': 'Export Data',
}

export default function AdminLayout({ children }) {
  const { isAdmin, adminLogout } = useAdminAuth()
  const location = useLocation()
  const navigate = useNavigate()
  const [sidebarOpen, setSidebarOpen] = useState(false)

  useEffect(() => {
    if (!isAdmin) navigate('/admin/login')
  }, [isAdmin, navigate])

  function handleLogout() {
    adminLogout()
    navigate('/admin/login')
  }

  if (!isAdmin) return null

  const currentTitle = titles[location.pathname] || 'Admin'

  return (
    <div className="admin-layout">
      <div
        className={`sidebar-overlay${sidebarOpen ? ' open' : ''}`}
        id="overlay"
        onClick={() => setSidebarOpen(false)}
      />
      <aside className={`sidebar${sidebarOpen ? ' open' : ''}`} id="sidebar">
        <div className="sidebar__logo">
          <span>🌱</span>
          <span className="sidebar__logo-text">Bottle<strong>Back</strong></span>
          <button className="sidebar__close" onClick={() => setSidebarOpen(false)}>✕</button>
        </div>
        <div className="sidebar__badge">Admin Panel</div>
        <nav className="sidebar__nav">
          {navItems.map(item => (
            <Link
              key={item.to}
              to={item.to}
              className={`sidebar__link${location.pathname === item.to ? ' sidebar__link--active' : ''}`}
              onClick={() => setSidebarOpen(false)}
            >
              <span className="sidebar__icon">{item.icon}</span>
              {item.label}
            </Link>
          ))}
        </nav>
        <div className="sidebar__footer">
          <Link to="/" target="_blank" className="sidebar__link">
            <span className="sidebar__icon">🌐</span> Public Site ↗
          </Link>
          <button
            onClick={handleLogout}
            className="sidebar__link sidebar__link--danger"
            style={{ background: 'none', border: 'none', cursor: 'pointer', width: '100%', textAlign: 'left' }}
          >
            <span className="sidebar__icon">🚪</span> Log Out
          </button>
        </div>
      </aside>

      <header className="topbar">
        <button className="topbar__toggle" onClick={() => setSidebarOpen(o => !o)}>☰</button>
        <div className="topbar__title">{currentTitle}</div>
        <div className="topbar__right">
          <span className="topbar__user">👤 Admin</span>
          <button onClick={handleLogout} className="topbar__logout">Sign Out</button>
        </div>
      </header>

      <div className="main-area">
        <div className="main-content">
          {children}
        </div>
      </div>
    </div>
  )
}
