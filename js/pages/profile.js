import {
  getProfile,
  setProfile,
  getSession,
  setSession,
  getPreferences,
  setPreferences,
} from "../utils/storage.js";
import { initialsFromName } from "../utils/initials.js";
import { showToast } from "../services/toast.js";
import { apiFetch } from "../services/api.js";

const GOAL_OPTIONS = [
  "Muscle gain",
  "Hypertrophy",
  "Strength",
  "Endurance",
  "Mobility",
  "Fat loss",
];

const TROPHIES = [
  { title: "First PR", sub: "Logged a personal best", icon: "fa-trophy", mod: "gold" },
  { title: "Consistency", sub: "4-week training block", icon: "fa-fire", mod: "blue" },
  { title: "Volume club", sub: "10k+ lbs in one session", icon: "fa-weight-hanging", mod: "green" },
  { title: "Explorer", sub: "Opened every main tab", icon: "fa-compass", mod: "purple" },
];

/** @type {any[]} */
let workoutsCache = [];
/** @type {any[]} */
let routinesCache = [];

function whenLayoutReady(fn) {
  if (window.__fittrackLayoutReady) fn();
  else document.addEventListener("fittrack:layout-ready", fn, { once: true });
}

async function refreshLists() {
  try {
    const [w, r] = await Promise.all([
      apiFetch("workouts/list.php"),
      apiFetch("routines/list.php"),
    ]);
    workoutsCache = w.workouts || [];
    routinesCache = r.routines || [];
  } catch {
    workoutsCache = [];
    routinesCache = [];
  }
}

function workoutsThisMonth() {
  const now = new Date();
  const y = now.getFullYear();
  const m = now.getMonth();
  return workoutsCache.filter((w) => {
    const d = new Date(w.date);
    return !Number.isNaN(d.getTime()) && d.getFullYear() === y && d.getMonth() === m;
  }).length;
}

function computeStreak() {
  const days = new Set(
    workoutsCache
      .map((w) => w.date)
      .filter(Boolean)
      .map((d) => d.slice(0, 10)),
  );
  if (!days.size) return 0;
  const today = new Date();
  const fmt = (dt) => dt.toISOString().slice(0, 10);
  let check = new Date(today);
  if (!days.has(fmt(check))) {
    check.setDate(check.getDate() - 1);
    if (!days.has(fmt(check))) return 0;
  }
  let streak = 0;
  while (days.has(fmt(check))) {
    streak += 1;
    check.setDate(check.getDate() - 1);
  }
  return streak;
}

function renderHero(p) {
  const mono = document.getElementById("prof-hero-initials");
  const nameEl = document.getElementById("prof-head-name");
  const emailEl = document.getElementById("prof-head-email");
  const cityEl = document.getElementById("prof-head-city");
  const wrap = document.getElementById("prof-head-city-wrap");
  if (mono) mono.textContent = initialsFromName(p.name);
  if (nameEl) nameEl.textContent = p.name || "Athlete";
  if (emailEl) {
    emailEl.textContent = p.email || "";
    emailEl.setAttribute("href", `mailto:${p.email || ""}`);
  }
  const city = (p.city || "").trim();
  if (cityEl && wrap) {
    const hint = wrap.querySelector(".ft-muted");
    if (city) {
      cityEl.textContent = city;
      if (hint) hint.remove();
    } else {
      cityEl.textContent = "Add city";
      if (!hint) {
        const span = document.createElement("span");
        span.className = "ft-muted";
        span.style.fontWeight = "500";
        span.textContent = " in Account";
        wrap.appendChild(span);
      }
    }
  }
}

function renderStats() {
  const w = workoutsThisMonth();
  const r = routinesCache.length;
  const st = computeStreak();
  const elW = document.getElementById("prof-stat-workouts");
  const elR = document.getElementById("prof-stat-programs");
  const elS = document.getElementById("prof-stat-streak");
  if (elW) elW.textContent = String(w);
  if (elR) elR.textContent = String(r);
  if (elS) elS.textContent = st > 0 ? `${st}d` : "—";
}

