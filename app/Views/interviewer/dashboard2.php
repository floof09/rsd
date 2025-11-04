<?php $title = 'Interviewer Dashboard • RSD'; ?>
<?= view('layouts/header', ['title' => $title, 'bodyClass' => 'interviewer-dashboard']) ?>

<?= view('components/interviewer_sidebar') ?>

<main class="main-content">
    <header class="top-bar">
        <h1>Interviewer Dashboard</h1>
        <div class="user-info" style="gap:8px;">
            <span>Welcome, <?= esc(session()->get('first_name')) ?> <?= esc(session()->get('last_name')) ?></span>
            <div class="user-avatar"><?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?></div>
        </div>
    </header>

    <div class="dashboard-content">
        <div class="hero">
            <div>
                <h1>Good day, <?= esc(session()->get('first_name')) ?> 👋</h1>
                <div class="hint">Track your applications and log IGT interviews faster with the quick tools below.</div>
            </div>
            <a href="<?= base_url('interviewer/application') ?>" class="btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                New Application
            </a>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success" style="margin-top:14px;"> <?= session()->getFlashdata('success') ?> </div>
        <?php endif; ?>

        <section class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-top">
                    <div class="kpi-ico">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="8.5" cy="7" r="4"/>
                            <polyline points="17 11 19 13 23 9"/>
                        </svg>
                    </div>
                    <div class="kpi-trend">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M18 15l-6-6-6 6"/></svg>
                        12%
                    </div>
                </div>
                <div class="kpi-meta">
                    <h3><?= (int)($total_applications ?? 0) ?></h3>
                    <p>Total Applications</p>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-top">
                    <div class="kpi-ico kpi-ico--green">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <line x1="9" y1="3" x2="9" y2="21"/>
                        </svg>
                    </div>
                    <div class="kpi-trend">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M18 15l-6-6-6 6"/></svg>
                        8%
                    </div>
                </div>
                <div class="kpi-meta">
                    <h3><?= (int)($rsd_applications ?? 0) ?></h3>
                    <p>RSD Applications</p>
                </div>
            </div>

            <div class="kpi-card">
                <div class="kpi-top">
                    <div class="kpi-ico kpi-ico--cyan">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 7h-9"/>
                            <path d="M14 17H5"/>
                            <circle cx="17" cy="17" r="3"/>
                            <circle cx="7" cy="7" r="3"/>
                        </svg>
                    </div>
                    <div class="kpi-trend">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M18 15l-6-6-6 6"/></svg>
                        5%
                    </div>
                </div>
                <div class="kpi-meta">
                    <h3><?= (int)($igt_applications ?? 0) ?></h3>
                    <p>IGT Applications</p>
                </div>
            </div>
        </section>

        <section class="panel-grid">
            <div class="panel" style="grid-column: span 7; min-width:0;">
                <h3>Quick Actions</h3>
                <div class="action-grid">
                    <a class="action-card" href="<?= base_url('interviewer/application') ?>">
                        <div class="action-ico">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                        </div>
                        <strong>Create Application</strong>
                    </a>
                    <a class="action-card" href="<?= base_url('interviewer/applications') ?>">
                        <div class="action-ico">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <strong>My Applications</strong>
                    </a>
                    <a class="action-card" href="<?= base_url('interviewer/applications') ?>?filter=igt">
                        <div class="action-ico">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        </div>
                        <strong>IGT Candidates</strong>
                    </a>
                </div>
                <div class="divider"></div>
                <div class="hint">💡 Tip: Open an application and use "➕ IGT Interview" for IGT companies to record the additional interview.</div>
            </div>
            <div class="panel" style="grid-column: span 5; min-width:0;">
                <h3>Recently Updated</h3>
                <?php if (!empty($recent_applications) && is_array($recent_applications)): ?>
                    <div class="activity">
                        <?php foreach ($recent_applications as $ra): ?>
                            <div class="activity-row">
                                <div class="activity-avatar"><?= strtoupper(substr($ra['first_name'] ?? 'U', 0, 1)) ?></div>
                                <div class="activity-info">
                                    <div class="activity-title">#<?= str_pad((int)($ra['id'] ?? 0), 4, '0', STR_PAD_LEFT) ?> · <?= esc(($ra['first_name'] ?? '') . ' ' . ($ra['last_name'] ?? '')) ?></div>
                                    <div class="activity-meta"><?= esc($ra['company_name'] ?? '—') ?> • <?= !empty($ra['updated_at']) ? date('M d, H:i', strtotime($ra['updated_at'])) : '—' ?></div>
                                </div>
                                <div class="activity-badge"><?= esc(str_replace('_',' ', $ra['status'] ?? 'pending')) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="hint">No recent updates yet.</div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</main>

<?= view('layouts/footer') ?>
