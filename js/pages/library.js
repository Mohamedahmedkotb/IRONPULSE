import { openModal, closeModal } from "../services/modal.js";
import { showToast } from "../services/toast.js";
import { apiFetch } from "../services/api.js";

const cats = ["All", "Chest", "Back", "Legs", "Shoulders", "Arms", "Core"];
/** @type {{n:string,m:string,img:string,d:string,id:number}[]} */
let exercises = [];
let cat = "All";
let q = "";

async function loadExercises() {
  try {
    const data = await apiFetch("exercises/list.php");
    exercises = (data.exercises || []).map((e) => ({
      id: e.id,
      n: e.name || e.n,
      m: e.muscle_group || e.m || "General",
      img:
        e.img && String(e.img).startsWith("http")
          ? e.img.replace(/^https:\/\/images\.unsplash\.com\//, "")
          : e.img || "photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=600&q=80",
      d: e.instructions || e.d || "",
    }));
  } catch {
    exercises = [];
  }
}

function renderCats() {
  document.getElementById("lib-cats").innerHTML = cats
    .map(
      (c) =>
        `<button type="button" class="ft-pill${c === cat ? " is-active" : ""}" data-c="${c}">${c}</button>`,
    )
    .join("");
}

function filtered() {
  return exercises.filter((ex) => {
    const okC = cat === "All" || ex.m === cat;
    const okQ =
      !q || ex.n.toLowerCase().includes(q) || ex.m.toLowerCase().includes(q);
    return okC && okQ;
  });
}

function renderGrid() {
  const grid = document.getElementById("lib-grid");
  if (!filtered().length) {
    grid.innerHTML = `<div class="ft-card"><p class="ft-muted">No exercises found. Try another filter.</p></div>`;
    return;
  }
  grid.innerHTML = filtered()
    .map(
      (ex) => `
    <article class="media-card" data-ex="${encodeURIComponent(ex.n)}" data-id="${ex.id}">
      <div class="thumb" style="background-image:url(${String(ex.img).startsWith("http") ? ex.img : `https://images.unsplash.com/${ex.img}`})"><span class="badge">${ex.m}</span></div>
      <div class="body"><h3>${ex.n}</h3><p class="ft-muted" style="font-size:0.85rem">${(ex.d || "—").slice(0, 72)}${(ex.d || "").length > 72 ? "…" : ""}</p>
      <button type="button" class="ft-btn ft-btn--secondary ft-btn--sm" style="margin-top:10px">Details</button></div>
    </article>`,
    )
    .join("");
}

function whenLayoutReady(fn) {
  if (window.__fittrackLayoutReady) fn();
  else document.addEventListener("fittrack:layout-ready", fn, { once: true });
}

whenLayoutReady(async () => {
  await loadExercises();
  renderCats();
  renderGrid();

  document.getElementById("lib-cats")?.addEventListener("click", (e) => {
    const b = e.target.closest("[data-c]");
    if (!b) return;
    cat = b.getAttribute("data-c") || "All";
    renderCats();
    renderGrid();
  });

  document.getElementById("lib-search")?.addEventListener("input", (e) => {
    q = e.target.value.trim().toLowerCase();
    renderGrid();
  });

  document.getElementById("lib-grid")?.addEventListener("click", (e) => {
    const card = e.target.closest("[data-ex]");
    if (!card) return;
    const name = decodeURIComponent(card.getAttribute("data-ex"));
    const ex = exercises.find((x) => x.n === name);
    if (!ex) return;
    openModal({
      title: ex.n,
      bodyHtml: `<p><strong>Target:</strong> ${ex.m}</p><p style="margin-top:12px">${ex.d || "—"}</p><p class="ft-muted" style="margin-top:12px">Cue: brace, track joints, own the eccentric.</p>`,
      footerHtml: `<button type="button" class="ft-btn ft-btn--secondary" data-close-modal>Close</button><button type="button" class="ft-btn ft-btn--primary" id="lib-add-routine">Add to routine builder</button>`,
    });
    document.getElementById("lib-add-routine")?.addEventListener(
      "click",
      () => {
        closeModal();
        window.location.href = "routinebuilder.html";
      },
      { once: true },
    );
  });

  document.getElementById("lib-add-custom")?.addEventListener("click", () => {
    openModal({
      title: "Custom exercise",
      bodyHtml: `<label class="ft-label">Name</label><input class="ft-input" id="cust-name" /><label class="ft-label" style="margin-top:12px">Muscle</label><input class="ft-input" id="cust-muscle" />`,
      footerHtml: `<button type="button" class="ft-btn ft-btn--secondary" data-close-modal>Cancel</button><button type="button" class="ft-btn ft-btn--primary" id="cust-save">Save</button>`,
    });
    document.getElementById("cust-save")?.addEventListener(
      "click",
      () => {
        const n = document.getElementById("cust-name")?.value?.trim();
        const m =
          document.getElementById("cust-muscle")?.value?.trim() || "Custom";
        if (!n) {
          showToast("error", "Enter a name.");
          return;
        }
        exercises.unshift({
          id: 0,
          n,
          m,
          img: "photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=600&q=80",
          d: "User-defined movement.",
        });
        closeModal();
        renderGrid();
        showToast("success", "Exercise added to this session (local only until synced).");
      },
      { once: true },
    );
  });
});