let draftGoals = [];

function renderGoalsFromProfile(p) {
  draftGoals = [...(p.goals && p.goals.length ? p.goals : GOAL_OPTIONS.slice(0, 2))];
  renderGoalPills();
}

function renderGoalPills() {
  const el = document.getElementById("prof-goals");
  if (!el) return;
  el.innerHTML = GOAL_OPTIONS.map((g) => {
    const on = draftGoals.includes(g);
    return `<button type="button" class="ft-pill${on ? " is-active" : ""}" data-goal="${g}">${g}</button>`;
  }).join("");
}

function renderTrophies() {
  const el = document.getElementById("prof-trophies");
  if (!el) return;
  el.innerHTML = TROPHIES.map(
    (t) => `<div class="trophy-card">
      <div class="trophy-card__ico trophy-card__ico--${t.mod}"><i class="fas ${t.icon}"></i></div>
      <div class="trophy-card__text"><strong>${t.title}</strong><span>${t.sub}</span></div>
    </div>`,
  ).join("");
}

let formSnapshot = null;

function readFormToProfile(base) {
  return {
    ...base,
    name: document.getElementById("prof-name")?.value?.trim() || base.name,
    email: document.getElementById("prof-email")?.value?.trim() || base.email,
    phone: document.getElementById("prof-phone")?.value?.trim() || "",
    city: document.getElementById("prof-city")?.value?.trim() || "",
    gender: document.getElementById("prof-gender")?.value || "",
    bio: document.getElementById("prof-bio")?.value?.trim() || "",
  };
}

function fillForm(p) {
  document.getElementById("prof-name").value = p.name || "";
  document.getElementById("prof-email").value = p.email || "";
  document.getElementById("prof-phone").value = p.phone || "";
  document.getElementById("prof-city").value = p.city || "";
  document.getElementById("prof-gender").value = p.gender || "";
  document.getElementById("prof-bio").value = p.bio || "";
  formSnapshot = JSON.stringify({
    name: p.name,
    email: p.email,
    phone: p.phone,
    city: p.city,
    gender: p.gender,
    bio: p.bio,
  });
}

function applyPrefsToUI() {
  const pr = getPreferences();
  const u = document.getElementById("pref-units");
  if (u) u.value = pr.units || "metric";
  document.getElementById("pref-email").checked = !!pr.emailWeekly;
  document.getElementById("pref-push").checked = !!pr.pushPRs;
  document.getElementById("pref-coach").checked = !!pr.coachMessages;
  document.getElementById("pref-compact").checked = !!pr.compactCharts;
}

async function persistProfileAndSession(next) {
  await apiFetch("users/update.php", {
    method: "PUT",
    json: {
      full_name: next.name,
      email: next.email,
      phone: next.phone,
      city: next.city,
      gender: next.gender,
      bio: next.bio,
      goals: next.goals,
    },
  });
  setProfile(next);
  const s = getSession();
  if (s) {
    setSession({
      ...s,
      name: next.name,
      email: next.email,
    });
  }
  renderHero(next);
  await refreshLists();
  renderStats();
  document.dispatchEvent(new CustomEvent("fittrack:profile-updated"));
}

function mergeApiProfile(apiProf) {
  const base = getProfile();
  return {
    ...base,
    name: apiProf.name || base.name,
    email: apiProf.email || base.email,
    phone: apiProf.phone ?? "",
    city: apiProf.city ?? "",
    gender: apiProf.gender ?? "",
    bio: apiProf.bio ?? "",
    goals:
      Array.isArray(apiProf.goals) && apiProf.goals.length
        ? apiProf.goals
        : base.goals,
  };
}

function switchTab(id) {
  document.querySelectorAll("[data-tab]").forEach((t) => {
    const active = t.getAttribute("data-tab") === id;
    t.setAttribute("aria-selected", active ? "true" : "false");
    t.classList.toggle("is-active", active);
  });
  document.querySelectorAll(".tab-panel").forEach((p) => {
    p.hidden = p.getAttribute("data-panel") !== id;
  });
}

