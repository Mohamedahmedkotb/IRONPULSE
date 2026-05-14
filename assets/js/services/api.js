import { apiUrl } from "../utils/paths.js";
import { getSession, setSession, clearSession } from "../utils/storage.js";

/**
 * @param {string} subpath e.g. "auth/login.php"
 * @param {RequestInit & { json?: unknown }} options
 * @returns {Promise<any>} parsed `data` from API envelope
 */
export async function apiFetch(subpath, options = {}) {
  const { json: requestJson, ...rest } = options;
  const method = (rest.method || "GET").toUpperCase();
  const headers = new Headers(rest.headers || {});
  let body = rest.body;
  if (requestJson !== undefined) {
    body = JSON.stringify(requestJson);
    if (!headers.has("Content-Type")) {
      headers.set("Content-Type", "application/json");
    }
  }
  const csrf = getSession()?.csrf_token;
  if (csrf && ["POST", "PUT", "DELETE"].includes(method)) {
    headers.set("X-CSRF-Token", csrf);
  }
  const res = await fetch(apiUrl(subpath), {
    credentials: "include",
    ...rest,
    method,
    headers,
    body,
  });
  const text = await res.text();
  let payload = {};
  try {
    payload = text ? JSON.parse(text) : {};
  } catch {
    const err = new Error(text.slice(0, 200) || "Invalid response");
    err.status = res.status;
    throw err;
  }
  if (!res.ok || payload.success === false) {
    const err = new Error(payload.message || res.statusText || "Request failed");
    err.status = res.status;
    err.payload = payload;
    throw err;
  }
  return payload.data;
}

/** Refresh local session mirror from GET auth/me.php */
export async function refreshSessionFromMe() {
  const data = await apiFetch("auth/me.php");
  const u = data.user;
  if (!u?.email) {
    return null;
  }
  setSession({
    name: u.full_name || u.name || "Athlete",
    email: u.email,
    userId: u.id,
    csrf_token: data.csrf_token || "",
  });
  return data;
}

export async function logoutAll() {
  try {
    await apiFetch("auth/logout.php", {
      method: "POST",
      json: {},
    });
  } catch {
    /* session may already be gone */
  }
  clearSession();
}
