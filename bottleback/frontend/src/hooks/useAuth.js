import { useState, useEffect } from 'react'

export function useAuth() {
  const [user, setUser] = useState(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const token = localStorage.getItem('bb_user_token')
    if (!token) { setLoading(false); return }
    fetch('/api/users/me', {
      headers: { Authorization: `Bearer ${token}` },
    })
      .then(r => r.ok ? r.json() : null)
      .then(data => { setUser(data); setLoading(false) })
      .catch(() => setLoading(false))
  }, [])

  function login(token) {
    localStorage.setItem('bb_user_token', token)
    fetch('/api/users/me', {
      headers: { Authorization: `Bearer ${token}` },
    })
      .then(r => r.ok ? r.json() : null)
      .then(setUser)
  }

  function logout() {
    localStorage.removeItem('bb_user_token')
    setUser(null)
  }

  return { user, loading, login, logout, token: localStorage.getItem('bb_user_token') }
}

export function useAdminAuth() {
  const [isAdmin, setIsAdmin] = useState(!!localStorage.getItem('bb_admin_token'))

  function adminLogin(token) {
    localStorage.setItem('bb_admin_token', token)
    setIsAdmin(true)
  }

  function adminLogout() {
    localStorage.removeItem('bb_admin_token')
    setIsAdmin(false)
  }

  return { isAdmin, adminLogin, adminLogout, token: localStorage.getItem('bb_admin_token') }
}
