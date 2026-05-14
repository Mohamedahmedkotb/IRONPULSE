<?php
declare(strict_types=1);

$nav = $currentNav ?? '';
$u = static fn (string $p): string => ip_url($p);

$topLinks = [
    ['id' => 'home', 'href' => 'pages/dashboard.php', 'label' => 'Dashboard'],
    ['id' => 'workouts', 'href' => 'pages/workouts.php', 'label' => 'Workouts'],
    ['id' => 'routines', 'href' => 'pages/routines.php', 'label' => 'Routines'],
    ['id' => 'exercises', 'href' => 'pages/exercises.php', 'label' => 'Exercises'],
];

$crumbMap = [
    'workouts' => ['Workouts', $u('pages/workouts.php')],
    'routines' => ['Routines', $u('pages/routines.php')],
    'exercises' => ['Exercises', $u('pages/exercises.php')],
    'coaches' => ['Coaches', $u('pages/coaches.php')],
    'meals' => ['Meal plans', $u('pages/meals.php')],
    'profile' => ['Profile', $u('pages/profile.php')],
    'settings' => ['Settings', $u('pages/settings.php')],
];

if ($nav === 'home' || $nav === '') {
    $crumbItems = [['label' => 'Dashboard', 'href' => null]];
} else {
    $crumbItems = [['label' => 'Dashboard', 'href' => $u('pages/dashboard.php')]];
    if (isset($crumbMap[$nav])) {
        $crumbItems[] = ['label' => $crumbMap[$nav][0], 'href' => null];
    }
}
?>
<div class="app-main-wrap">
<header class="app-topbar">
    <div class="app-topbar__primary">
        <button type="button" id="sidebar-toggle" class="app-topbar__menu" aria-label="Open menu" aria-expanded="false" aria-controls="app-sidebar">
            <i class="fas fa-bars" aria-hidden="true"></i>
        </button>
        <div class="app-topbar__cluster">
            <a href="<?= ip_h($u('pages/dashboard.php')) ?>" class="app-topbar__logo-link" aria-label="IronPulse home">
                <img src="<?= ip_h(ip_url('assets/images/favicon.svg')) ?>" alt="" width="34" height="34">
            </a>
            <nav class="app-topbar__links" aria-label="Quick navigation">
                <?php foreach ($topLinks as $L): ?>
                    <a href="<?= ip_h(ip_url($L['href'])) ?>" class="<?= $nav === $L['id'] ? 'is-active' : '' ?>" data-nav="<?= ip_h($L['id']) ?>"><?= ip_h($L['label']) ?></a>
                <?php endforeach; ?>
            </nav>
            <div class="app-topbar__search" role="search">
                <label class="sr-only" for="global-search">Search</label>
                <i class="fas fa-search" aria-hidden="true"></i>
                <input type="search" id="global-search" name="q" placeholder="Programs, exercises… (Enter to search library)" autocomplete="off" enterkeyhint="search">
            </div>
        </div>
        <div class="app-topbar__end">
            <button type="button" class="ip-theme-toggle" data-ip-theme-toggle aria-label="Toggle dark mode">
                <i class="fas fa-moon ip-icon-dark" aria-hidden="true"></i>
                <i class="fas fa-sun ip-icon-light" aria-hidden="true"></i>
            </button>
            <a href="<?= ip_h($u('pages/profile.php')) ?>" class="app-topbar__avatar-link" title="Profile" aria-label="Profile">
                <span class="user-monogram user-monogram--sm" id="topbar-initials" aria-hidden="true"><?= ip_h(ip_user_initials($currentUser ?? [])) ?></span>
            </a>
        </div>
    </div>
    <nav class="app-topbar__breadcrumbs" aria-label="Breadcrumb">
        <?php foreach ($crumbItems as $i => $c): ?>
            <?php if ($i > 0): ?>
                <span class="app-topbar__sep" aria-hidden="true">/</span>
            <?php endif; ?>
            <?php if ($c['href'] === null): ?>
                <span aria-current="page"><?= ip_h($c['label']) ?></span>
            <?php else: ?>
                <a href="<?= ip_h((string) $c['href']) ?>"><?= ip_h($c['label']) ?></a>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
</header>
