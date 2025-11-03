<?php $seg2 = service('uri')->getSegment(2); ?>
<aside class="sidebar">
    <button id="sidebarToggle" class="sidebar-toggle" type="button" aria-pressed="false">
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
            <h2><span>RSD Interviewer</span></h2>
        </div>
    </div>
    <nav class="sidebar-nav">
    <a href="<?= base_url('interviewer/dashboard') ?>" class="nav-item <?= (service('uri')->getSegment(2) === 'dashboard') ? 'active' : '' ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"/>
                <rect x="14" y="3" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/>
                <rect x="3" y="14" width="7" height="7"/>
            </svg>
            <span>Dashboard</span>
        </a>
    <a href="<?= base_url('interviewer/application') ?>" class="nav-item <?= ($seg2 === 'application') ? 'active' : '' ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
            </svg>
            <span>Application Form</span>
        </a>
    <a href="<?= base_url('interviewer/applications') ?>" class="nav-item <?= ($seg2 === 'applications') ? 'active' : '' ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="14" rx="2" ry="2"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <span>Application Forms</span>
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
