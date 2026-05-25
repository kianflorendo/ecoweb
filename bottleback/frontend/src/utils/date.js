const PH = { timeZone: 'Asia/Manila' }

// PostgreSQL returns timestamps without 'Z', so JS treats them as local time.
// Appending 'Z' forces UTC interpretation before converting to Manila time.
function utcDate(s) {
  if (!s) return new Date(0)
  const str = String(s)
  return new Date(str.endsWith('Z') || str.includes('+') ? str : str + 'Z')
}

export function phDateTime(s, opts = {}) {
  return utcDate(s).toLocaleString('en-PH', { ...PH, ...opts })
}

export function phDateOnly(s, opts = {}) {
  return utcDate(s).toLocaleDateString('en-PH', { ...PH, ...opts })
}

export function phTimeOnly(s, opts = {}) {
  return utcDate(s).toLocaleTimeString('en-PH', { ...PH, ...opts })
}
