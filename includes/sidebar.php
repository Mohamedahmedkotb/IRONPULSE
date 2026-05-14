<?php
$nav = $currentNav ?? '';
$mk = static function (string $id, string $href, string $icon, string $label) use ($nav): string {
    $active = $nav === $id ? ' is-active' : '';
    $u = ip_url($href);
    return '<li><a class="app-sidebar__link' . $active . '" data-nav="' . ip_h($id) . '" href="' . ip_h($u) . '"><i class="fas ' . ip_h($icon) . '"></i> ' . ip_h($label) . '</a></li>';
};
?>
<aside id="app-sidebar" class="app-sidebar">
    <div class="app-sidebar__brand">
        <a href="<?= ip_h(ip_url('pages/dashboard.php')) ?>" class="app-sidebar__brand-link">
            <img class="app-sidebar__logo-img" src="<?= ip_h(ip_url('assets/images/favicon.svg')) ?>" alt="IronPulse" width="44" height="44">
            <div class="app-sidebar__titles">
                <h1>IronPulse</h1>
                <span>Performance OS</span>
            </div>
        </a>
    </div>
    <nav class="app-sidebar__nav" aria-label="Main">
        <p class="app-sidebar__section-label">Main</p>
        <ul>
            <?= $mk('home', 'pages/dashboard.php', 'fa-th-large', 'Dashboard') ?>
            <?= $mk('workouts', 'pages/workouts.php', 'fa-clipboard-list', 'Workouts') ?>
            <?= $mk('routines', 'pages/routines.php', 'fa-layer-group', 'Routines') ?>
            <?= $mk('exercises', 'pages/exercises.php', 'fa-dumbbell', 'Exercises') ?>
        </ul>
        <p class="app-sidebar__section-label">More</p>
        <ul>
            <?= $mk('coaches', 'pages/coaches.php', 'fa-user-tie', 'Coaches') ?>
            <?= $mk('meals', 'pages/meals.php', 'fa-utensils', 'Meal plans') ?>
            <?= $mk('profile', 'pages/profile.php', 'fa-user', 'Profile') ?>
            <?= $mk('settings', 'pages/settings.php', 'fa-gear', 'Settings') ?>
        </ul>
    </nav>
    <div class="app-sidebar__user">
        <span class="user-monogram" id="sidebar-initials" aria-hidden="true"><?= ip_h(ip_user_initials($currentUser ?? [])) ?></span>
        <div class="app-sidebar__user-meta">
            <strong id="sidebar-user-name"><?= ip_h($currentUser['full_name'] ?? 'Member') ?></strong>
            <span>Member</span>
        </div>
        <form method="post" action="<?= ip_h(ip_url('actions/logout.php')) ?>" style="margin:0;">
            <?= ip_csrf_field() ?>
            <button type="submit" class="btn-ghost-sidebar" title="Log out" aria-label="Log out"><i class="fas fa-right-from-bracket"></i></button>
        </form>
    </div>
</aside>
