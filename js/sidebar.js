class AppSidebar extends HTMLElement {
    constructor() {
        super();
    }

    connectedCallback() {
        // Determine the relative path to root depending on current page location
        const isHtmlDir = window.location.pathname.includes('/html/');
        const prefix = isHtmlDir ? '../' : '';

        // Get the current page to set active state
        const currentPath = window.location.pathname.split('/').pop() || 'index.html';

        this.innerHTML = `
        <nav class="sidebar">
            <div class="sidebar-header">
                <div class="brand-title">PERFORMANCE</div>
                <div class="badge">Elite Status</div>
            </div>
            
            <ul class="nav-links">
                <li class="${currentPath === 'index.html' || currentPath === '' ? 'active' : ''}"><a href="${prefix}index.html"><i class="fas fa-th-large"></i> Overview</a></li>
                <li class="${currentPath === 'Exerciselibrary.html' ? 'active' : ''}"><a href="${prefix}html/Exerciselibrary.html"><i class="fas fa-dumbbell"></i> Exercises</a></li>
                <li class="${currentPath === 'workoutlogger.html' ? 'active' : ''}"><a href="${prefix}html/workoutlogger.html"><i class="fas fa-history"></i> History</a></li>
                <li class="${currentPath === 'ProgressDashboard.html' || currentPath === 'FitTrackPro.html' ? 'active' : ''}"><a href="${prefix}html/ProgressDashboard.html"><i class="fas fa-chart-line"></i> Analytics</a></li>
                <li class="${currentPath === 'EliteCoaches.html' ? 'active' : ''}"><a href="${prefix}html/EliteCoaches.html"><i class="fas fa-user-md"></i> Coach</a></li>
            </ul>
            
            <div class="sidebar-footer">
                <button class="btn-primary btn-full"><i class="fas fa-play"></i> Start Session</button>
            </div>
        </nav>
        `;
    }
}

customElements.define('app-sidebar', AppSidebar);
