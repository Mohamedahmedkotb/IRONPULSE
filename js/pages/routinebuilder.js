import { apiFetch } from "../services/api.js";
import { showToast } from "../services/toast.js";

/** @type {{id:number,name:string,m:string,img:string}[]} */
let libExercises = [];

/** @type {{uid:string,name:string,sets:number,reps:number,exercise_id?:number}[]} */
let exercises = [];

function uid() {
  return Math.random().toString(36).slice(2, 9);
}

async function loadLibrary() {
  try {
    const data = await apiFetch("exercises/list.php");
    libExercises = (data.exercises || []).map((e) => ({
      id: e.id,
      name: e.name || e.n,
      m: e.muscle_group || e.m || "",
      img: e.img || "",
    }));
  } catch {
    libExercises = [];
  }
}

async function loadState() {
  const params = new URLSearchParams(window.location.search);
  const id = params.get("id");
  if (!id) sessionStorage.removeItem("ft-routine-id");
  if (id) {
    try {
      const data = await apiFetch("routines/list.php");
      const found = (data.routines || []).find((r) => String(r.id) === String(id));
      if (found) {
        document.getElementById("rb-title").value = found.title;
        document.getElementById("rb-desc").value = found.description || "";
        exercises = (found.exercises || []).map((e) => ({
          uid: uid(),
          name: e.name,
          sets: e.sets,
          reps: e.reps,
          exercise_id: e.exercise_id,
        }));
        return;
      }
    } catch {
      showToast("error", "Could not load routine.");
    }
  }
  exercises = [
    { uid: uid(), name: "Bench press", sets: 4, reps: 6, exercise_id: undefined },
    { uid: uid(), name: "Pull-up", sets: 4, reps: 8, exercise_id: undefined },
  ];
  if (!id) {
    document.getElementById("rb-title").value = "Upper A — Strength";
    document.getElementById("rb-desc").value = "Heavy compounds + accessories.";
  } else {
    document.getElementById("rb-title").value = "Routine";
    document.getElementById("rb-desc").value = "";
  }
}

async function persist() {
  const title = document.getElementById("rb-title").value.trim() || "Untitled";
  const description = document.getElementById("rb-desc").value.trim();
  const params = new URLSearchParams(window.location.search);
  const existingId = params.get("id") || sessionStorage.getItem("ft-routine-id");
  const payload = {
    id: existingId ? Number(existingId) : undefined,
    title,
    description,
    exercises: exercises.map(({ name, sets, reps, exercise_id }) => ({
      name,
      sets,
      reps,
      exercise_id: exercise_id || undefined,
    })),
  };
  try {
    const data = await apiFetch("routines/create.php", {
      method: "POST",
      json: payload,
    });
    const newId = data.id;
    sessionStorage.setItem("ft-routine-id", String(newId));
    history.replaceState(
      null,
      "",
      `routinebuilder.html?id=${encodeURIComponent(String(newId))}`,
    );
    showToast("success", "Routine saved.");
  } catch (err) {
    showToast("error", err.message || "Save failed.");
  }
}

function renderList() {
  document.getElementById("rb-list").innerHTML = exercises
    .map(
      (ex, idx) => `
    <div class="rb-exercise" draggable="true" data-uid="${ex.uid}" data-idx="${idx}">
      <div class="ft-flex-between" style="margin-bottom:8px">
        <span><span class="rb-handle" aria-hidden="true">⠿</span> <strong>${ex.name}</strong></span>
        <button type="button" class="ft-btn ft-btn--ghost ft-btn--sm rb-remove" data-uid="${ex.uid}">Remove</button>
      </div>
      <div class="ft-flex" style="gap:12px; flex-wrap:wrap">
        <label style="font-size:0.8rem">Sets <input type="number" class="ft-input rb-sets" data-uid="${ex.uid}" value="${ex.sets}" style="width:72px;padding:6px 8px" /></label>
        <label style="font-size:0.8rem">Reps <input type="number" class="ft-input rb-reps" data-uid="${ex.uid}" value="${ex.reps}" style="width:72px;padding:6px 8px" /></label>
      </div>
    </div>`,
    )
    .join("");
}

function renderLib(q) {
  const qq = q.trim().toLowerCase();
  const list = libExercises.filter(
    (n) => !qq || n.name.toLowerCase().includes(qq) || n.m.toLowerCase().includes(qq),
  );
  document.getElementById("rb-lib").innerHTML = list
    .map(
      (n) =>
        `<button type="button" class="rb-side-item" data-add="${encodeURIComponent(n.name)}" data-exercise-id="${n.id}"><span>${n.name}</span><i class="fas fa-plus"></i></button>`,
    )
    .join("");
}

function whenLayoutReady(fn) {
  if (window.__fittrackLayoutReady) fn();
  else document.addEventListener("fittrack:layout-ready", fn, { once: true });
}

whenLayoutReady(async () => {
  await loadLibrary();
  await loadState();
  renderList();
  renderLib("");

  document
    .getElementById("rb-lib-q")
    ?.addEventListener("input", (e) => renderLib(e.target.value));

  document.getElementById("rb-lib")?.addEventListener("click", (e) => {
    const b = e.target.closest("[data-add]");
    if (!b) return;
    const name = decodeURIComponent(b.getAttribute("data-add") || "");
    const eid = Number(b.getAttribute("data-exercise-id")) || undefined;
    exercises.push({ uid: uid(), name, sets: 3, reps: 10, exercise_id: eid });
    renderList();
    showToast("success", `${name} added.`);
  });

  document.getElementById("rb-add-empty")?.addEventListener("click", () => {
    exercises.push({ uid: uid(), name: "New movement", sets: 3, reps: 8 });
    renderList();
  });

  document.getElementById("rb-list")?.addEventListener("click", (e) => {
    const rm = e.target.closest(".rb-remove");
    if (!rm) return;
    const id = rm.getAttribute("data-uid");
    exercises = exercises.filter((x) => x.uid !== id);
    renderList();
  });

  document.getElementById("rb-list")?.addEventListener("input", (e) => {
    const sets = e.target.closest(".rb-sets");
    const reps = e.target.closest(".rb-reps");
    const uidAttr = (sets || reps)?.getAttribute("data-uid");
    const ex = exercises.find((x) => x.uid === uidAttr);
    if (!ex) return;
    if (sets) ex.sets = Number(sets.value) || 1;
    if (reps) ex.reps = Number(reps.value) || 1;
  });

  const host = document.getElementById("rb-list");
  host?.addEventListener("dragstart", (e) => {
    const card = e.target.closest(".rb-exercise");
    if (!card) return;
    card.classList.add("dragging");
    e.dataTransfer.setData("text/plain", card.getAttribute("data-idx"));
    e.dataTransfer.effectAllowed = "move";
  });
  host?.addEventListener("dragend", (e) => {
    e.target.closest(".rb-exercise")?.classList.remove("dragging");
  });
  host?.addEventListener("dragover", (e) => e.preventDefault());
  host?.addEventListener("drop", (e) => {
    e.preventDefault();
    const card = e.target.closest(".rb-exercise");
    if (!card) return;
    const from = Number(e.dataTransfer.getData("text/plain"));
    const to = Number(card.getAttribute("data-idx"));
    if (Number.isNaN(from) || Number.isNaN(to) || from === to) return;
    const list = [...exercises];
    const [moved] = list.splice(from, 1);
    list.splice(to, 0, moved);
    exercises = list;
    renderList();
  });

  document.getElementById("rb-save")?.addEventListener("click", () => {
    persist();
  });
});
