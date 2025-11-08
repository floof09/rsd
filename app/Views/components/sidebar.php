<?php
$userType = session()->get('user_type');
$isAdmin = ($userType === 'admin');
$isInterviewer = ($userType === 'interviewer');
$seg2 = service('uri')->getSegment(2);
// Helper to render an item
function navItem($href,$isActive,$label,$iconSvg){
    $active = $isActive ? ' active' : '';
    $current = $isActive ? ' aria-current="page"' : '';
    return '<a href="'.esc($href).'" class="sb-link'.$active.'" data-tip="'.esc($label).'"'.$current.'>'.$iconSvg.'<span class="nav-label">'.esc($label).'</span></a>';
}
?>
<aside class="app-sidebar" aria-label="Primary navigation">
    <div class="sb-top">
        <div class="brand" role="banner">
            <div class="brand-logo">
                <svg width="40" height="40" viewBox="0 0 100 100" aria-hidden="true">
                    <circle cx="30" cy="30" r="20" fill="#c5c5c5"/>
                    <circle cx="70" cy="30" r="20" fill="#dddddd"/>
                    <circle cx="30" cy="70" r="20" fill="#dddddd"/>
                    <circle cx="70" cy="70" r="20" fill="#c5c5c5"/>
                    <rect x="35" y="35" width="30" height="30" fill="#fece83"/>
                </svg>
            </div>
            <div class="brand-text">
                <span class="brand-title">RSD</span>
                <span class="brand-sub"><?= $isAdmin ? 'Admin' : ($isInterviewer ? 'Interviewer' : '') ?></span>
            </div>
        </div>
        <button id="sidebarToggle" class="sb-toggle" type="button" aria-label="Collapse sidebar" aria-expanded="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
    </div>
    <nav class="sb-nav" role="navigation">
        <?php if ($isAdmin): ?>
            <div class="sb-section" aria-label="Core">
                <div class="sb-section-label">Core</div>
                <?= navItem(base_url('admin/dashboard'), url_is('admin/dashboard'), 'Dashboard', '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>'); ?>
                <?= navItem(base_url('admin/applications'), url_is('admin/applications'), 'Applications', '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>'); ?>
                <?= navItem(base_url('admin/recruiters'), url_is('admin/recruiters*'), 'Recruiters', '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>'); ?>
            </div>
            <div class="sb-section" aria-label="Management">
                <div class="sb-section-label">Manage</div>
                <?= navItem(base_url('admin/system-logs'), url_is('admin/system-logs*'), 'System Logs', '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>'); ?>
                <?= navItem(base_url('admin/companies'), url_is('admin/companies*'), 'Companies', '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>'); ?>
                <?= navItem(base_url('admin/email/test'), url_is('admin/email/test'), 'Email Test', '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2" ry="2"/><polyline points="3 7 12 13 21 7"/></svg>'); ?>
                <?= navItem(base_url('admin/reports'), url_is('admin/reports*'), 'Reports', '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>'); ?>
            </div>
        <?php elseif ($isInterviewer): ?>
            <div class="sb-section" aria-label="Interviewer">
                <div class="sb-section-label">Overview</div>
                <?= navItem(base_url('interviewer/dashboard'), ($seg2==='dashboard'), 'Dashboard', '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>'); ?>
                <?= navItem(base_url('interviewer/application'), ($seg2==='application'), 'Apply', '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>'); ?>
                <?= navItem(base_url('interviewer/applications'), ($seg2==='applications'), 'Applications', '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="14" rx="2" ry="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>'); ?>
            </div>
        <?php endif; ?>
    </nav>
    <div class="sb-footer">
        <a href="<?= base_url('auth/logout') ?>" class="sb-link sb-logout" data-tip="Logout">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            <span class="nav-label">Logout</span>
        </a>
    </div>
</aside>

<link rel="stylesheet" href="<?= base_url('assets/css/sidebar.css') ?>?v=<?= time() ?>">
<script src="<?= base_url('assets/js/sidebar.js') ?>?v=<?= time() ?>" defer></script>
