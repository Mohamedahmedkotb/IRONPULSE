import { apiFetch } from "../services/api.js";
import { showToast } from "../services/toast.js";

function whenLayoutReady(fn) {
  if (window.__fittrackLayoutReady) fn();
  else document.addEventListener("fittrack:layout-ready", fn, { once: true });
}

whenLayoutReady(async () => {
  const tbody = document.getElementById("meal-rows");
  try {
    const data = await apiFetch("meals/list.php");
    const rows = data.meals || [];
    const sorted = [...rows].sort(
      (a, b) => String(a.plan_date).localeCompare(String(b.plan_date)),
    );
    if (sorted.length) {
      tbody.innerHTML = sorted
        .map(
          (r) =>
            `<tr><td><strong>${r.plan_date}</strong></td><td>${r.breakfast || "—"}</td><td>${r.lunch || "—"}</td><td>${r.dinner || "—"}</td><td>${r.calories}</td></tr>`,
        )
        .join("");
    } else {
      const week = [
        { d: "Mon", b: "Oats + berries", l: "Chicken bowl", di: "Salmon + rice", cal: 3180 },
        { d: "Tue", b: "Greek yogurt", l: "Turkey wrap", di: "Steak + greens", cal: 3320 },
        { d: "Wed", b: "Egg scramble", l: "Tuna pasta", di: "Tofu stir fry", cal: 3100 },
      ];
      tbody.innerHTML = week
        .map(
          (r) =>
            `<tr><td><strong>${r.d}</strong></td><td>${r.b}</td><td>${r.l}</td><td>${r.di}</td><td>${r.cal}</td></tr>`,
        )
        .join("");
    }
  } catch {
    tbody.innerHTML = `<tr><td colspan="5" class="ft-muted">Sign in and save meal plans to sync here.</td></tr>`;
  }

  let water = 2.4;
  const wl = document.getElementById("water-l");
  document.getElementById("water-plus")?.addEventListener("click", () => {
    water = Math.round((water + 0.25) * 10) / 10;
    wl.textContent = String(water);
    showToast("success", "Logged hydration.");
  });
  document.getElementById("water-minus")?.addEventListener("click", () => {
    water = Math.max(0, Math.round((water - 0.25) * 10) / 10);
    wl.textContent = String(water);
  });

  const slider = document.getElementById("cal-slider");
  const calEl = document.getElementById("meal-cal");
  slider?.addEventListener("input", () => {
    calEl.textContent = slider.value;
  });
});
