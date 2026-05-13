import { apiFetch } from "../services/api.js";
import { openModal, closeModal } from "../services/modal.js";
import { showToast } from "../services/toast.js";

const defaultSets = [
  { set: 1, ex: "Barbell bench press", w: 135, r: 10, done: true },
  { set: 2, ex: "Barbell bench press", w: 185, r: 8, done: true },
  { set: 3, ex: "Barbell bench press", w: 205, r: 6, done: false },
];

let sets = JSON.parse(JSON.stringify(defaultSets));
let sec = 0;
let timer = null;
let paused = false;
/** @type {Array<{id:string,name:string,type:string,date:string,durationMin:number,notes:string,calories?:number}>} */
let workoutsCache = [];

function whenLayoutReady(fn) {
  if (window.__fittrackLayoutReady) fn();
  else document.addEventListener("fittrack:layout-ready", fn, { once: true });
}

async function loadWorkouts() {
  const data = await apiFetch("workouts/list.php");
  workoutsCache = data.workouts || [];
  return workoutsCache;
}

function renderHistory() {
  const q = document.getElementById("wl-search").value.trim().toLowerCase();
  const f = document.getElementById("wl-filter").value;
  const rows = workoutsCache.filter((x) => {
    const okQ =
      !q ||
      x.name.toLowerCase().includes(q) ||
      (x.notes || "").toLowerCase().includes(q);
    const okF = f === "all" || x.type === f;
    return okQ && okF;
  });
  document.getElementById("wl-rows").innerHTML = rows
    .map(
      (r) => `<tr data-id="${r.id}">
      <td>${r.date}</td><td>${r.name}</td><td>${r.type}</td><td>${r.durationMin} min</td><td>${r.notes || "—"}</td>
      <td style="white-space:nowrap">
        <button type="button" class="ft-btn ft-btn--ghost ft-btn--sm wl-edit">Edit</button>
        <button type="button" class="ft-btn ft-btn--ghost ft-btn--sm wl-del" style="color:var(--danger)">Delete</button>
      </td></tr>`,
    )
    .join("");
}

function renderSets() {
  document.getElementById("wl-sets").innerHTML = sets
    .map(
      (s, i) => `<tr data-i="${i}">
      <td>${s.set}</td><td>${s.ex}</td>
      <td><input type="number" class="wl-w" value="${s.w}" aria-label="Weight" /></td>
      <td><input type="number" class="wl-r" value="${s.r}" aria-label="Reps" /></td>
      <td><input type="checkbox" class="wl-d"${s.done ? " checked" : ""} aria-label="Set complete" /></td>
    </tr>`,
    )
    .join("");
}

function tick() {
  if (paused) return;
  sec += 1;
  const m = String(Math.floor(sec / 60)).padStart(2, "0");
  const s = String(sec % 60).padStart(2, "0");
  document.getElementById("wl-time").textContent = `${m}:${s}`;
  document.getElementById("wl-kcal").textContent = String(
    280 + Math.floor(sec / 20),
  );
  document.getElementById("wl-hr").textContent = String(
    110 + Math.floor((Math.sin(sec / 30) + 1) * 8),
  );
}

