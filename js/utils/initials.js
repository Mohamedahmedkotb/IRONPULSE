/** Two-letter initials from a display name (no photos). */
export function initialsFromName(name) {
  const s = String(name || "").trim();
  if (!s) return "??";
  const parts = s.split(/\s+/).filter(Boolean);
  if (parts.length >= 2) {
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
  }
  if (s.length >= 2) return s.slice(0, 2).toUpperCase();
  return (s[0] + s[0]).toUpperCase();
}
