import { setSession } from "../utils/storage.js";
import { isValidEmail } from "../utils/validators.js";
import { showToast } from "../services/toast.js";
import { apiFetch } from "../services/api.js";

function qs(id) {
  return document.getElementById(id);
}

function init() {
  const params = new URLSearchParams(window.location.search);
  if (params.get("registered") === "1") {
    showToast("success", "Account created. You can log in now.");
    history.replaceState(null, "", window.location.pathname);
  }

  const form = qs("login-form");
  const email = qs("login-email");
  const pass = qs("login-password");
  const errE = qs("login-email-err");
  const errP = qs("login-pass-err");
  const submit = qs("login-submit");
  const toggle = qs("toggle-pass");

  toggle?.addEventListener("click", () => {
    if (!(pass instanceof HTMLInputElement)) return;
    const show = pass.type === "password";
    pass.type = show ? "text" : "password";
    toggle.innerHTML = show
      ? '<i class="fas fa-eye-slash"></i>'
      : '<i class="fas fa-eye"></i>';
    toggle.setAttribute("aria-label", show ? "Hide password" : "Show password");
  });

  qs("login-forgot")?.addEventListener("click", (e) => {
    e.preventDefault();
    showToast("info", "Password reset link would be sent to your email.");
  });

  qs("social-google")?.addEventListener("click", () =>
    showToast("info", "Google sign-in is a demo in this build."),
  );
  qs("social-apple")?.addEventListener("click", () =>
    showToast("info", "Apple sign-in is a demo in this build."),
  );

  form?.addEventListener("submit", async (e) => {
    if (form?.dataset.php === "1") {
      if (errE) errE.hidden = true;
      if (errP) errP.hidden = true;
      email?.classList.remove("is-invalid");
      pass?.classList.remove("is-invalid");

      const em = (email instanceof HTMLInputElement ? email.value : "").trim();
      const pw = pass instanceof HTMLInputElement ? pass.value : "";
      let ok = true;
      if (!isValidEmail(em)) {
        if (errE) {
          errE.textContent = "Enter a valid email address.";
          errE.hidden = false;
        }
        email?.classList.add("is-invalid");
        ok = false;
      }
      if (pw.length < 1) {
        if (errP) {
          errP.textContent = "Enter your password.";
          errP.hidden = false;
        }
        pass?.classList.add("is-invalid");
        ok = false;
      }
      if (!ok) {
        e.preventDefault();
        return;
      }
      submit?.classList.add("ft-btn--loading");
      if (submit) submit.disabled = true;
      return;
    }

    e.preventDefault();
    if (errE) errE.hidden = true;
    if (errP) errP.hidden = true;
    email?.classList.remove("is-invalid");
    pass?.classList.remove("is-invalid");

    const em = (email instanceof HTMLInputElement ? email.value : "").trim();
    const pw = pass instanceof HTMLInputElement ? pass.value : "";
    let ok = true;
    if (!isValidEmail(em)) {
      if (errE) {
        errE.textContent = "Enter a valid email address.";
        errE.hidden = false;
      }
      email?.classList.add("is-invalid");
      ok = false;
    }
    if (pw.length < 6) {
      if (errP) {
        errP.textContent = "Password must be at least 6 characters.";
        errP.hidden = false;
      }
      pass?.classList.add("is-invalid");
      ok = false;
    }
    if (!ok) return;

    submit.classList.add("ft-btn--loading");
    submit.disabled = true;
    try {
      const data = await apiFetch("auth/login.php", {
        method: "POST",
        json: { email: em, password: pw },
      });
      const u = data.user;
      setSession({
        email: u.email,
        name: u.full_name || u.name || "Athlete",
        userId: u.id,
        csrf_token: data.csrf_token || "",
      });
      showToast("success", "Welcome back!");
      window.location.href = "home.html";
    } catch (err) {
      if (errP) {
        errP.textContent = err.message || "Login failed.";
        errP.hidden = false;
      }
      pass?.classList.add("is-invalid");
      showToast("error", err.message || "Login failed.");
    } finally {
      submit.classList.remove("ft-btn--loading");
      submit.disabled = false;
    }
  });
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", init);
} else {
  init();
}
