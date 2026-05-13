import { showToast } from "../services/toast.js";

const len = 283;

function category(bmi) {
  if (bmi < 18.5) return { label: "Underweight", color: "#3b82f6" };
  if (bmi < 25) return { label: "Normal", color: "#22c55e" };
  if (bmi < 30) return { label: "Overweight", color: "#f59e0b" };
  return { label: "Obese", color: "#ef4444" };
}

function tips(cat) {
  if (cat.label === "Underweight")
    return [
      { t: "Fuel", d: "Gradual surplus with structured resistance training." },
      {
        t: "Nutrition",
        d: "Prioritize protein and whole foods around workouts.",
      },
    ];
  if (cat.label === "Normal")
    return [
      { t: "Maintain", d: "Keep habits consistent; adjust calories to goals." },
      {
        t: "Training",
        d: "Blend strength + easy cardio for sustainable progress.",
      },
    ];
  if (cat.label === "Overweight")
    return [
      {
        t: "Deficit",
        d: "Moderate deficit with high protein preserves muscle.",
      },
      {
        t: "Training",
        d: "Blend strength + easy cardio for sustainable progress.",
      },
    ];
  return [
    {
      t: "Clinical",
      d: "Discuss changes with a clinician for personalized care.",
    },
    {
      t: "Nutrition",
      d: "Prioritize protein and whole foods around workouts.",
    },
  ];
}

function readInputs() {
  const h = Number(document.getElementById("bmi-hn").value);
  const w = Number(document.getElementById("bmi-wn").value);
  return { h, w };
}

function renderGauge(bmi) {
  const arc = document.getElementById("bmi-arc");
  const t = Math.max(15, Math.min(40, bmi));
  const p = (t - 15) / 25;
  if (arc) arc.style.strokeDasharray = `${p * len} ${len}`;
}

function runCalc(showOk) {
  const { h, w } = readInputs();
  if (!h || h < 100 || !w || w < 35) {
    if (showOk) showToast("error", "Enter realistic height and weight.");
    return;
  }
  const m = h / 100;
  const bmi = Math.round((w / (m * m)) * 10) / 10;
  const cat = category(bmi);
  document.getElementById("bmi-val").textContent = String(bmi);
  const cEl = document.getElementById("bmi-cat");
  cEl.textContent = cat.label;
  cEl.style.color = cat.color;
  renderGauge(bmi);
  document.getElementById("bmi-tips").innerHTML = tips(cat)
    .map(
      (x) =>
        `<div class="ft-card" style="padding:16px"><strong>${x.t}</strong><p class="ft-muted" style="margin:8px 0 0;font-size:0.85rem">${x.d}</p></div>`,
    )
    .join("");
  if (showOk) showToast("success", "BMI updated.");
}

function whenLayoutReady(fn) {
  if (window.__fittrackLayoutReady) fn();
  else document.addEventListener("fittrack:layout-ready", fn, { once: true });
}

whenLayoutReady(() => {
  const hRange = document.getElementById("bmi-h");
  const hNum = document.getElementById("bmi-hn");
  const wRange = document.getElementById("bmi-w");
  const wNum = document.getElementById("bmi-wn");
  const hv = document.getElementById("bmi-hv");
  const wv = document.getElementById("bmi-wv");

  function syncH() {
    hNum.value = hRange.value;
    hv.textContent = hRange.value;
  }
  function syncW() {
    wNum.value = wRange.value;
    wv.textContent = wRange.value;
  }
  hRange.addEventListener("input", syncH);
  hNum.addEventListener("input", () => {
    hRange.value = hNum.value;
    syncH();
  });
  wRange.addEventListener("input", syncW);
  wNum.addEventListener("input", () => {
    wRange.value = wNum.value;
    syncW();
  });

  document.querySelectorAll("[data-g]").forEach((b) => {
    b.addEventListener("click", () => {
      document
        .querySelectorAll("[data-g]")
        .forEach((x) => x.classList.remove("is-active"));
      b.classList.add("is-active");
    });
  });

  document
    .getElementById("bmi-calc")
    ?.addEventListener("click", () => runCalc(true));
  runCalc(false);
});
