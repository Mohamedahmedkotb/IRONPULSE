<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['email'])) {
    header('Location: ../html/login.html');
    exit;
}

$userName = $_SESSION['name'] ?? 'Athlete';
$userEmail = $_SESSION['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitTrack Pro - Profile</title>
    <link rel="stylesheet" href="../styles/Userprofile.css">
</head>
<body>
    <div class="layout-wrapper">
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="logo">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 18h12M6 6h12M2 12h20M4 12v-2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v2M4 12v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/>
                </svg>
                FitTrack Pro
            </div>
            <nav class="nav-links">
                <a href="Userprofile.php" class="active">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Overview
                </a>
                <a href="../html/Exerciselibrary.html">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8h1a4 4 0 0 1 0 8h-1"></path><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path><line x1="6" y1="1" x2="6" y2="4"></line><line x1="10" y1="1" x2="10" y2="4"></line><line x1="14" y1="1" x2="14" y2="4"></line></svg>
                    Exercises
                </a>
                <a href="#">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    History
                </a>
                <a href="#">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                    Analytics
                </a>
                <a href="../html/EliteCoaches.html">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    Coach
                </a>
                <a href="logout.php" style="margin-top: 20px; color: #ef4444;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    Logout
                </a>
            </nav>
            <div class="sidebar-bottom">
                <div class="user-status-card">
                    <img src="../assets/media/02e365b6-a962-484a-b83a-bfc51fd522d5.png" class="small-avatar" alt="Avatar">
                    <div class="status-info">
                        <h4>Performance</h4>
                        <p>Elite Status</p>
                    </div>
                </div>
                <button class="start-session-btn" id="startSessionBtn">▶ Start Session</button>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">
            <header class="page-header">
                <div class="header-titles">
                    <h1>Profile</h1>
                    <p>Manage your athlete profile and goals.</p>
                </div>
                <div class="header-actions">
                    <button class="btn-cancel">Cancel</button>
                    <button class="btn-save">Save Changes</button>
                </div>
            </header>

            <div class="dashboard-grid">
                <!-- LEFT COLUMN -->
                <div class="grid-col-left">
                    <!-- Profile Card -->
                    <div class="card profile-card fade-in-up" style="animation-delay: 0.1s">
                        <div class="avatar-wrapper">
                            <img src="../assets/media/02e365b6-a962-484a-b83a-bfc51fd522d5.png" alt="<?php echo htmlspecialchars($userName); ?>">
                            <button class="edit-avatar" aria-label="Edit avatar">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                        </div>
                        <h2><?php echo htmlspecialchars($userName); ?></h2>
                        <p class="membership-tier">Elite Member</p>
                        <div class="streak-badge">
                            <span class="dot pulse"></span> ACTIVE STREAK: 14 DAYS
                        </div>
                    </div>

                    <!-- Personal Details -->
                    <div class="card details-card fade-in-up" style="animation-delay: 0.2s">
                        <h3>Personal Details</h3>
                        <form>
                            <div class="input-group">
                                <label>EMAIL</label>
                                <input type="email" value="<?php echo htmlspecialchars($userEmail); ?>" readonly>
                            </div>
                            <div class="row">
                                <div class="input-group half">
                                    <label>HEIGHT</label>
                                    <div class="input-with-unit">
                                        <input type="number" value="175">
                                        <span class="unit">cm</span>
                                    </div>
                                </div>
                                <div class="input-group half">
                                    <label>WEIGHT</label>
                                    <div class="input-with-unit">
                                        <input type="number" value="68">
                                        <span class="unit">kg</span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- RIGHT COLUMN -->
                <div class="grid-col-right">
                    <div class="section-title fade-in-up" style="animation-delay: 0.3s">
                        <img src="../assets/media/e8df88e4-780b-4e88-a504-d61dd4b26347.png" class="icon" alt="Focus"> Current Focus
                    </div>
                    
                    <div class="focus-cards fade-in-up" style="animation-delay: 0.4s">
                        <!-- Active Focus -->
                        <div class="card focus-card active">
                            <div class="active-badge">ACTIVE</div>
                            <div class="focus-header">
                                <div class="focus-icon-circle blue">
                                    <img src="../assets/media/528ac074-5989-4b62-a559-9985555ea795.png" alt="Marathon Prep">
                                </div>
                                <div class="focus-info">
                                    <h4>Marathon Prep</h4>
                                    <p>12 Weeks Remaining</p>
                                </div>
                            </div>
                            <div class="progress-section">
                                <div class="progress-labels">
                                    <span>Progress</span>
                                    <span class="blue-text">45%</span>
                                </div>
                                <div class="progress-bar-bg">
                                    <div class="progress-bar-fill" style="width: 45%;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Secondary Focus -->
                        <div class="card focus-card secondary">
                            <div class="focus-header">
                                <div class="focus-icon-circle gray">
                                    <img src="../assets/media/5dc7e156-427e-456c-b251-797da94d6f9f.png" alt="Hypertrophy">
                                </div>
                                <div class="focus-info">
                                    <h4>Hypertrophy</h4>
                                    <p>Off-season</p>
                                </div>
                            </div>
                            <div class="progress-section">
                                <div class="progress-labels">
                                    <span>Progress</span>
                                    <span>Hold</span>
                                </div>
                                <div class="progress-bar-bg">
                                    <div class="progress-bar-fill gray" style="width: 100%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-title fade-in-up" style="animation-delay: 0.5s">
                        <span style="margin-right: 8px;">🏆</span> Trophy Cabinet
                    </div>
                    
                    <div class="card trophy-cabinet fade-in-up" style="animation-delay: 0.6s">
                        <div class="trophies-grid">
                            <!-- Speed Demon: lightning bolt -->
                            <div class="trophy-item">
                                <div class="trophy-icon orange">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                                    </svg>
                                </div>
                                <h4>Speed Demon</h4>
                                <p>Sub 6min Mile</p>
                            </div>
                            <!-- Iron Will: calendar with check -->
                            <div class="trophy-item">
                                <div class="trophy-icon blue">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                        <polyline points="9 16 11 18 15 14"></polyline>
                                    </svg>
                                </div>
                                <h4>Iron Will</h4>
                                <p>30 Day Streak</p>
                            </div>
                            <!-- Century: diamond -->
                            <div class="trophy-item">
                                <div class="trophy-icon gray">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 3h12l4 6-10 13L2 9z"></path>
                                        <path d="M2 9h20"></path>
                                        <path d="M6 3l4 6m4 0l4-6"></path>
                                        <path d="M10 9l2 13m0 0l2-13"></path>
                                    </svg>
                                </div>
                                <h4>Century</h4>
                                <p>100k Cycle</p>
                            </div>
                            <!-- Everest: mountain -->
                            <div class="trophy-item">
                                <div class="trophy-icon gray">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="12 2 2 20 22 20"></polygon>
                                        <polyline points="12 2 17 12 22 20"></polyline>
                                        <line x1="9" y1="14" x2="15" y2="14"></line>
                                    </svg>
                                </div>
                                <h4>Everest</h4>
                                <p>10k Elevation</p>
                            </div>
                        </div>
                    </div>

                    <!-- Danger Zone -->
                    <div class="danger-zone fade-in-up" style="animation-delay: 0.7s">
                        <h3 class="danger-title">Danger Zone</h3>
                        <p>Permanently remove your account and all associated workout data.</p>
                        <button class="btn-danger">Clear Data & Delete Account</button>
                    </div>

                </div>
            </div>
        </main>
    <!-- SESSION MODAL -->
    <div class="session-overlay" id="sessionOverlay">
        <div class="session-modal" id="sessionModal">
            <!-- Header -->
            <div class="session-modal-header">
                <div class="session-modal-title">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                    <span id="sessionModalTitle">New Session</span>
                </div>
                <button class="session-close-btn" id="sessionCloseBtn" aria-label="Close">&times;</button>
            </div>

            <!-- Setup View -->
            <div id="sessionSetupView">
                <p class="session-subtitle">Choose your workout type to begin tracking.</p>
                <div class="session-type-grid">
                    <button class="session-type-btn" data-type="Strength Training">
                        <span class="stype-icon">🏋️</span>
                        <span>Strength</span>
                    </button>
                    <button class="session-type-btn" data-type="Cardio">
                        <span class="stype-icon">🏃</span>
                        <span>Cardio</span>
                    </button>
                    <button class="session-type-btn" data-type="Flexibility">
                        <span class="stype-icon">🧘</span>
                        <span>Flexibility</span>
                    </button>
                    <button class="session-type-btn" data-type="HIIT">
                        <span class="stype-icon">⚡</span>
                        <span>HIIT</span>
                    </button>
                    <button class="session-type-btn" data-type="Calisthenics">
                        <span class="stype-icon">🤸</span>
                        <span>Calisthenics</span>
                    </button>
                    <button class="session-type-btn" data-type="Custom">
                        <span class="stype-icon">✏️</span>
                        <span>Custom</span>
                    </button>
                </div>
                <button class="session-start-confirm-btn" id="sessionStartConfirmBtn" disabled>Start Session</button>
            </div>

            <!-- Active Session View -->
            <div id="sessionActiveView" class="hidden">
                <div class="session-type-label" id="sessionTypeLabel"></div>
                <div class="session-timer" id="sessionTimer">00:00:00</div>
                <div class="session-status-pulse">
                    <span class="dot pulse"></span> Session in progress
                </div>
                <div class="session-stats">
                    <div class="session-stat">
                        <span class="session-stat-value" id="sessionCalories">0</span>
                        <span class="session-stat-label">Est. Calories</span>
                    </div>
                    <div class="session-stat">
                        <span class="session-stat-value" id="sessionSets">0</span>
                        <span class="session-stat-label">Sets Logged</span>
                    </div>
                </div>
                <div class="session-active-actions">
                    <button class="session-log-set-btn" id="sessionLogSetBtn">+ Log Set</button>
                    <button class="session-stop-btn" id="sessionStopBtn">⏹ Stop Session</button>
                </div>
            </div>

            <!-- Summary View -->
            <div id="sessionSummaryView" class="hidden">
                <div class="session-summary-icon">🎉</div>
                <h3 class="session-summary-title">Session Complete!</h3>
                <div class="session-summary-stats">
                    <div class="summary-stat">
                        <span class="summary-stat-value" id="summaryDuration">--</span>
                        <span class="summary-stat-label">Duration</span>
                    </div>
                    <div class="summary-stat">
                        <span class="summary-stat-value" id="summaryCalories">--</span>
                        <span class="summary-stat-label">Est. Calories</span>
                    </div>
                    <div class="summary-stat">
                        <span class="summary-stat-value" id="summarySets">--</span>
                        <span class="summary-stat-label">Sets Logged</span>
                    </div>
                </div>
                <button class="session-start-confirm-btn" id="sessionDoneBtn">Done</button>
            </div>
        </div>
    </div>

    <script src="../js/userprofile.js"></script>
</body>
</html>
