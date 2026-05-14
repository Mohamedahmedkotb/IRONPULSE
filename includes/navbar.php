<div class="app-main-wrap">
<header class="app-topbar">
    <button type="button" id="sidebar-toggle" class="app-topbar__menu" aria-label="Open menu">
        <i class="fas fa-bars"></i>
    </button>
    <div class="ft-flex" style="flex:1;align-items:center;gap:var(--space-4);min-width:0;width:100%">
        <a href="<?= ip_h(ip_url('pages/dashboard.php')) ?>" class="app-topbar__logo-link" aria-label="IronPulse home">
            <img src="<?= ip_h(ip_url('assets/images/favicon.svg')) ?>" alt="" width="34" height="34">
        </a>
        <div class="app-topbar__search" role="search">
            <label class="sr-only" for="global-search">Search</label>
            <i class="fas fa-search" aria-hidden="true"></i>
            <input type="search" id="global-search" placeholder="Search programs, exercises…" autocomplete="off">
        </div>
        <div class="app-topbar__actions">
            <a href="<?= ip_h(ip_url('pages/profile.php')) ?>" class="app-topbar__avatar-link" title="Profile" aria-label="Profile">
                <span class="user-monogram user-monogram--sm" aria-hidden="true"><?= ip_h(ip_user_initials($currentUser ?? [])) ?></span>
            </a>
        </div>
    </div>
</header>
