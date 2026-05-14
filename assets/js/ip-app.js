/**
 * IronPulse — theme, global search, non-auth form submit feedback, shell a11y.
 */
(function () {
  "use strict";

  function currentTheme() {
    return document.documentElement.getAttribute("data-theme") === "dark"
      ? "dark"
      : "light";
  }

  function setTheme(next) {
    document.documentElement.setAttribute("data-theme", next);
    try {
      localStorage.setItem("ip-theme", next);
    } catch (e) {
      /* ignore */
    }
  }

  document.querySelectorAll("[data-ip-theme-toggle]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      setTheme(currentTheme() === "dark" ? "light" : "dark");
    });
  });

  var search = document.getElementById("global-search");
  if (search) {
    search.addEventListener("keydown", function (e) {
      if (e.key !== "Enter") return;
      e.preventDefault();
      var q = String(search.value || "").trim();
      var base =
        (window.__IP_URLS__ && window.__IP_URLS__.exercises) ||
        "/pages/exercises.php";
      var sep = base.indexOf("?") >= 0 ? "&" : "?";
      window.location.href = q ? base + sep + "search=" + encodeURIComponent(q) : base;
    });
  }

  document.addEventListener(
    "submit",
    function (e) {
      var form = e.target;
      if (!form || form.tagName !== "FORM") return;
      if (String(form.method || "").toLowerCase() !== "post") return;
      if (form.closest(".auth-page")) return;
      if (form.hasAttribute("data-ip-no-loading")) return;
      if (form.getAttribute("onsubmit")) return;
      var action = String(form.getAttribute("action") || "");
      if (action.indexOf("actions/") === -1) return;
      var btn = form.querySelector(
        'button[type="submit"], input[type="submit"]',
      );
      if (!btn || btn.disabled) return;
      btn.disabled = true;
      btn.classList.add("ft-btn--loading");
    },
    false,
  );

  function syncMenuExpanded() {
    var menuBtn = document.getElementById("sidebar-toggle");
    var side = document.getElementById("app-sidebar");
    if (!menuBtn || !side) return;
    menuBtn.setAttribute(
      "aria-expanded",
      side.classList.contains("is-open") ? "true" : "false",
    );
  }

  document.addEventListener("DOMContentLoaded", function () {
    syncMenuExpanded();
    var btn = document.getElementById("sidebar-toggle");
    var side = document.getElementById("app-sidebar");
    if (btn && side) {
      var obs = new MutationObserver(syncMenuExpanded);
      obs.observe(side, { attributes: true, attributeFilter: ["class"] });
    }

    document.querySelectorAll(".ip-toast").forEach(function (el) {
      window.setTimeout(function () {
        el.style.transition = "opacity .35s ease, transform .35s ease";
        el.style.opacity = "0";
        el.style.transform = "translateY(-6px)";
        window.setTimeout(function () {
          if (el.parentNode) el.parentNode.removeChild(el);
        }, 380);
      }, 5200);
    });
  });
})();
