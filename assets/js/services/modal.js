let overlay;

function ensureOverlay() {
  if (overlay) return overlay;
  overlay = document.createElement("div");
  overlay.className = "ft-modal-overlay";
  overlay.id = "ft-modal-overlay";
  overlay.innerHTML = `<div class="ft-modal" role="dialog" aria-modal="true" aria-labelledby="ft-modal-title">
    <div class="ft-modal__head">
      <h2 id="ft-modal-title" style="margin:0;font-size:1.15rem"></h2>
      <button type="button" class="ft-btn ft-btn--ghost ft-btn--sm" id="ft-modal-close" aria-label="Close">×</button>
    </div>
    <div class="ft-modal__body" id="ft-modal-body"></div>
    <div class="ft-modal__foot" id="ft-modal-foot"></div>
  </div>`;
  document.body.appendChild(overlay);
  overlay.addEventListener("click", (e) => {
    if (e.target === overlay) closeModal();
  });
  overlay
    .querySelector("#ft-modal-close")
    .addEventListener("click", closeModal);
  return overlay;
}

export function openModal({ title, bodyHtml, footerHtml = "" }) {
  const o = ensureOverlay();
  o.querySelector("#ft-modal-title").textContent = title;
  o.querySelector("#ft-modal-body").innerHTML = bodyHtml;
  const foot = o.querySelector("#ft-modal-foot");
  foot.innerHTML = footerHtml;
  foot.style.display = footerHtml ? "flex" : "none";
  foot.querySelectorAll("[data-close-modal]").forEach((btn) => {
    btn.addEventListener("click", closeModal, { once: true });
  });
  requestAnimationFrame(() => o.classList.add("is-open"));
  const onKey = (e) => {
    if (e.key === "Escape") {
      closeModal();
      document.removeEventListener("keydown", onKey);
    }
  };
  document.addEventListener("keydown", onKey);
}

export function closeModal() {
  if (overlay) overlay.classList.remove("is-open");
}
