/**
 * Layout partials (sync with /components/*.html when editing)
 */
export const navbarHTML = `<a href="home.html" class="app-topbar__logo-link" aria-label="Ironpulse home"><img src="../assets/media/logo.png" alt="Ironpulse" width="34" height="34" /></a>
<div class="app-topbar__search" role="search">
  <label class="sr-only" for="global-search">Search</label>
  <i class="fas fa-search" aria-hidden="true"></i>
  <input type="search" id="global-search" placeholder="Search programs, exercises, coaches…" autocomplete="off" />
</div>
<div class="app-topbar__actions">
  <button type="button" class="app-topbar__icon-btn" id="btn-notifications" title="Notifications" aria-label="Notifications">
    <i class="fas fa-bell"></i>
  </button>
  <a href="Userprofile.html" class="app-topbar__avatar-link" title="Profile" aria-label="Open profile">
    <span class="user-monogram user-monogram--sm" id="topbar-initials" aria-hidden="true">AT</span>
  </a>
</div>`;

export const sidebarHTML = `<div class="app-sidebar__brand">
  <a href="home.html" class="app-sidebar__brand-link">
    <img class="app-sidebar__logo-img" src="../assets/media/logo.png" alt="Ironpulse" width="44" height="44" />
    <div class="app-sidebar__titles">
      <h1>Ironpulse</h1>
      <span>Performance OS</span>
    </div>
  </a>
</div>
<nav class="app-sidebar__nav" aria-label="Main">
  <p class="app-sidebar__section-label">Main</p>
  <ul>
    <li><a class="app-sidebar__link" data-nav="home" href="home.html"><i class="fas fa-th-large"></i> Dashboard</a></li>
    <li><a class="app-sidebar__link" data-nav="progress" href="ProgressDashboard.html"><i class="fas fa-chart-line"></i> Progress</a></li>
    <li><a class="app-sidebar__link" data-nav="routine" href="routinebuilder.html"><i class="fas fa-layer-group"></i> Programs</a></li>
    <li><a class="app-sidebar__link" data-nav="logger" href="workoutlogger.html"><i class="fas fa-clipboard-list"></i> Workout Log</a></li>
  </ul>
  <p class="app-sidebar__section-label">Content</p>
  <ul>
    <li><a class="app-sidebar__link" data-nav="library" href="Exerciselibrary.html"><i class="fas fa-dumbbell"></i> Library</a></li>
    <li><a class="app-sidebar__link" data-nav="tips" href="FitnessTips.html"><i class="fas fa-compass"></i> Explore</a></li>
    <li><a class="app-sidebar__link" data-nav="coaches" href="EliteCoaches.html"><i class="fas fa-user-tie"></i> Coaches</a></li>
    <li><a class="app-sidebar__link" data-nav="meals" href="mealplans.html"><i class="fas fa-utensils"></i> Meal Plans</a></li>
    <li><a class="app-sidebar__link" data-nav="bmi" href="BMICalculator.html"><i class="fas fa-weight-scale"></i> BMI</a></li>
    <li><a class="app-sidebar__link" data-nav="profile" href="Userprofile.html"><i class="fas fa-user"></i> Profile</a></li>
  </ul>
</nav>
<div class="app-sidebar__user">
  <span class="user-monogram" id="sidebar-initials" aria-hidden="true">AT</span>
  <div class="app-sidebar__user-meta">
    <strong id="sidebar-user-name">Athlete</strong>
    <span>Elite member</span>
  </div>
  <button type="button" class="btn-ghost-sidebar" id="sidebar-logout" title="Log out" aria-label="Log out"><i class="fas fa-right-from-bracket"></i></button>
</div>`;

export const footerHTML = `<footer class="app-footer">
  <div class="app-footer__brand">
    <img src="../assets/media/logo.png" alt="" class="app-footer__logo" width="36" height="36" />
    <div>
      <strong class="app-footer__name">Ironpulse</strong>
      <p class="app-footer__tagline">Train smarter. Track everything.</p>
    </div>
  </div>
  <nav class="app-footer__nav" aria-label="Footer">
    <a href="#">Privacy</a>
    <a href="#">Terms</a>
    <a href="#">Support</a>
    <a href="FitnessTips.html">Tips</a>
  </nav>
  <p class="app-footer__copy">© <span id="footer-year"></span> Ironpulse. All rights reserved.</p>
</footer>`;

export const authHeaderHTML = `<div class="auth-header-inner">
  <a href="home.html" class="auth-header-logo" data-auth-home>
    <span class="auth-header-wordmark">Ironpulse</span>
  </a>
  <nav class="auth-header-nav" aria-label="Account">
    <a href="login.html" class="auth-header-link" data-auth-login>Log in</a>
    <a href="signup.html" class="ft-btn ft-btn--primary ft-btn--sm" data-auth-signup>Sign up</a>
  </nav>
</div>`;
