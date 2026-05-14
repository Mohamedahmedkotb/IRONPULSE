<?php

declare(strict_types=1);

?>
<div class="auth-header-inner">
    <a href="<?= ip_h(ip_url('index.php')) ?>" class="auth-header-logo" data-auth-home>
        <span class="auth-header-wordmark">Ironpulse</span>
    </a>
    <nav class="auth-header-nav" aria-label="Account">
        <a href="<?= ip_h(ip_url('pages/login.php')) ?>" class="auth-header-link" data-auth-login>Log in</a>
        <a href="<?= ip_h(ip_url('pages/signup.php')) ?>" class="ft-btn ft-btn--primary ft-btn--sm" data-auth-signup>Sign up</a>
    </nav>
</div>
