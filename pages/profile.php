<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
ip_require_login();

$currentUser = ip_current_user($pdo);
if (!$currentUser) {
    session_destroy();
    ip_redirect('pages/login.php');
}

$pageTitle = 'Profile';
$currentNav = 'profile';

require_once dirname(__DIR__) . '/includes/header.php';
require_once dirname(__DIR__) . '/includes/sidebar.php';
require_once dirname(__DIR__) . '/includes/navbar.php';

$av = (string) ($currentUser['avatar'] ?? '');
$avUrl = $av !== '' ? (str_starts_with($av, 'http') ? $av : ip_url($av)) : '';
?>
<main class="app-main">
    <div class="ft-surface" style="max-width:720px;border-radius:var(--radius-xl);border:1px solid var(--border);padding:var(--space-6)">
        <h1 style="margin-top:0">Profile</h1>
        <p class="ft-muted">Update your details. Changes save with a normal form POST.</p>

        <form method="post" action="<?= ip_h(ip_url('actions/update_profile.php')) ?>" enctype="multipart/form-data" style="margin-top:var(--space-5)">
            <?= ip_csrf_field() ?>
            <div style="display:flex;gap:var(--space-5);flex-wrap:wrap;margin-bottom:var(--space-5);align-items:center">
                <div style="width:96px;height:96px;border-radius:50%;overflow:hidden;background:var(--page-bg);border:1px solid var(--border)">
                    <?php if ($avUrl !== ''): ?>
                        <img src="<?= ip_h($avUrl) ?>" alt="" style="width:100%;height:100%;object-fit:cover">
                    <?php else: ?>
                        <div style="display:grid;place-items:center;height:100%;font-weight:800;font-size:1.5rem;color:var(--accent)"><?= ip_h(ip_user_initials($currentUser)) ?></div>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="ft-label" for="avatar">Avatar (JPEG/PNG/WebP, max 2MB)</label>
                    <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp">
                </div>
            </div>

            <div class="auth-field" style="margin-bottom:1rem">
                <label class="ft-label" for="full_name">Full name</label>
                <input class="ft-input" style="width:100%" id="full_name" name="full_name" required value="<?= ip_h((string) ($currentUser['full_name'] ?? '')) ?>">
            </div>
            <div class="auth-field" style="margin-bottom:1rem">
                <label class="ft-label" for="email">Email</label>
                <input class="ft-input" style="width:100%" id="email" name="email" type="email" required value="<?= ip_h((string) ($currentUser['email'] ?? '')) ?>">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                <div>
                    <label class="ft-label" for="gender">Gender</label>
                    <input class="ft-input" style="width:100%" id="gender" name="gender" value="<?= ip_h((string) ($currentUser['gender'] ?? '')) ?>">
                </div>
                <div>
                    <label class="ft-label" for="age">Age</label>
                    <input class="ft-input" style="width:100%" id="age" name="age" type="number" min="0" max="150" value="<?= ($currentUser['age'] ?? null) !== null && $currentUser['age'] !== '' ? (int) $currentUser['age'] : '' ?>">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:1rem">
                <div>
                    <label class="ft-label" for="height">Height (cm)</label>
                    <input class="ft-input" style="width:100%" id="height" name="height" step="0.1" value="<?= ($currentUser['height'] ?? null) !== null && (string) $currentUser['height'] !== '' ? ip_h((string) $currentUser['height']) : '' ?>">
                </div>
                <div>
                    <label class="ft-label" for="weight">Weight (kg)</label>
                    <input class="ft-input" style="width:100%" id="weight" name="weight" step="0.1" value="<?= ($currentUser['weight'] ?? null) !== null && (string) $currentUser['weight'] !== '' ? ip_h((string) $currentUser['weight']) : '' ?>">
                </div>
            </div>
            <div class="auth-field" style="margin-top:1rem">
                <label class="ft-label" for="activity_level">Activity level</label>
                <input class="ft-input" style="width:100%" id="activity_level" name="activity_level" value="<?= ip_h((string) ($currentUser['activity_level'] ?? '')) ?>">
            </div>
            <div class="auth-field" style="margin-top:1rem">
                <label class="ft-label" for="fitness_goal">Fitness goal</label>
                <input class="ft-input" style="width:100%" id="fitness_goal" name="fitness_goal" value="<?= ip_h((string) ($currentUser['fitness_goal'] ?? '')) ?>">
            </div>
            <div class="auth-field" style="margin-top:1rem">
                <label class="ft-label" for="phone">Phone</label>
                <input class="ft-input" style="width:100%" id="phone" name="phone" value="<?= ip_h((string) ($currentUser['phone'] ?? '')) ?>">
            </div>
            <div class="auth-field" style="margin-top:1rem">
                <label class="ft-label" for="city">City</label>
                <input class="ft-input" style="width:100%" id="city" name="city" value="<?= ip_h((string) ($currentUser['city'] ?? '')) ?>">
            </div>
            <div class="auth-field" style="margin-top:1rem">
                <label class="ft-label" for="bio">Bio</label>
                <textarea class="ft-input" style="width:100%;min-height:100px" id="bio" name="bio"><?= ip_h((string) ($currentUser['bio'] ?? '')) ?></textarea>
            </div>
            <button type="submit" class="ft-btn ft-btn--primary ft-btn--lg" style="margin-top:var(--space-4)">Save profile</button>
        </form>
    </div>
</main>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
