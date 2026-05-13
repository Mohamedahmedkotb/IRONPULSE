/* ============================================================
   IRONPULSE — Userprofile.js
   Start Session modal logic
   ============================================================ */

(function () {
  'use strict';

  // ── Calorie burn rates per second (rough estimates per workout type)
  const CALORIE_RATES = {
    'Strength Training': 0.1167,  // ~7 kcal/min
    'Cardio':            0.1667,  // ~10 kcal/min
    'Flexibility':       0.0667,  // ~4 kcal/min
    'HIIT':              0.2000,  // ~12 kcal/min
    'Calisthenics':      0.1333,  // ~8 kcal/min
    'Custom':            0.1167,
  };

  // ── DOM refs
  const startBtn         = document.getElementById('startSessionBtn');
  const overlay          = document.getElementById('sessionOverlay');
  const closeBtn         = document.getElementById('sessionCloseBtn');
  const typeBtns         = document.querySelectorAll('.session-type-btn');
  const confirmBtn       = document.getElementById('sessionStartConfirmBtn');
  const setupView        = document.getElementById('sessionSetupView');
  const activeView       = document.getElementById('sessionActiveView');
  const summaryView      = document.getElementById('sessionSummaryView');
  const timerEl          = document.getElementById('sessionTimer');
  const caloriesEl       = document.getElementById('sessionCalories');
  const setsEl           = document.getElementById('sessionSets');
  const typeLabelEl      = document.getElementById('sessionTypeLabel');
  const logSetBtn        = document.getElementById('sessionLogSetBtn');
  const stopBtn          = document.getElementById('sessionStopBtn');
  const summaryDuration  = document.getElementById('summaryDuration');
  const summaryCalories  = document.getElementById('summaryCalories');
  const summarySets      = document.getElementById('summarySets');
  const doneBtn          = document.getElementById('sessionDoneBtn');
  const modalTitle       = document.getElementById('sessionModalTitle');

  // ── State
  let selectedType  = null;
  let timerInterval = null;
  let startTime     = null;
  let elapsedSecs   = 0;
  let sets          = 0;
  let calories      = 0;

  // ── Helpers
  function formatTime(secs) {
    const h = Math.floor(secs / 3600);
    const m = Math.floor((secs % 3600) / 60);
    const s = secs % 60;
    return [h, m, s].map(v => String(v).padStart(2, '0')).join(':');
  }

  function showView(view) {
    setupView.classList.add('hidden');
    activeView.classList.add('hidden');
    summaryView.classList.add('hidden');
    view.classList.remove('hidden');
  }

  function openModal() {
    resetSetup();
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeModal() {
    overlay.classList.remove('active');
    document.body.style.overflow = '';
    // If session still running, keep it but close modal
    if (!timerInterval) {
      resetAll();
    }
  }

  function resetSetup() {
    showView(setupView);
    modalTitle.textContent = 'New Session';
    typeBtns.forEach(b => b.classList.remove('selected'));
    confirmBtn.disabled = true;
    selectedType = null;
  }

  function resetAll() {
    clearInterval(timerInterval);
    timerInterval = null;
    elapsedSecs = 0;
    sets = 0;
    calories = 0;
    selectedType = null;
    timerEl.textContent = '00:00:00';
    caloriesEl.textContent = '0';
    setsEl.textContent = '0';
    startBtn.textContent = '▶ Start Session';
    startBtn.style.background = '';
  }

  // ── Type selection
  typeBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      typeBtns.forEach(b => b.classList.remove('selected'));
      btn.classList.add('selected');
      selectedType = btn.dataset.type;
      confirmBtn.disabled = false;
    });
  });

  // ── Start confirmed
  confirmBtn.addEventListener('click', () => {
    if (!selectedType) return;

    // Switch to active view
    showView(activeView);
    modalTitle.textContent = selectedType;
    typeLabelEl.textContent = selectedType;

    // Update sidebar button
    startBtn.textContent = '⏱ Session Active';
    startBtn.style.background = '#059669';

    // Start timer
    startTime = Date.now() - (elapsedSecs * 1000);
    timerInterval = setInterval(() => {
      elapsedSecs = Math.floor((Date.now() - startTime) / 1000);
      timerEl.textContent = formatTime(elapsedSecs);

      // Update calories
      calories = Math.round(elapsedSecs * (CALORIE_RATES[selectedType] || 0.1167));
      caloriesEl.textContent = calories;
    }, 1000);
  });

  // ── Log a set
  logSetBtn.addEventListener('click', () => {
    sets += 1;
    setsEl.textContent = sets;

    // Mini bounce feedback
    setsEl.animate([
      { transform: 'scale(1.4)', color: '#3b82f6' },
      { transform: 'scale(1)',   color: '#111827' }
    ], { duration: 300, easing: 'cubic-bezier(0.34,1.56,0.64,1)' });
  });

  // ── Stop session
  stopBtn.addEventListener('click', () => {
    clearInterval(timerInterval);
    timerInterval = null;

    // Populate summary
    summaryDuration.textContent  = formatTime(elapsedSecs);
    summaryCalories.textContent  = calories;
    summarySets.textContent      = sets;

    showView(summaryView);
    modalTitle.textContent = 'Session Complete';
  });

  // ── Done (close summary)
  doneBtn.addEventListener('click', () => {
    closeModal();
    resetAll();
    showView(setupView);
  });

  // ── Open modal
  startBtn.addEventListener('click', () => {
    if (timerInterval) {
      // Session running — reopen active view
      overlay.classList.add('active');
      document.body.style.overflow = 'hidden';
      showView(activeView);
      modalTitle.textContent = selectedType;
    } else {
      openModal();
    }
  });

  // ── Close via X or overlay click
  closeBtn.addEventListener('click', closeModal);
  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) closeModal();
  });

  // ── ESC key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && overlay.classList.contains('active')) {
      closeModal();
    }
  });

})();
