<aside class="sidebar applicant-sidebar">
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
    </button>
    <div class="sidebar-header">
        <div class="logo">
            <svg width="40" height="40" viewBox="0 0 100 100">
                <circle cx="30" cy="30" r="20" fill="#c5c5c5"/>
                <circle cx="70" cy="30" r="20" fill="#dddddd"/>
                <circle cx="30" cy="70" r="20" fill="#dddddd"/>
                <circle cx="70" cy="70" r="20" fill="#c5c5c5"/>
                <rect x="35" y="35" width="30" height="30" fill="#fece83"/>
            </svg>
            <h2><span>RSD Portal</span></h2>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="<?= base_url('applicant/dashboard') ?>" class="nav-item <?= url_is('applicant/dashboard') ? 'active' : '' ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"/>
                <rect x="14" y="3" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/>
                <rect x="3" y="14" width="7" height="7"/>
            </svg>
            <span>Dashboard</span>
        </a>
        <?php if (!isset($application)): ?>
            <a href="<?= base_url('application/form') ?>" class="nav-item <?= url_is('application/form') ? 'active' : '' ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2v20M2 12h20"/>
                </svg>
                <span>Apply Now</span>
            </a>
        <?php elseif ($application && $application['status'] === 'approved'): ?>
            <a href="#" class="nav-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                </svg>
                <span>Learnings</span>
            </a>
        <?php endif; ?>
        <a href="#" class="nav-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
            <span>Profile</span>
        </a>
    </nav>
    <div class="sidebar-footer">
        <a href="<?= base_url('auth/logout') ?>" class="logout-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            <span>Logout</span>
        </a>
    </div>
</aside>
