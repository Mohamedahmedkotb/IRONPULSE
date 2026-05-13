import {
  navbarHTML,
  sidebarHTML,
  footerHTML,
  authHeaderHTML,
} from "./components/partials.js";
import { componentUrl } from "./utils/paths.js";
import { getSession, clearSession } from "./utils/storage.js";
import { refreshSessionFromMe, logoutAll } from "./services/api.js";
import { initialsFromName } from "./utils/initials.js";
import { showToast } from "./services/toast.js";

async function loadHtml(path) {
  try {
    const res = await fetch(path, { cache: "no-cache" });
    if (res.ok) return await res.text();
  } catch {
    /* file:// or blocked */
  }
  return null;
}

function setActiveNav(page) {
  document.querySelectorAll("[data-nav]").forEach((el) => {
    el.classList.toggle("is-active", el.getAttribute("data-nav") === page);
  });
}

function applyUserChrome() {
  const s = getSession();
  const name = s?.name || "Athlete";
  const initials = initialsFromName(name);
  const n1 = document.getElementById("sidebar-user-name");
  const sideMono = document.getElementById("sidebar-initials");
  const topMono = document.getElementById("topbar-initials");
  if (n1) n1.textContent = name;
  if (sideMono) sideMono.textContent = initials;
  if (topMono) topMono.textContent = initials;
}

document.addEventListener("fittrack:profile-updated", () => applyUserChrome());

function wireShellInteractions() {
  const menuBtn = document.getElementById("sidebar-toggle");
  const sidebar = document.getElementById("app-sidebar");
  const backdrop = document.getElementById("sidebar-backdrop");

  const close = () => {
    sidebar?.classList.remove("is-open");
    backdrop?.classList.remove("is-visible");
    document.body.style.overflow = "";
  };

  menuBtn?.addEventListener("click", () => {
    sidebar?.classList.toggle("is-open");
    backdrop?.classList.toggle("is-visible");
    document.body.style.overflow = sidebar?.classList.contains("is-open")
      ? "hidden"
      : "";
  });

  backdrop?.addEventListener("click", close);
  sidebar
    ?.querySelectorAll("a")
    .forEach((a) => a.addEventListener("click", close));

  document.getElementById("sidebar-logout")?.addEventListener("click", async () => {
    await logoutAll();
    window.location.href = "login.html";
  });

  document
    .getElementById("btn-notifications")
    ?.addEventListener("click", () => {
      showToast("info", "You have no new notifications.");
    });

  const search = document.getElementById("global-search");
  search?.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      const q = search.value.trim();
      if (q)
        showToast(
          "info",
          `Search saved: “${q}”. Open Library or Coaches to filter.`,
        );
    }
  });
}

async function injectPartial(targetId, filename, fallbackHtml) {
  const el = document.getElementById(targetId);
  if (!el) return;
  const url = componentUrl(filename);
  const fetched = await loadHtml(url.href);
  el.innerHTML = fetched || fallbackHtml;
}

export async function initLayout() {
  const shell = document.body.dataset.shell || "dashboard";
  const page = document.body.dataset.page || "";
  const authRequired = document.body.dataset.authRequired === "true";

  if (authRequired && shell === "dashboard") {
    try {
      const data = await refreshSessionFromMe();
      if (!data?.user?.email) {
        throw new Error("unauthorized");
      }
    } catch {
      clearSession();
      window.location.replace("login.html");
      return;
    }
  }

  if (shell === "dashboard") {
    await injectPartial("app-sidebar", "sidebar.html", sidebarHTML);
    await injectPartial("app-navbar", "navbar.html", navbarHTML);
    await injectPartial("app-footer", "footer.html", footerHTML);
    setActiveNav(page);
    applyUserChrome();
    wireShellInteractions();
  } else if (shell === "auth") {
    await injectPartial("auth-header-slot", "auth-header.html", authHeaderHTML);
    await injectPartial("app-footer", "footer.html", footerHTML);
  }

  const y = document.getElementById("footer-year");
  if (y) y.textContent = String(new Date().getFullYear());

  window.__fittrackLayoutReady = true;
  document.dispatchEvent(
    new CustomEvent("fittrack:layout-ready", { detail: { shell, page } }),
  );
}

initLayout().catch(console.error);
