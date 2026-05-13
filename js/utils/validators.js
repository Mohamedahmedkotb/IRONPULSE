export function isValidEmail(s) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(s).trim());
}

export function passwordStrength(password) {
  let score = 0;
  const p = String(password);
  if (p.length >= 8) score++;
  if (p.length >= 12) score++;
  if (/[a-z]/.test(p) && /[A-Z]/.test(p)) score++;
  if (/\d/.test(p)) score++;
  if (/[^a-zA-Z0-9]/.test(p)) score++;
  if (score <= 1) return { score, label: "Weak", pct: 25 };
  if (score === 2) return { score, label: "Fair", pct: 45 };
  if (score === 3) return { score, label: "Good", pct: 65 };
  if (score === 4) return { score, label: "Strong", pct: 85 };
  return { score, label: "Excellent", pct: 100 };
}