whenLayoutReady(() => {
  timer = window.setInterval(tick, 1000);

  loadWorkouts()
    .then(() => renderHistory())
    .catch(() => {
      showToast("error", "Could not load workouts.");
    });
  renderSets();

  document.getElementById("wl-pause")?.addEventListener("click", (e) => {
    paused = !paused;
    const btn = e.currentTarget;
    btn.innerHTML = paused
      ? '<i class="fas fa-play"></i> Resume'
      : '<i class="fas fa-pause"></i> Pause';
  });
  document.getElementById("wl-reset")?.addEventListener("click", () => {
    sec = 0;
    paused = false;
    document.getElementById("wl-time").textContent = "00:00";
  });

  document
    .getElementById("wl-search")
    ?.addEventListener("input", renderHistory);
  document
    .getElementById("wl-filter")
    ?.addEventListener("change", renderHistory);

  document.getElementById("wl-rows")?.addEventListener("click", async (e) => {
    const del = e.target.closest(".wl-del");
    const ed = e.target.closest(".wl-edit");
    const tr = e.target.closest("tr[data-id]");
    if (!tr) return;
    const id = tr.getAttribute("data-id");
    const item = workoutsCache.find((x) => x.id === id);
    if (del) {
      try {
        await apiFetch(`workouts/delete.php?id=${encodeURIComponent(id)}`, {
          method: "DELETE",
        });
        workoutsCache = workoutsCache.filter((x) => x.id !== id);
        renderHistory();
        showToast("success", "Workout removed.");
      } catch (err) {
        showToast("error", err.message || "Delete failed.");
      }
    }
    if (ed && item) {
      openModal({
        title: "Edit workout",
        bodyHtml: `<label class="ft-label">Name</label><input class="ft-input" id="e-name" value="${item.name.replace(/"/g, "&quot;")}" />
          <label class="ft-label" style="margin-top:12px">Type</label><select class="ft-input" id="e-type"><option>Push</option><option>Pull</option><option>Legs</option><option>Cardio</option></select>
          <label class="ft-label" style="margin-top:12px">Duration (min)</label><input type="number" class="ft-input" id="e-dur" value="${item.durationMin}" />
          <label class="ft-label" style="margin-top:12px">Notes</label><textarea class="ft-input" id="e-notes" rows="2">${(item.notes || "").replace(/</g, "&lt;")}</textarea>`,
        footerHtml: `<button type="button" class="ft-btn ft-btn--secondary" data-close-modal>Cancel</button><button type="button" class="ft-btn ft-btn--primary" id="e-save">Save</button>`,
      });
      document.getElementById("e-type").value = item.type;
      document.getElementById("e-save")?.addEventListener(
        "click",
        async () => {
          try {
            await apiFetch("workouts/update.php", {
              method: "PUT",
              json: {
                id,
                name: document.getElementById("e-name").value.trim() || item.name,
                type: document.getElementById("e-type").value,
                durationMin:
                  Number(document.getElementById("e-dur").value) ||
                  item.durationMin,
                notes: document.getElementById("e-notes").value.trim(),
                date: item.date,
                calories: item.calories ?? 0,
              },
            });
            item.name =
              document.getElementById("e-name").value.trim() || item.name;
            item.type = document.getElementById("e-type").value;
            item.durationMin =
              Number(document.getElementById("e-dur").value) ||
              item.durationMin;
            item.notes = document.getElementById("e-notes").value.trim();
            closeModal();
            renderHistory();
            showToast("success", "Workout updated.");
          } catch (err) {
            showToast("error", err.message || "Update failed.");
          }
        },
        { once: true },
      );
    }
  });

  document.getElementById("wl-sets")?.addEventListener("change", (e) => {
    const tr = e.target.closest("tr[data-i]");
    if (!tr) return;
    const i = Number(tr.getAttribute("data-i"));
    const w = tr.querySelector(".wl-w");
    const r = tr.querySelector(".wl-r");
    const d = tr.querySelector(".wl-d");
    if (w && r && d) {
      sets[i].w = Number(w.value) || 0;
      sets[i].r = Number(r.value) || 0;
      sets[i].done = d.checked;
    }
  });

  document.getElementById("wl-add")?.addEventListener("click", () => {
    openModal({
      title: "Log workout",
      bodyHtml: `<label class="ft-label">Name</label><input class="ft-input" id="n-name" placeholder="Leg day" />
        <label class="ft-label" style="margin-top:12px">Type</label><select class="ft-input" id="n-type"><option>Push</option><option>Pull</option><option>Legs</option><option>Cardio</option></select>
        <label class="ft-label" style="margin-top:12px">Duration (min)</label><input type="number" class="ft-input" id="n-dur" value="50" />
        <label class="ft-label" style="margin-top:12px">Notes</label><textarea class="ft-input" id="n-notes" rows="2"></textarea>`,
      footerHtml: `<button type="button" class="ft-btn ft-btn--secondary" data-close-modal>Cancel</button><button type="button" class="ft-btn ft-btn--primary" id="n-save">Save</button>`,
    });
    document.getElementById("n-save")?.addEventListener(
      "click",
      async () => {
        const name = document.getElementById("n-name").value.trim();
        if (!name) {
          showToast("error", "Enter a workout name.");
          return;
        }
        try {
          const kcal = 280 + Math.floor(Math.random() * 200);
          const res = await apiFetch("workouts/create.php", {
            method: "POST",
            json: {
              name,
              type: document.getElementById("n-type").value,
              date: new Date().toISOString().slice(0, 10),
              durationMin: Number(document.getElementById("n-dur").value) || 45,
              notes: document.getElementById("n-notes").value.trim(),
              calories: kcal,
            },
          });
          await loadWorkouts();
          renderHistory();
          closeModal();
          showToast("success", "Workout saved.");
        } catch (err) {
          showToast("error", err.message || "Save failed.");
        }
      },
      { once: true },
    );
  });

  document.getElementById("wl-finish")?.addEventListener("click", async () => {
    try {
      const kcal = 280 + Math.floor(sec / 20);
      await apiFetch("workouts/create.php", {
        method: "POST",
        json: {
          name:
            "Session " +
            new Date().toLocaleTimeString([], {
              hour: "2-digit",
              minute: "2-digit",
            }),
          type: "Push",
          date: new Date().toISOString().slice(0, 10),
          durationMin: Math.max(1, Math.floor(sec / 60)),
          notes: "From logger",
          calories: kcal,
        },
      });
      await loadWorkouts();
      renderHistory();
      showToast("success", "Workout finished and saved.");
    } catch (err) {
      showToast("error", err.message || "Could not save session.");
    }
  });
});
