import { drawLineChart } from "../services/charts.js";
import { apiFetch } from "../services/api.js";

function whenLayoutReady(fn) {
  if (window.__fittrackLayoutReady) fn();
  else document.addEventListener("fittrack:layout-ready", fn, { once: true });
}

function animateCounters() {
  document.querySelectorAll("[data-counter]").forEach((el) => {
    const target = Number(el.getAttribute("data-counter"));
    if (Number.isNaN(target)) return;
    const dur = 900;
    const t0 = performance.now();
    function frame(now) {
      const p = Math.min(1, (now - t0) / dur);
      const eased = 1 - (1 - p) ** 3;
      el.textContent = String(Math.round(target * eased));
      if (p < 1) requestAnimationFrame(frame);
    }
    requestAnimationFrame(frame);
  });
}

whenLayoutReady(async () => {
  let workoutCount = 142;
  let streak = 14;
  let programs = 3;
  let chartData = [42, 55, 48, 70, 62, 80, 88];

  try {
    const data = await apiFetch("auth/me.php");
    const s = data.stats || {};
    workoutCount = s.workout_count ?? workoutCount;
    streak = s.streak_days ?? streak;
    programs = s.routines_count ?? programs;
    if (Array.isArray(s.chart_calories_last_7) && s.chart_calories_last_7.length) {
      chartData = s.chart_calories_last_7.map((n) => Number(n) || 0);
    }
  } catch {
    /* demo numbers */
  }

  const statCards = document.querySelectorAll(".dash-stats .dash-stat-card");
  const c0 = statCards[0]?.querySelector(".num[data-counter]");
  const c1 = statCards[1]?.querySelector("[data-counter]");
  const c2 = statCards[2]?.querySelector(".num[data-counter]");
  if (c0) c0.setAttribute("data-counter", String(workoutCount));
  if (c1) c1.setAttribute("data-counter", String(streak));
  if (c2) c2.setAttribute("data-counter", String(programs));

  animateCounters();

  const canvas = document.getElementById("chart-home-line");
  let resizeTimer;
  if (canvas instanceof HTMLCanvasElement) {
    const draw = () => {
      const w = canvas.parentElement?.clientWidth || 800;
      canvas.width = w;
      canvas.height = 220;
      drawLineChart(canvas, chartData, { padding: 20 });
    };
    draw();
    window.addEventListener("resize", () => {
      clearTimeout(resizeTimer);
      resizeTimer = window.setTimeout(draw, 150);
    });
  }

  try {
    const wData = await apiFetch("workouts/list.php");
    const rows = (wData.workouts || []).slice(0, 3);
    const ul = document.querySelector(".dash-grid .activity-list");
    if (ul && rows.length) {
      ul.innerHTML = rows
        .map(
          (r) => `<li>
          <div class="activity-ico blue"><i class="fas fa-dumbbell"></i></div>
          <div>
            <div class="ft-flex-between" style="margin: 0; align-items: flex-start">
              <strong>${r.name}</strong>
              <span class="ft-muted" style="font-size: 0.75rem">${r.date}</span>
            </div>
            <p class="ft-muted" style="font-size: 0.85rem; margin: 4px 0">${r.notes || r.type}</p>
            <span class="ft-pill is-active" style="cursor: default; font-size: 0.7rem">${r.durationMin} min</span>
          </div>
        </li>`,
        )
        .join("");
    }
  } catch {
    /* keep static HTML */
  }
});
