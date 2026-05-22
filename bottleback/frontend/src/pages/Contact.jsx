import { useEffect, useState } from 'react'
import Nav from '../components/Nav'
import Footer from '../components/Footer'

export default function Contact() {
  useEffect(() => { document.title = 'Contact | BottleBack' }, [])
  const [form, setForm] = useState({ name: '', email: '', subject: '', message: '' })
  const [errors, setErrors] = useState([])
  const [success, setSuccess] = useState(false)
  const [loading, setLoading] = useState(false)

  function validate() {
    const e = []
    if (!form.name.trim()) e.push('Name is required.')
    if (!form.email.trim()) e.push('Email is required.')
    else if (!/\S+@\S+\.\S+/.test(form.email)) e.push('Invalid email address.')
    if (!form.subject) e.push('Subject is required.')
    if (!form.message.trim()) e.push('Message is required.')
    return e
  }

  async function handleSubmit(e) {
    e.preventDefault()
    const errs = validate()
    if (errs.length) { setErrors(errs); return }
    setErrors([]); setLoading(true)
    try {
      const res = await fetch('/api/contact', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(form),
      })
      if (res.ok) {
        setSuccess(true)
        setForm({ name: '', email: '', subject: '', message: '' })
      } else {
        setErrors(['Failed to send message. Please try again.'])
      }
    } catch {
      setErrors(['Network error. Please try again.'])
    }
    setLoading(false)
  }

  return (
    <>
      <Nav />
      <section className="page-hero">
        <div className="container">
          <div className="section-label section-label--light">Get in Touch</div>
          <h1 className="page-hero__title">Contact <em>Us</em></h1>
          <p className="page-hero__sub">Questions about the BottleBack project? Interested in learning more or collaborating? Send us a message.</p>
        </div>
      </section>

      <section className="section">
        <div className="container">
          <div className="contact-grid">
            <div className="contact-info">
              <h3>About this <em>project</em></h3>
              <p>BottleBack is a BSIT Capstone Project from the College of Computer Studies, Our Lady of Fatima University, Antipolo City, aimed at addressing plastic bottle waste in Barangay Muzon, Taytay, Rizal.</p>
              <div className="contact-cards">
                {[
                  { h: 'Students & Researchers', p: 'Curious about our methodology, hardware design, or sensor setup? We\'re happy to share insights for your own research.' },
                  { h: 'Barangay Muzon Residents', p: 'Want to know where the machine will be placed or how to use it? Reach out and we\'ll keep you updated.' },
                  { h: 'Barangay Officials & LGUs', p: 'Interested in integrating this machine into your waste management program or partnering with us?' },
                  { h: 'Media & Advocates', p: 'Covering environmental technology or community innovation? We\'d love to share our story.' },
                ].map(c => (
                  <div className="contact-card" key={c.h}>
                    <span></span>
                    <div><strong>{c.h}</strong><p>{c.p}</p></div>
                  </div>
                ))}
              </div>
            </div>

            <div className="contact-form-wrap">
              {success ? (
                <div className="form-success">
                  <div className="form-success__icon"></div>
                  <h3>Message sent!</h3>
                  <p>Thank you for reaching out to the BottleBack research team. We'll get back to you as soon as possible.</p>
                  <button onClick={() => setSuccess(false)} className="btn btn--outline">Send another message</button>
                </div>
              ) : (
                <>
                  {errors.length > 0 && (
                    <div className="form-errors">
                      <ul>{errors.map(e => <li key={e}>{e}</li>)}</ul>
                    </div>
                  )}
                  <form className="eco-form" onSubmit={handleSubmit}>
                    <div className="form-row">
                      <div className="form-group">
                        <label htmlFor="name">Your Name *</label>
                        <input type="text" id="name" placeholder="Juan dela Cruz" value={form.name} onChange={e => setForm(f => ({ ...f, name: e.target.value }))} required />
                      </div>
                      <div className="form-group">
                        <label htmlFor="email">Email Address *</label>
                        <input type="email" id="email" placeholder="juan@email.com" value={form.email} onChange={e => setForm(f => ({ ...f, email: e.target.value }))} required />
                      </div>
                    </div>
                    <div className="form-group">
                      <label htmlFor="subject">Subject *</label>
                      <select id="subject" value={form.subject} onChange={e => setForm(f => ({ ...f, subject: e.target.value }))} required>
                        <option value="" disabled>Choose a topic...</option>
                        <option value="general">General Inquiry</option>
                        <option value="hardware">Arduino / Hardware Questions</option>
                        <option value="software">Software / Dashboard Questions</option>
                        <option value="research">Research Collaboration</option>
                        <option value="barangay">Barangay / Community Deployment</option>
                        <option value="media">Media / Press Inquiry</option>
                        <option value="feedback">Feedback / Suggestions</option>
                      </select>
                    </div>
                    <div className="form-group">
                      <label htmlFor="message">Message *</label>
                      <textarea id="message" rows={6} placeholder="Tell us more about your inquiry..." value={form.message} onChange={e => setForm(f => ({ ...f, message: e.target.value }))} required />
                    </div>
                    <button type="submit" className="btn btn--primary btn--full" disabled={loading}>
                      {loading ? 'Sending…' : 'Send Message'}
                    </button>
                  </form>
                </>
              )}
            </div>
          </div>
        </div>
      </section>
      <Footer />
    </>
  )
}
