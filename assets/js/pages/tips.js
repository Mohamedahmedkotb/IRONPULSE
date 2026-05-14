import { openModal, closeModal } from "../services/modal.js";
import { showToast } from "../services/toast.js";

const filters = ["All", "Strength", "Cardio", "Nutrition", "Recovery"];
const articles = [
  {
    cat: "Strength",
    title: "Bracing 101",
    desc: "Create 360° pressure for heavy compounds.",
    img: "photo-1581009146145-b5ef050c2e1e?auto=format&fit=crop&w=600&q=80",
  },
  {
    cat: "Cardio",
    title: "Zone 2 without boredom",
    desc: "Protocols that keep heart rate honest.",
    img: "photo-1476480862126-209bfaa8edc8?auto=format&fit=crop&w=600&q=80",
  },
  {
    cat: "Nutrition",
    title: "Protein distribution",
    desc: "Why 4 meals beat 1 mega-shake.",
    img: "photo-1490645935967-10de6ba17061?auto=format&fit=crop&w=600&q=80",
  },
  {
    cat: "Recovery",
    title: "Sleep extension tactics",
    desc: "Stack habits for +45 min deep sleep.",
    img: "photo-1541781774459-bb2af2f05b55?auto=format&fit=crop&w=600&q=80",
  },
  {
    cat: "Strength",
    title: "Accessory selection",
    desc: "Pick lifts that fix your weak links.",
    img: "photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=600&q=80",
  },
  {
    cat: "Recovery",
    title: "Deload week design",
    desc: "Reduce fatigue without losing skill.",
    img: "photo-1518611012118-696072aa579a?auto=format&fit=crop&w=600&q=80",
  },
];

let active = "All";

function whenLayoutReady(fn) {
  if (window.__fittrackLayoutReady) fn();
  else document.addEventListener("fittrack:layout-ready", fn, { once: true });
}

function renderFilters() {
  const host = document.getElementById("tip-filters");
  host.innerHTML = filters
    .map(
      (f) =>
        `<button type="button" class="ft-pill${f === active ? " is-active" : ""}" data-f="${f}">${f}</button>`,
    )
    .join("");
}

function renderGrid() {
  const host = document.getElementById("tip-grid");
  const list =
    active === "All" ? articles : articles.filter((a) => a.cat === active);
  host.innerHTML = list
    .map(
      (a) => `
    <article class="media-card" data-tip="${a.title}">
      <div class="thumb" style="background-image:url(https://images.unsplash.com/${a.img})">
        <span class="badge">${a.cat}</span>
      </div>
      <div class="body">
        <h3>${a.title}</h3>
        <p class="ft-muted" style="font-size:0.875rem">${a.desc}</p>
        <button type="button" class="ft-btn ft-btn--ghost ft-btn--sm" style="margin-top:12px">Open</button>
      </div>
    </article>`,
    )
    .join("");
}

whenLayoutReady(() => {
  renderFilters();
  renderGrid();

  document.getElementById("tip-filters")?.addEventListener("click", (e) => {
    const b = e.target.closest("[data-f]");
    if (!b) return;
    active = b.getAttribute("data-f") || "All";
    renderFilters();
    renderGrid();
  });

  document.getElementById("featured-read")?.addEventListener("click", () => {
    openModal({
      title: "The perfect deadlift setup",
      bodyHtml: `<p>Start with mid-foot bar placement, pull slack, and wedge your hips until shins touch the bar. Maintain lat engagement and push the floor away.</p><p class="ft-muted" style="margin-top:12px">Estimated read: 6 min</p>`,
      footerHtml: `<button type="button" class="ft-btn ft-btn--secondary" data-close-modal>Close</button>`,
    });
  });

  document.getElementById("tip-grid")?.addEventListener("click", (e) => {
    const card = e.target.closest(".media-card");
    if (!card) return;
    const title = card.getAttribute("data-tip");
    openModal({
      title,
      bodyHtml: `<p>Full article content would load here. This demo focuses on layout and navigation.</p>`,
      footerHtml: `<button type="button" class="ft-btn ft-btn--secondary" data-close-modal>Close</button>`,
    });
    showToast("info", `Opened “${title}”.`);
  });
});
