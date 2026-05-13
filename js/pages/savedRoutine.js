import { apiFetch } from "../services/api.js";
import { showToast } from "../services/toast.js";

function whenLayoutReady(fn) {
  if (window.__fittrackLayoutReady) fn();
  else document.addEventListener("fittrack:layout-ready", fn, { once: true });
}

function render(list) {
  const host = document.getElementById("sr-grid");
  if (!list.length) {
    host.innerHTML = `<div class="ft-card"><p class="ft-muted">No routines yet. Build one in the routine builder and save.</p></div>`;
    return;
  }
  host.innerHTML = list
    .map(
      (r) => `
    <article class="ft-card">
      <h3>${r.title}</h3>
      <p class="ft-muted" style="font-size:0.9rem">${r.description || ""}</p>
      <p style="margin:12px 0"><strong>${r.exercises?.length || 0}</strong> exercises</p>
      <div class="ft-flex" style="gap:8px">
        <a class="ft-btn ft-btn--primary ft-btn--sm" href="routinebuilder.html?id=${encodeURIComponent(r.id)}">Open</a>
        <button type="button" class="ft-btn ft-btn--ghost ft-btn--sm sr-del" data-id="${r.id}" style="color:var(--danger)">Delete</button>
      </div>
    </article>`,
    )
    .join("");
}

whenLayoutReady(async () => {
  let list = [];
  try {
    const data = await apiFetch("routines/list.php");
    list = data.routines || [];
  } catch {
    showToast("error", "Could not load routines.");
  }
  render(list);

  document.getElementById("sr-grid")?.addEventListener("click", async (e) => {
    const b = e.target.closest(".sr-del");
    if (!b) return;
    const id = b.getAttribute("data-id");
    try {
      await apiFetch(`routines/delete.php?id=${encodeURIComponent(id)}`, {
        method: "DELETE",
      });
      list = list.filter((r) => String(r.id) !== String(id));
      render(list);
      showToast("success", "Routine deleted.");
    } catch (err) {
      showToast("error", err.message || "Delete failed.");
    }
  });
});
