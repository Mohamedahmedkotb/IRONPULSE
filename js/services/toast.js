let host;

function ensureHost() {
  if (host) return host;
  host = document.createElement("div");
  host.className = "ft-toast-host";
  host.setAttribute("aria-live", "polite");
  document.body.appendChild(host);
  return host;
}

/**
 * @param {"success"|"error"|"info"} type
 * @param {string} message
 * @param {number} [duration]
 */
export function showToast(type, message, duration = 4200) {
  const h = ensureHost();
  const el = document.createElement("div");
  el.className = `ft-toast ft-toast--${type}`;
  el.setAttribute("role", "status");
  el.innerHTML = `<span>${escapeHtml(message)}</span>`;
  const close = document.createElement("button");
  close.type = "button";
  close.className = "ft-btn ft-btn--ghost ft-btn--sm";
  close.style.marginLeft = "auto";
  close.setAttribute("aria-label", "Dismiss");
  close.textContent = "×";
  close.addEventListener("click", () => el.remove());
  el.appendChild(close);
  h.appendChild(el);
  const t = window.setTimeout(() => {
    el.style.opacity = "0";
    el.style.transform = "translateY(-6px)";
    el.style.transition = "opacity 0.25s, transform 0.25s";
    window.setTimeout(() => el.remove(), 250);
  }, duration);
  close.addEventListener("click", () => window.clearTimeout(t));
}

function escapeHtml(s) {
  const d = document.createElement("div");
  d.textContent = s;
  return d.innerHTML;
}
