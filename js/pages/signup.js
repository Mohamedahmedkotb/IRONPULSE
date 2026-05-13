import { isValidEmail, passwordStrength } from "../utils/validators.js";
import { showToast } from "../services/toast.js";
import { apiFetch } from "../services/api.js";
import { setSession } from "../utils/storage.js";

let step = 1;
let selectedGoal = "Strength";

function showStep(n) {
  step = n;
  document.querySelectorAll(".signup-panel").forEach((p) => {
    const s = Number(p.getAttribute("data-step"));
    p.hidden = s !== n;
  });
  document.querySelectorAll("[data-step-indicator]").forEach((el, i) => {
    el.classList.toggle("is-active", i + 1 === n);
    el.classList.toggle("is-done", i + 1 < n);
  });
  const bar = document.querySelector(".signup-steps");
  if (bar) bar.setAttribute("aria-valuenow", String(n));
}

function init() {
  const pass = document.getElementById("su-pass");
  const toggle = document.getElementById("su-toggle-pass");
  const fill = document.getElementById("su-strength-fill");
  const label = document.getElementById("su-strength-label");

  toggle?.addEventListener("click", () => {
    if (!(pass instanceof HTMLInputElement)) return;
    const show = pass.type === "password";
    pass.type = show ? "text" : "password";
    toggle.innerHTML = show
      ? '<i class="fas fa-eye-slash"></i>'
      : '<i class="fas fa-eye"></i>';
    toggle.setAttribute("aria-label", show ? "Hide password" : "Show password");
  });

  pass?.addEventListener("input", () => {
    if (!(pass instanceof HTMLInputElement)) return;
    const { label: L, pct } = passwordStrength(pass.value);
    if (fill) fill.style.width = `${pct}%`;
    if (label) label.textContent = `Strength: ${L}`;
  });

  document.querySelectorAll("[data-next]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const next = Number(btn.getAttribute("data-next"));
      if (next === 2) {
        const name = document.getElementById("su-name").value.trim();
        const email = document.getElementById("su-email").value.trim();
        const ne = document.getElementById("su-name-err");
        const ee = document.getElementById("su-email-err");
        if (ne) ne.hidden = true;
        if (ee) ee.hidden = true;
        let ok = true;
        if (name.length < 2) {
          if (ne) {
            ne.textContent = "Please enter your full name.";
            ne.hidden = false;
          }
          ok = false;
        }
        if (!isValidEmail(email)) {
          if (ee) {
            ee.textContent = "Enter a valid email.";
            ee.hidden = false;
          }
          ok = false;
        }
        if (!ok) return;
      }
      if (next === 3) {
        const pw = pass instanceof HTMLInputElement ? pass.value : "";
        const pe = document.getElementById("su-pass-err");
        if (pe) pe.hidden = true;
        if (pw.length < 8) {
          if (pe) {
            pe.textContent = "Use at least 8 characters.";
            pe.hidden = false;
          }
          return;
        }
        const st = passwordStrength(pw);
        if (st.score < 2) {
          if (pe) {
            pe.textContent = "Choose a stronger password (mix letters, numbers, symbols).";
            pe.hidden = false;
          }
          return;
        }
      }
      showStep(next);
    });
  });

  document.querySelectorAll("[data-goal]").forEach((b) => {
    b.addEventListener("click", () => {
      document
        .querySelectorAll("[data-goal]")
        .forEach((x) => x.classList.remove("is-active"));
      b.classList.add("is-active");
      selectedGoal = b.getAttribute("data-goal") || "Strength";
    });
  });

  document
    .getElementById("signup-form")
    ?.addEventListener("submit", async (e) => {
      e.preventDefault();
      const sub = document.getElementById("su-submit");
      const name = document.getElementById("su-name").value.trim();
      const email = document.getElementById("su-email").value.trim();
      const pw = pass instanceof HTMLInputElement ? pass.value : "";
      sub?.classList.add("ft-btn--loading");
      if (sub) sub.disabled = true;
      try {
        const data = await apiFetch("auth/register.php", {
          method: "POST",
          json: {
            full_name: name,
            email,
            password: pw,
            fitness_goal: selectedGoal,
          },
        });
        const u = data.user;
        setSession({
          email: u.email,
          name: u.full_name || name,
          userId: u.id,
          csrf_token: data.csrf_token || "",
        });
        showToast("success", "Account ready. Redirecting…");
        window.location.href = "home.html";
      } catch (err) {
        showToast("error", err.message || "Registration failed.");
      } finally {
        sub?.classList.remove("ft-btn--loading");
        if (sub) sub.disabled = false;
      }
    });

  document
    .getElementById("su-google")
    ?.addEventListener("click", () =>
      showToast("info", "Google sign-up demo."),
    );
  document
    .getElementById("su-apple")
    ?.addEventListener("click", () => showToast("info", "Apple sign-up demo."));
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", init);
} else {
  init();
}