whenLayoutReady(async () => {
  let p = getProfile();
  try {
    const data = await apiFetch("users/profile.php");
    p = mergeApiProfile(data.profile);
    setProfile(p);
  } catch {
    showToast("info", "Using cached profile until the server is available.");
  }

  await refreshLists();
  fillForm(p);
  renderHero(p);
  renderStats();
  renderGoalsFromProfile(p);
  renderTrophies();
  applyPrefsToUI();

  document.querySelectorAll("[data-tab]").forEach((t) => {
    t.classList.toggle("is-active", t.getAttribute("data-tab") === "overview");
  });

  document.querySelectorAll("[data-tab]").forEach((tab) => {
    tab.addEventListener("click", () => {
      switchTab(tab.getAttribute("data-tab"));
    });
  });

  document.getElementById("prof-tab-account")?.addEventListener("click", () => switchTab("account"));

  document.getElementById("prof-goals")?.addEventListener("click", (e) => {
    const b = e.target.closest("[data-goal]");
    if (!b) return;
    const g = b.getAttribute("data-goal");
    if (draftGoals.includes(g)) draftGoals = draftGoals.filter((x) => x !== g);
    else draftGoals.push(g);
    renderGoalPills();
  });

  document.getElementById("prof-save-goals")?.addEventListener("click", async () => {
    const next = { ...getProfile(), goals: [...draftGoals] };
    if (!next.goals.length) {
      showToast("error", "Keep at least one training goal.");
      return;
    }
    try {
      await persistProfileAndSession(next);
      showToast("success", "Training focus saved.");
    } catch (err) {
      showToast("error", err.message || "Could not save goals.");
    }
  });

  document.getElementById("prof-form")?.addEventListener("submit", async (e) => {
    e.preventDefault();
    const next = readFormToProfile(getProfile());
    if (!next.name || !next.email) {
      showToast("error", "Name and email are required.");
      return;
    }
    try {
      await persistProfileAndSession(next);
      fillForm(next);
      draftGoals = [...(next.goals || [])];
      renderGoalPills();
      showToast("success", "Profile updated.");
    } catch (err) {
      showToast("error", err.message || "Update failed.");
    }
  });

  document.getElementById("prof-reset-form")?.addEventListener("click", () => {
    if (!formSnapshot) return;
    const o = JSON.parse(formSnapshot);
    document.getElementById("prof-name").value = o.name || "";
    document.getElementById("prof-email").value = o.email || "";
    document.getElementById("prof-phone").value = o.phone || "";
    document.getElementById("prof-city").value = o.city || "";
    document.getElementById("prof-gender").value = o.gender || "";
    document.getElementById("prof-bio").value = o.bio || "";
    showToast("info", "Form reverted to last saved values.");
  });

  document.getElementById("prof-save-quick")?.addEventListener("click", async () => {
    const next = readFormToProfile(getProfile());
    try {
      await persistProfileAndSession(next);
      fillForm(next);
      draftGoals = [...(next.goals || [])];
      renderGoalPills();
      setPreferences({
        units: document.getElementById("pref-units")?.value || "metric",
        emailWeekly: document.getElementById("pref-email")?.checked,
        pushPRs: document.getElementById("pref-push")?.checked,
        coachMessages: document.getElementById("pref-coach")?.checked,
        compactCharts: document.getElementById("pref-compact")?.checked,
      });
      showToast("success", "Profile and preferences saved.");
    } catch (err) {
      showToast("error", err.message || "Save failed.");
    }
  });

  document.getElementById("pref-save")?.addEventListener("click", () => {
    setPreferences({
      units: document.getElementById("pref-units")?.value || "metric",
      emailWeekly: document.getElementById("pref-email")?.checked,
      pushPRs: document.getElementById("pref-push")?.checked,
      coachMessages: document.getElementById("pref-coach")?.checked,
      compactCharts: document.getElementById("pref-compact")?.checked,
    });
    showToast("success", "Preferences saved.");
  });
});
