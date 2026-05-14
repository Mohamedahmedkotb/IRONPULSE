<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
ip_require_guest();

$pageTitle = 'Log in';
$authPage = 'login';
$authJsModules = ['login'];
require_once dirname(__DIR__) . '/includes/header_auth.php';
?>
    <div class="auth-shell">
      <div class="auth-hero">
        <div class="auth-hero-content">
          <p class="eyebrow">IRONPULSE</p>
          <h1>Movement is fluid. Data is exact.</h1>
          <p>Precision metrics for elite performance. Continue your kinetic journey.</p>
        </div>
      </div>
      <div class="auth-panel">
        <div class="auth-card ft-page-enter">
          <h2>Welcome back</h2>
          <p class="sub">Enter your credentials to access your dashboard.</p>
            <?php if ($e = ip_flash_get('error')): ?>
                <p class="ft-input-error" id="php-flash-err" style="margin-bottom:1rem"><?= ip_h($e) ?></p>
            <?php endif; ?>
            <?php if ($o = ip_flash_get('success')): ?>
                <p style="margin-bottom:1rem;color:var(--success,#059669)" role="status"><?= ip_h($o) ?></p>
            <?php endif; ?>
          <form id="login-form" method="post" action="<?= ip_h(ip_url('actions/login_action.php')) ?>" data-php="1" novalidate>
            <?= ip_csrf_field() ?>
            <div class="auth-field">
              <label class="ft-label" for="login-email">Email</label>
              <div class="ft-input-wrap">
                <i class="fas fa-envelope"></i>
                <input
                  class="ft-input"
                  type="email"
                  id="login-email"
                  name="email"
                  autocomplete="email"
                  placeholder="you@ironpulse.com"
                  required
                />
              </div>
              <p class="ft-input-error" id="login-email-err" hidden></p>
            </div>
            <div class="auth-field">
              <div class="auth-row" style="margin-bottom: 0.5rem">
                <label class="ft-label" for="login-password" style="margin: 0">Password</label>
                <a href="#" id="login-forgot">Forgot password?</a>
              </div>
              <div class="ft-input-wrap">
                <i class="fas fa-lock"></i>
                <input
                  class="ft-input ft-input--toggle-trailing"
                  type="password"
                  id="login-password"
                  name="password"
                  autocomplete="current-password"
                  placeholder="Enter your password"
                  required
                />
                <button type="button" class="auth-toggle-pass" id="toggle-pass" aria-label="Show password">
                  <i class="fas fa-eye"></i>
                </button>
              </div>
              <p class="ft-input-error" id="login-pass-err" hidden></p>
            </div>
            <div class="auth-field">
              <div class="auth-row" style="margin-bottom: 0">
                <label class="auth-remember">
                  <input type="checkbox" id="remember" name="remember" value="1" />
                  <span>Remember me</span>
                </label>
              </div>
            </div>
            <div class="auth-field" style="margin-bottom: 0; margin-top: 0.25rem">
              <button type="submit" class="ft-btn ft-btn--primary ft-btn--block ft-btn--lg ft-btn--ripple" id="login-submit">
                Log in to dashboard
              </button>
            </div>
          </form>
          <div class="auth-divider">OR CONTINUE WITH</div>
          <div class="auth-social">
            <button type="button" id="social-google">
              <img src="https://www.google.com/favicon.ico" alt="" width="18" height="18" />
              Google
            </button>
            <button type="button" id="social-apple"><i class="fab fa-apple"></i> Apple</button>
          </div>
          <p class="auth-footer">New here? <a href="<?= ip_h(ip_url('pages/signup.php')) ?>">Create an account</a></p>
        </div>
      </div>
    </div>
<?php require_once dirname(__DIR__) . '/includes/footer_auth.php'; ?>
