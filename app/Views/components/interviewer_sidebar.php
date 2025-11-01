<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
                <rect width="40" height="40" rx="8" fill="url(#gradient)"/>
                <path d="M20 10L28 16V24L20 30L12 24V16L20 10Z" fill="white"/>
                <defs>
                    <linearGradient id="gradient" x1="0" y1="0" x2="40" y2="40">
                        <stop offset="0%" stop-color="#667eea"/>
                        <stop offset="100%" stop-color="#764ba2"/>
                    </linearGradient>
                </defs>
            </svg>
            <span class="logo-text">RSD Interviewer</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="<?= base_url('interviewer/dashboard') ?>" class="nav-item <?= (uri_string() === 'interviewer/dashboard') ? 'active' : '' ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"/>
                <rect x="14" y="3" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/>
                <rect x="3" y="14" width="7" height="7"/>
            </svg>
            <span>Dashboard</span>
        </a>

        <a href="<?= base_url('interviewer/application') ?>" class="nav-item <?= (strpos(uri_string(), 'interviewer/application') !== false) ? 'active' : '' ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
            </svg>
            <span>Application Form</span>
        </a>

        <div class="sidebar-divider"></div>

        <a href="<?= base_url('auth/logout') ?>" class="nav-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            <span>Logout</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar-small"><?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?></div>
            <div class="user-details">
                <div class="user-name"><?= esc(session()->get('first_name')) ?> <?= esc(session()->get('last_name')) ?></div>
                <div class="user-role">Interviewer</div>
            </div>
        </div>
    </div>
</aside>
