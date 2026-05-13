import { openModal, closeModal } from "../services/modal.js";
import { showToast } from "../services/toast.js";
import { apiFetch } from "../services/api.js";

const filters = ["All specialties", "Strength", "Endurance", "Mobility"];
let filter = filters[0];
let q = "";

/** @type {Array<{id:number,name:string,spec:string,rate:number,stars:string,img:string,tags:string[],f:string}>} */
let coaches = [];

function specToFilter(spec) {
  const s = (spec || "").toLowerCase();
  if (s.includes("endurance") || s.includes("running") || s.includes("cycling")) {
    return "Endurance";
  }
  if (s.includes("mobility") || s.includes("yoga") || s.includes("recovery")) {
    return "Mobility";
  }
  return "Strength";
}

async function loadCoaches() {
  const data = await apiFetch("coaches/list.php");
  coaches = (data.coaches || []).map((c) => {
    const parts = String(c.specialty || "")
      .split(/[,/&]/)
      .map((x) => x.trim())
      .filter(Boolean);
    return {
      id: c.id,
      name: c.name,
      spec: c.specialty || "",
      rate: 75 + (c.id % 5) * 5,
      stars: Number(c.rating || 0).toFixed(2),
      img: c.image || "",
      tags: parts.length ? parts.slice(0, 3) : [c.specialty || "Coach"],
      f: specToFilter(c.specialty),
    };
  });
}

function whenLayoutReady(fn) {
  if (window.__fittrackLayoutReady) fn();
  else document.addEventListener("fittrack:layout-ready", fn, { once: true });
}

function renderFilters() {
  document.getElementById("coach-filters").innerHTML = filters
    .map(
      (f) =>
        `<button type="button" class="ft-pill${f === filter ? " is-active" : ""}" data-f="${f}">${f}</button>`,
    )
    .join("");
}

function list() {
  return coaches.filter((c) => {
    const okF = filter === filters[0] || c.f === filter;
    const okQ =
      !q ||
      c.name.toLowerCase().includes(q) ||
      c.spec.toLowerCase().includes(q) ||
      c.tags.some((t) => t.toLowerCase().includes(q));
    return okF && okQ;
  });
}

function renderGrid() {
  document.getElementById("coach-grid").innerHTML = list()
    .map(
      (c) => `
    <article class="media-card" data-coach-id="${c.id}" data-coach="${encodeURIComponent(c.name)}">
      <div class="thumb" style="background-image:url(${c.img})">
        <span class="badge" style="left:auto;right:12px;background:rgba(251,191,36,0.95);color:#0f172a">★ ${c.stars}</span>
      </div>
      <div class="body">
        <h3>${c.name}</h3>
        <p class="ft-muted" style="font-size:0.875rem">${c.spec}</p>
        <div class="ft-pills" style="margin-top:8px">${c.tags.map((t) => `<span class="ft-pill" style="cursor:default;font-size:0.7rem">${t}</span>`).join("")}</div>
        <div class="ft-flex-between" style="margin-top:14px">
          <span style="font-weight:800;color:var(--text-primary)">$${c.rate}/hr</span>
          <button type="button" class="ft-btn ft-btn--primary ft-btn--sm coach-book">Book</button>
        </div>
      </div>
    </article>`,
    )
    .join("");
}

whenLayoutReady(async () => {
  try {
    await loadCoaches();
  } catch {
    showToast("error", "Could not load coaches.");
  }
  renderFilters();
  renderGrid();

  document.getElementById("coach-filters")?.addEventListener("click", (e) => {
    const b = e.target.closest("[data-f]");
    if (!b) return;
    filter = b.getAttribute("data-f") || filters[0];
    renderFilters();
    renderGrid();
  });

  document.getElementById("coach-q")?.addEventListener("input", (e) => {
    q = e.target.value.trim().toLowerCase();
    renderGrid();
  });

  document.getElementById("coach-grid")?.addEventListener("click", (e) => {
    const book = e.target.closest(".coach-book");
    const card = e.target.closest("[data-coach-id]");
    if (!book || !card) return;
    const id = Number(card.getAttribute("data-coach-id"));
    const name = decodeURIComponent(card.getAttribute("data-coach"));
    const c = coaches.find((x) => x.id === id);
    if (!c) return;
    openModal({
      title: `Book ${c.name}`,
      bodyHtml: `<p>${c.spec} · ★ ${c.stars} · $${c.rate}/hr</p>
        <label class="ft-label" style="margin-top:16px">Preferred time</label>
        <select class="ft-input" id="bk-time"><option>Morning</option><option>Afternoon</option><option>Evening</option></select>
        <label class="ft-label" style="margin-top:12px">Notes</label>
        <textarea class="ft-input" id="bk-notes" rows="3" placeholder="Goals, injuries, schedule…"></textarea>`,
      footerHtml: `<button type="button" class="ft-btn ft-btn--secondary" data-close-modal>Cancel</button>
        <button type="button" class="ft-btn ft-btn--primary" id="bk-confirm">Request session</button>`,
    });
    document.getElementById("bk-confirm")?.addEventListener(
      "click",
      async () => {
        try {
          const when = new Date();
          when.setDate(when.getDate() + 1);
          await apiFetch("coaches/book.php", {
            method: "POST",
            json: {
              coach_id: id,
              booking_date: when.toISOString().slice(0, 19).replace("T", " "),
              notes: document.getElementById("bk-notes")?.value?.trim() || "",
            },
          });
          closeModal();
          showToast("success", `Booking request sent to ${name}.`);
        } catch (err) {
          showToast("error", err.message || "Booking failed.");
        }
      },
      { once: true },
    );
  });
});
