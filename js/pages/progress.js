import { drawLineChart, drawBarChart } from "../services/charts.js";
import { apiFetch } from "../services/api.js";

const labelsWeek = ["M", "T", "W", "T", "F", "S", "S"];
const labelsMonth = ["J", "F", "M", "A", "M", "J", "J", "A", "S", "O", "N", "D"];

function whenLayoutReady(fn) {
  if (window.__fittrackLayoutReady) fn();
  else document.addEventListener("fittrack:layout-ready", fn, { once: true });
}

function resizeCanvas(canvas) {
  const w = canvas.parentElement?.clientWidth || 600;
  canvas.width = w;
  canvas.height = 220;
}

whenLayoutReady(async () => {
  let weekVol = [42, 55, 48, 70, 62, 80, 88];
  let monthVol = [40, 45, 50, 48, 60, 65, 70, 68, 75, 80, 78, 90];
  let weekBars = [3, 2, 4, 1, 3, 5, 2];
  let monthBars = [12, 14, 11, 15, 13, 16, 14, 12, 18, 15, 17, 20];

  try {
    const [wData, pData] = await Promise.all([
      apiFetch("workouts/list.php"),
      apiFetch("progress/list.php"),
    ]);
    const workouts = wData.workouts || [];
    const byDate = {};
    for (const x of workouts) {
      const d = x.date?.slice(0, 10);
      if (!d) continue;
      byDate[d] = (byDate[d] || 0) + 1;
    }
    const days = Object.keys(byDate).sort();
    const last7 = days.slice(-7);
    if (last7.length) {
      weekBars = last7.map((d) => byDate[d] || 0);
      while (weekBars.length < 7) weekBars.unshift(0);
      weekBars = weekBars.slice(-7);
    }
    const logs = pData.logs || [];
    const weights = logs
      .map((l) => l.weight)
      .filter((n) => n != null && Number(n) > 0)
      .slice(0, 12)
      .reverse();
    if (weights.length >= 2) {
      monthVol = weights.map((w) => Number(w) * 2);
      weekVol = weights.slice(-7).map((w) => Number(w) * 2);
      if (weekVol.length < 7) {
        const pad = Array(7 - weekVol.length).fill(weekVol[0] || 50);
        weekVol = [...pad, ...weekVol];
      }
    }
  } catch {
    /* keep defaults */
  }

  const pbs = [
    { icon: "fa-dumbbell", title: "Bench press", sub: "225 lbs × 1", time: "Today" },
    { icon: "fa-weight-hanging", title: "Deadlift", sub: "315 lbs × 2", time: "3d ago" },
    { icon: "fa-person-running", title: "5k run", sub: "21:40", time: "1w ago" },
  ];

  function renderPBs() {
    const ul = document.getElementById("pb-list");
    if (!ul) return;
    ul.innerHTML = pbs
      .map(
        (p) => `<li>
      <div class="activity-ico blue"><i class="fas ${p.icon}"></i></div>
      <div><div class="ft-flex-between" style="margin:0"><strong>${p.title}</strong><span class="ft-muted" style="font-size:0.75rem">${p.time}</span></div>
      <p class="ft-muted" style="font-size:0.85rem;margin:4px 0">${p.sub}</p></div>
    </li>`,
      )
      .join("");
  }

  renderPBs();
  const line = document.getElementById("chart-vol");
  const bar = document.getElementById("chart-bar");
  let range = "week";

  function redraw() {
    if (line instanceof HTMLCanvasElement) {
      resizeCanvas(line);
      const data = range === "week" ? weekVol : monthVol;
      drawLineChart(line, data.length ? data : [0, 0, 0, 0, 0, 0, 0], { padding: 20 });
    }
    if (bar instanceof HTMLCanvasElement) {
      resizeCanvas(bar);
      const vals = range === "week" ? weekBars : monthBars;
      const labs = range === "week" ? labelsWeek : labelsMonth;
      drawBarChart(bar, vals, { labels: labs });
    }
  }

  redraw();
  document.querySelectorAll("[data-range]").forEach((b) => {
    b.addEventListener("click", () => {
      range = b.getAttribute("data-range") || "week";
      document.querySelectorAll("[data-range]").forEach((x) => {
        x.classList.toggle("is-active", x.getAttribute("data-range") === range);
      });
      redraw();
    });
  });

  let resizeTimer;
  window.addEventListener("resize", () => {
    clearTimeout(resizeTimer);
    resizeTimer = window.setTimeout(redraw, 150);
  });
});
