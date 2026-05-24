import { BrowserRouter, Routes, Route } from 'react-router-dom'
import Home from './pages/Home'
import About from './pages/About'
import HowItWorks from './pages/HowItWorks'
import Data from './pages/Data'
import Awareness from './pages/Awareness'
import Contact from './pages/Contact'
import Login from './pages/auth/Login'
import Register from './pages/auth/Register'
import Profile from './pages/auth/Profile'
import EditProfile from './pages/auth/EditProfile'
import AdminLogin from './pages/admin/AdminLogin'
import Dashboard from './pages/admin/Dashboard'
import Transactions from './pages/admin/Transactions'
import Users from './pages/admin/Users'
import Machine from './pages/admin/Machine'
import Messages from './pages/admin/Messages'
import Settings from './pages/admin/Settings'
import Export from './pages/admin/Export'

export default function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/about" element={<About />} />
        <Route path="/how-it-works" element={<HowItWorks />} />
        <Route path="/data" element={<Data />} />
        <Route path="/awareness" element={<Awareness />} />
        <Route path="/contact" element={<Contact />} />
        <Route path="/login" element={<Login />} />
        <Route path="/register" element={<Register />} />
        <Route path="/profile" element={<Profile />} />
        <Route path="/edit-profile" element={<EditProfile />} />
        <Route path="/admin/login" element={<AdminLogin />} />
        <Route path="/admin" element={<Dashboard />} />
        <Route path="/admin/transactions" element={<Transactions />} />
        <Route path="/admin/users" element={<Users />} />
        <Route path="/admin/machine" element={<Machine />} />
        <Route path="/admin/messages" element={<Messages />} />
        <Route path="/admin/settings" element={<Settings />} />
        <Route path="/admin/export" element={<Export />} />
      </Routes>
    </BrowserRouter>
  )
}
