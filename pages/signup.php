<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/init.php';
ip_require_guest();

$pageTitle = 'Sign up';
$authPage = 'signup';
$authJsModules = ['signup'];
require_once dirname(__DIR__) . '/includes/header_auth.php';
?>
    <div class="auth-shell">
      <div class="auth-hero">
        <div class="auth-hero-content">
          <p class="eyebrow">IRONPULSE</p>
          <h1>Start your journey.</h1>
          <p>Join thousands of athletes who train with structured data, not guesswork.</p>
        </div>
      </div>
      <div class="auth-panel">
        <div class="auth-card ft-page-enter">
          <?php if ($e = ip_flash_get('error')): ?>
            <p class="ft-input-error" style="margin-bottom:1rem"><?= ip_h($e) ?></p>
          <?php endif; ?>
          <?php if ($s = ip_flash_get('success')): ?>
            <p style="margin-bottom:1rem;color:var(--success,#059669)"><?= ip_h($s) ?></p>
          <?php endif; ?>
          <div
            class="signup-steps"
            role="progressbar"
            aria-valuenow="1"
            aria-valuemin="1"
            aria-valuemax="3"
            aria-label="Signup progress"
          >
            <span class="signup-step is-active" data-step-indicator="1"></span>
            <span class="signup-step" data-step-indicator="2"></span>
            <span class="signup-step" data-step-indicator="3"></span>
          </div>

          <form id="signup-form" method="post" action="<?= ip_h(ip_url('actions/register_action.php')) ?>" data-php="1" novalidate>
            <?= ip_csrf_field() ?>
            <input type="hidden" name="fitness_goal" id="hid-fitness_goal" value="Strength" />
            <input type="hidden" name="gender" id="hid-gender" value="" />
            <div class="signup-panel" data-step="1">
              <h2>Create your account</h2>
              <p class="sub">Basic details to personalize your dashboard.</p>
              <div class="auth-field">
                <label class="ft-label" for="su-name">Full name</label>
                <div class="ft-input-wrap">
                  <i class="fas fa-user"></i>
                  <input class="ft-input" type="text" id="su-name" name="full_name" autocomplete="name" placeholder="Alex Rivera" required />
                </div>
                <p class="ft-input-error" id="su-name-err" hidden></p>
              </div>
              <div class="auth-field">
                <label class="ft-label" for="su-email">Email</label>
                <div class="ft-input-wrap">
                  <i class="fas fa-envelope"></i>
                  <input class="ft-input" type="email" id="su-email" name="email" autocomplete="email" placeholder="you@ironpulse.com" required />
                </div>
                <p class="ft-input-error" id="su-email-err" hidden></p>
              </div>
              <div class="auth-field" style="margin-bottom: 0">
                <button type="button" class="ft-btn ft-btn--primary ft-btn--block ft-btn--lg" data-next="2">Continue</button>
              </div>
            </div>

            <div class="signup-panel" data-step="2" hidden>
              <h2>Secure your profile</h2>
              <p class="sub">Use a strong password to protect your training data.</p>
              <div class="auth-field">
                <label class="ft-label" for="su-pass">Password</label>
                <div class="ft-input-wrap">
                  <i class="fas fa-lock"></i>
                  <input
                    class="ft-input ft-input--toggle-trailing"
                    type="password"
                    id="su-pass"
                    name="password"
                    autocomplete="new-password"
                    required
                  />
                  <button type="button" class="auth-toggle-pass" id="su-toggle-pass" aria-label="Show password">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
                <div class="strength-bar" aria-hidden="true"><span id="su-strength-fill"></span></div>
                <p class="strength-label" id="su-strength-label">Strength: —</p>
                <p class="ft-input-error" id="su-pass-err" hidden></p>
              </div>
              <div class="ft-flex auth-field" style="gap: 12px; margin-bottom: 0">
                <button type="button" class="ft-btn ft-btn--secondary ft-btn--block" data-next="1">Back</button>
                <button type="button" class="ft-btn ft-btn--primary ft-btn--block" data-next="3">Continue</button>
              </div>
            </div>

            <div class="signup-panel" data-step="3" hidden>
              <h2>Training focus</h2>
              <p class="sub">Pick what you want to emphasize first.</p>
              <div class="ft-pills auth-field" style="margin-bottom: 1.5rem">
                <button type="button" class="ft-pill is-active" data-goal="Strength">Strength</button>
                <button type="button" class="ft-pill" data-goal="Hypertrophy">Hypertrophy</button>
                <button type="button" class="ft-pill" data-goal="Endurance">Endurance</button>
                <button type="button" class="ft-pill" data-goal="Mobility">Mobility</button>
              </div>
              <div class="ft-flex" style="gap: 12px">
                <button type="button" class="ft-btn ft-btn--secondary ft-btn--block" data-next="2">Back</button>
                <button type="submit" class="ft-btn ft-btn--primary ft-btn--block ft-btn--ripple ft-btn--lg" id="su-submit">Create account</button>
              </div>
            </div>
          </form>

          <div class="auth-divider">OR CONTINUE WITH</div>
          <div class="auth-social">
            <button type="button" id="su-google">
              <img src="https://www.google.com/favicon.ico" alt="" width="18" height="18" />
              Google
            </button>
            <button type="button" id="su-apple"><i class="fab fa-apple"></i> Apple</button>
          </div>
          <p class="auth-footer">Already have an account? <a href="<?= ip_h(ip_url('pages/login.php')) ?>">Log in</a></p>
        </div>
      </div>
    </div>
<?php require_once dirname(__DIR__) . '/includes/footer_auth.php'; ?>
