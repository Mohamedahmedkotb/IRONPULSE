/** Resolve URL relative to this module (works for /js/ location) */
export function componentUrl(filename) {
  return new URL(`../../components/${filename}`, import.meta.url);
}

/** Backend REST base; override with window.__IRONPULSE_API_BASE__ (absolute URL ending in /). */
export function apiUrl(subpath = "") {
  const clean = String(subpath || "").replace(/^\/+/, "");
  if (typeof window !== "undefined" && window.__IRONPULSE_API_BASE__) {
    const b = String(window.__IRONPULSE_API_BASE__).replace(/\/?$/, "/");
    return b + clean;
  }
  return new URL(`../../backend/api/${clean}`, import.meta.url).href;
}

export function isInHtmlFolder() {
  const p = window.location.pathname.replace(/\\/g, "/");
  return p.includes("/html/");
}

/** Prefix for static assets from current page */
export function assetPrefix() {
  return isInHtmlFolder() ? "../" : "";
}
