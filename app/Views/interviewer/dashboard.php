<?php $title = 'Interviewer Dashboard • RSD'; ?>
<?= view('layouts/header', ['title' => $title, 'bodyClass' => 'interviewer-dashboard']) ?>
<?= view('components/sidebar') ?>

<main class="main-content" role="main">
    <header class="top-bar">
        <h1>Interviewer Dashboard</h1>
        <div class="user-info">
            <span>Welcome, <?= esc(session()->get('first_name')) ?> <?= esc(session()->get('last_name')) ?></span>
            <div class="user-avatar" aria-hidden="true"><?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?></div>
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
            <div class="alert alert-success" style="margin-top:14px;"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <section class="kpi-grid" aria-label="Key performance indicators">
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

        <section class="panel-grid" aria-label="Recent activity and actions">
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
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 0 2-2h2a2 2 0 0 0 2 2"/></svg>
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

<?= view('components/sidebar_script') ?>
<?= view('layouts/footer') ?>
    <header class="top-bar">
        <h1>Interviewer Dashboard</h1>
        <div class="user-info" style="gap:8px;">
            <button id="themeBtn" class="theme-toggle" type="button" aria-pressed="false" aria-label="Toggle theme">
                <svg id="sunIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
                <svg id="moonIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                Theme
            </button>
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

<?= view('components/sidebar_script') ?>
<?= view('layouts/footer') ?>
<?php $title = 'Interviewer Dashboard • RSD'; ?>
<?= view('layouts/header', ['title' => $title, 'bodyClass' => 'interviewer-dashboard']) ?>

<?= view('components/sidebar') ?>

<main class="main-content">
    <header class="top-bar">
        <h1>Interviewer Dashboard</h1>
        <div class="user-info" style="gap:8px;">
            <button id="themeBtn" class="theme-toggle" type="button" aria-pressed="false" aria-label="Toggle theme">
                <svg id="sunIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
                <svg id="moonIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                Theme
            </button>
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
            <div class="alert alert-success" style="margin-top:14px;"><?= session()->getFlashdata('success') ?></div>
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
<!DOCTYPE html><!DOCTYPE html><!DOCTYPE html><!DOCTYPE html>

<html lang="en">

<head><html lang="en">

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0"><head><html lang="en"><html lang="en">

    <title>Interviewer Dashboard • RSD</title>

    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">    <meta charset="UTF-8">

    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">    <meta name="viewport" content="width=device-width, initial-scale=1.0"><head><head>

    <style>

        :root {    <title>Interviewer Dashboard • RSD</title>

            --ink: #0f172a;

            --muted: #64748b;    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">    <meta charset="UTF-8">    <meta charset="UTF-8">

            --border: #e2e8f0;

            --bg: #f8fafc;    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">

            --card: #ffffff;

            --brandA: #f59e0b;    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <meta name="viewport" content="width=device-width, initial-scale=1.0">

            --brandB: #f97316;

            --accent: #6366f1;    <style>

            --ok: #10b981;

            --warn: #f59e0b;        :root {    <title>Interviewer Dashboard • RSD</title>    <title>Interviewer Dashboard • RSD</title>

            --shadow: 0 1px 3px rgba(0,0,0,.04), 0 10px 25px rgba(0,0,0,.06);

        }            --ink: #0f172a;



        .theme-dark {            --muted: #64748b;    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">

            --ink: #f1f5f9;

            --muted: #94a3b8;            --border: #e2e8f0;

            --border: #1e293b;

            --bg: #0f172a;            --bg: #f8fafc;    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">

            --card: #1e293b;

            --shadow: 0 1px 3px rgba(0,0,0,.2), 0 10px 25px rgba(0,0,0,.3);            --card: #ffffff;

        }

            --brandA: #f59e0b;    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        .dashboard-content {

            background: linear-gradient(180deg, rgba(99,102,241,.02) 0%, transparent 40%);            --brandB: #f97316;

            position: relative;

        }            --accent: #6366f1;    <style>    <style>



        .dashboard-content::before {            --ok: #10b981;

            content: "";

            position: fixed;            --warn: #f59e0b;        :root{        :root{

            inset: 0;

            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239C92AC' fill-opacity='0.02'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");            --shadow: 0 1px 3px rgba(0,0,0,.04), 0 10px 25px rgba(0,0,0,.06);

            pointer-events: none;

            opacity: .6;        }            --ink:#0f172a; --muted:#64748b; --border:#e2e8f0; --bg:#f8fafc; --card:#ffffff;            --ink:#111827; --muted:#6b7280; --border:#e5e7eb; --bg:#f9fafb; --card:#ffffff;

            z-index: -1;

        }        .theme-dark {



        .hero {            --ink: #f1f5f9;            --brandA:#f59e0b; --brandB:#f97316; --accent:#6366f1; --ok:#10b981; --warn:#f59e0b;            --brandA:#f59e0b; --brandB:#f6ad55; --accent:#6366f1; --ok:#10b981; --warn:#f59e0b;

            position: relative;

            border-radius: 20px;            --muted: #94a3b8;

            padding: 32px 28px;

            display: flex;            --border: #1e293b;            --shadow:0 1px 3px rgba(0,0,0,.04), 0 10px 25px rgba(0,0,0,.06);            --shadow:0 10px 30px rgba(0,0,0,.06);

            align-items: center;

            justify-content: space-between;            --bg: #0f172a;

            gap: 18px;

            background: rgba(255, 255, 255, .7);            --card: #1e293b;        }        }

            backdrop-filter: blur(12px);

            border: 1px solid rgba(255,255,255,.6);            --shadow: 0 1px 3px rgba(0,0,0,.2), 0 10px 25px rgba(0,0,0,.3);

            box-shadow: 0 8px 32px rgba(0,0,0,.06);

            overflow: hidden;        }        .theme-dark{        .theme-dark{

        }

        .dashboard-content {

        .theme-dark .hero {

            background: rgba(30, 41, 59, .6);            background: linear-gradient(180deg, rgba(99,102,241,.02) 0%, transparent 40%);            --ink:#f1f5f9; --muted:#94a3b8; --border:#1e293b; --bg:#0f172a; --card:#1e293b;            --ink:#e5e7eb; --muted:#9ca3af; --border:#323846; --bg:#0f172a; --card:#101827;

            border-color: rgba(100,116,139,.2);

        }            position: relative;



        .hero::after {        }            --shadow: 0 1px 3px rgba(0,0,0,.2), 0 10px 25px rgba(0,0,0,.3);            --shadow: 0 10px 30px rgba(0,0,0,.4);

            content: "";

            position: absolute;        .dashboard-content::before {

            top: -40%;

            right: -10%;            content: "";        }        }

            width: 300px;

            height: 300px;            position: fixed;

            background: radial-gradient(circle, rgba(99,102,241,.15), transparent 70%);

            pointer-events: none;            inset: 0;        .dashboard-content{ background: linear-gradient(180deg, rgba(99,102,241,.02) 0%, transparent 40%); position:relative; }        .hero {

        }

            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239C92AC' fill-opacity='0.02'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");

        .hero h1 {

            font-weight: 800;            pointer-events: none;        .dashboard-content::before{ content:""; position:fixed; inset:0; background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239C92AC' fill-opacity='0.02'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"); pointer-events:none; opacity:.6; z-index:-1; }            position: relative;

            font-size: 28px;

            letter-spacing: -.3px;            opacity: .6;

            color: var(--ink);

            margin: 0;            z-index: -1;        .hero {            border-radius: 16px;

        }

        }

        .hero .hint {

            color: var(--muted);        .hero {            position: relative;            padding: 18px 18px;

            font-size: 14px;

            margin-top: 6px;            position: relative;

            line-height: 1.5;

        }            border-radius: 20px;            border-radius: 20px;            display:flex; align-items:center; justify-content:space-between; gap:14px;



        .kpi-grid {            padding: 32px 28px;

            display: grid;

            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));            display: flex;            padding: 32px 28px;            background: var(--card);

            gap: 16px;

            margin-top: 18px;            align-items: center;

        }

            justify-content: space-between;            display:flex; align-items:center; justify-content:space-between; gap:18px;        }

        .kpi-card {

            position: relative;            gap: 18px;

            background: var(--card);

            border: 1px solid var(--border);            background: rgba(255, 255, 255, .7);            background: rgba(255, 255, 255, .7);        .hero::before{ content:""; position:absolute; inset:0; border-radius:16px; padding:1px; background:linear-gradient(90deg, var(--brandA), var(--accent)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite:xor; mask-composite: exclude; }

            border-radius: 16px;

            padding: 20px;            backdrop-filter: blur(12px);

            display: flex;

            flex-direction: column;            border: 1px solid rgba(255,255,255,.6);            backdrop-filter: blur(12px);        .hero h1 { font-weight:800; letter-spacing:.2px; color:var(--ink); margin:0; }

            gap: 12px;

            box-shadow: var(--shadow);            box-shadow: 0 8px 32px rgba(0,0,0,.06);

            transition: all .2s cubic-bezier(.4,0,.2,1);

        }            overflow: hidden;            border: 1px solid rgba(255,255,255,.6);        .hero .hint { color:var(--muted); font-size:13px; margin-top:4px; }



        .kpi-card:hover {        }

            transform: translateY(-3px);

            box-shadow: 0 12px 40px rgba(0,0,0,.1);        .theme-dark .hero {            box-shadow: 0 8px 32px rgba(0,0,0,.06);    .kpi-grid{ display:grid; grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); gap:14px; margin-top:14px; }

            border-color: rgba(99,102,241,.3);

        }            background: rgba(30, 41, 59, .6);



        .kpi-top {            border-color: rgba(100,116,139,.2);            overflow:hidden;    .kpi-card{ position:relative; background:var(--card); border:1px solid var(--border); border-radius:14px; padding:14px; display:flex; gap:12px; align-items:center; box-shadow:var(--shadow); transition: transform .12s ease, box-shadow .12s ease; overflow:hidden; }

            display: flex;

            align-items: center;        }

            justify-content: space-between;

        }        .hero::after {        }    .kpi-card::after{ content:""; position:absolute; left:0; top:0; bottom:0; width:6px; background: linear-gradient(180deg, var(--brandA), var(--accent)); opacity:.9; }



        .kpi-ico {            content: "";

            width: 48px;

            height: 48px;            position: absolute;        .theme-dark .hero{ background: rgba(30, 41, 59, .6); border-color: rgba(100,116,139,.2); }    .kpi-card:hover{ transform: translateY(-2px); box-shadow:0 16px 40px rgba(0,0,0,.08); }

            border-radius: 14px;

            display: grid;            top: -40%;

            place-items: center;

            color: #fff;            right: -10%;        .hero::after{ content:""; position:absolute; top:-40%; right:-10%; width:300px; height:300px; background: radial-gradient(circle, rgba(99,102,241,.15), transparent 70%); pointer-events:none; }    .kpi-ico{ width:42px; height:42px; border-radius:12px; display:grid; place-items:center; color:#fff; background: linear-gradient(135deg, var(--accent), #8b5cf6); }

            background: linear-gradient(135deg, var(--accent), #8b5cf6);

            box-shadow: 0 4px 14px rgba(99,102,241,.3);            width: 300px;

        }

            height: 300px;        .hero h1 { font-weight:800; font-size:28px; letter-spacing:-.3px; color:var(--ink); margin:0; }    .kpi-meta h3{ margin:0; font-size:28px; font-weight:900; color:var(--ink); letter-spacing:.2px; }

        .kpi-trend {

            display: inline-flex;            background: radial-gradient(circle, rgba(99,102,241,.15), transparent 70%);

            align-items: center;

            gap: 4px;            pointer-events: none;        .hero .hint { color:var(--muted); font-size:14px; margin-top:6px; line-height:1.5; }    .kpi-meta p{ margin:2px 0 0; font-size:11px; color:var(--muted); font-weight:700; letter-spacing:.2px; text-transform:uppercase; }

            padding: 4px 8px;

            border-radius: 999px;        }

            font-size: 11px;

            font-weight: 700;        .hero h1 {        .kpi-grid{ display:grid; grid-template-columns: repeat(auto-fit,minmax(240px,1fr)); gap:16px; margin-top:18px; }        .panel-grid{ display:grid; grid-template-columns:repeat(12, 1fr); gap:14px; margin-top:14px; }

            background: #ecfdf5;

            color: #065f46;            font-weight: 800;

        }

            font-size: 28px;        .kpi-card{ position:relative; background:var(--card); border:1px solid var(--border); border-radius:16px; padding:20px; display:flex; flex-direction:column; gap:12px; box-shadow:var(--shadow); transition: all .2s cubic-bezier(.4,0,.2,1); }        .panel{ background:var(--card); border:1px solid var(--border); border-radius:14px; padding:16px; box-shadow:var(--shadow); }

        .kpi-meta h3 {

            margin: 0;            letter-spacing: -.3px;

            font-size: 36px;

            font-weight: 900;            color: var(--ink);        .kpi-card:hover{ transform: translateY(-3px); box-shadow:0 12px 40px rgba(0,0,0,.1); border-color: rgba(99,102,241,.3); }        .panel h3{ margin:0 0 10px; font-size:15px; letter-spacing:.2px; color:var(--ink); font-weight:800; }

            color: var(--ink);

            letter-spacing: -.5px;            margin: 0;

            line-height: 1;

        }        }        .kpi-top{ display:flex; align-items:center; justify-content:space-between; }    .quick-grid{ display:grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap:10px; }



        .kpi-meta p {        .hero .hint {

            margin: 6px 0 0;

            font-size: 12px;            color: var(--muted);        .kpi-ico{ width:48px; height:48px; border-radius:14px; display:grid; place-items:center; color:#fff; background: linear-gradient(135deg, var(--accent), #8b5cf6); box-shadow: 0 4px 14px rgba(99,102,241,.3); }    .qbtn{ display:flex; align-items:center; gap:10px; padding:12px 14px; border:1px solid var(--border); border-radius:999px; text-decoration:none; color:var(--ink); background:var(--card); font-weight:800; }

            color: var(--muted);

            font-weight: 600;            font-size: 14px;

            letter-spacing: .2px;

            text-transform: uppercase;            margin-top: 6px;        .kpi-trend{ display:inline-flex; align-items:center; gap:4px; padding:4px 8px; border-radius:999px; font-size:11px; font-weight:700; background: #ecfdf5; color:#065f46; }    .qbtn:hover{ border-color: transparent; background: linear-gradient(#fff, #fff) padding-box, linear-gradient(90deg, var(--brandA), var(--accent)) border-box; }

        }

            line-height: 1.5;

        .panel-grid {

            display: grid;        }        .kpi-meta h3{ margin:0; font-size:36px; font-weight:900; color:var(--ink); letter-spacing:-.5px; line-height:1; }        .badge{ display:inline-block; padding:4px 8px; font-size:11px; font-weight:700; border-radius:999px; }

            grid-template-columns: repeat(12, 1fr);

            gap: 16px;        .kpi-grid {

            margin-top: 18px;

        }            display: grid;        .kpi-meta p{ margin:6px 0 0; font-size:12px; color:var(--muted); font-weight:600; letter-spacing:.2px; text-transform:uppercase; }        .b-ok{ background:#ecfdf5; color:#065f46; }



        .panel {            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));

            background: var(--card);

            border: 1px solid var(--border);            gap: 16px;        .panel-grid{ display:grid; grid-template-columns:repeat(12, 1fr); gap:16px; margin-top:18px; }        .b-warn{ background:#fffbeb; color:#92400e; }

            border-radius: 16px;

            padding: 20px;            margin-top: 18px;

            box-shadow: var(--shadow);

        }        }        .panel{ background:var(--card); border:1px solid var(--border); border-radius:16px; padding:20px; box-shadow:var(--shadow); }    .list{ display:flex; flex-direction:column; }



        .panel h3 {        .kpi-card {

            margin: 0 0 14px;

            font-size: 16px;            position: relative;        .panel h3{ margin:0 0 14px; font-size:16px; letter-spacing:-.2px; color:var(--ink); font-weight:800; }    .row{ display:flex; align-items:center; justify-content:space-between; padding:12px 4px; border-bottom:1px solid var(--border); }

            letter-spacing: -.2px;

            color: var(--ink);            background: var(--card);

            font-weight: 800;

        }            border: 1px solid var(--border);        .action-grid{ display:grid; grid-template-columns: repeat(auto-fit, minmax(160px,1fr)); gap:12px; }    .row:last-child{ border-bottom:0; }



        .action-grid {            border-radius: 16px;

            display: grid;

            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));            padding: 20px;        .action-card{ display:flex; flex-direction:column; align-items:center; gap:10px; padding:18px 14px; border:1px solid var(--border); border-radius:14px; text-decoration:none; color:var(--ink); background:var(--card); transition: all .2s cubic-bezier(.4,0,.2,1); text-align:center; }    .row:hover{ background:transparent; }

            gap: 12px;

        }            display: flex;



        .action-card {            flex-direction: column;        .action-card:hover{ transform: translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,.08); border-color: rgba(99,102,241,.3); }        .row .left{ display:flex; align-items:center; gap:10px; }

            display: flex;

            flex-direction: column;            gap: 12px;

            align-items: center;

            gap: 10px;            box-shadow: var(--shadow);        .action-ico{ width:40px; height:40px; border-radius:12px; display:grid; place-items:center; background: linear-gradient(135deg, rgba(245,158,11,.1), rgba(99,102,241,.1)); color:var(--accent); }        .row .title{ font-weight:700; color:var(--ink); }

            padding: 18px 14px;

            border: 1px solid var(--border);            transition: all .2s cubic-bezier(.4,0,.2,1);

            border-radius: 14px;

            text-decoration: none;        }        .action-card strong{ font-size:14px; font-weight:800; }        .row .meta{ color:var(--muted); font-size:12px; }

            color: var(--ink);

            background: var(--card);        .kpi-card:hover {

            transition: all .2s cubic-bezier(.4,0,.2,1);

            text-align: center;            transform: translateY(-3px);        .badge{ display:inline-block; padding:4px 8px; font-size:11px; font-weight:700; border-radius:999px; }        .btn-primary{ display:inline-flex; align-items:center; gap:10px; padding:10px 14px; background:linear-gradient(90deg, var(--brandA), var(--brandB)); color:#fff; border-radius:10px; text-decoration:none; font-weight:800; border:0; box-shadow:var(--shadow); }

        }

            box-shadow: 0 12px 40px rgba(0,0,0,.1);

        .action-card:hover {

            transform: translateY(-2px);            border-color: rgba(99,102,241,.3);        .b-ok{ background:#ecfdf5; color:#065f46; }        .btn-primary:hover{ filter:brightness(.97); }

            box-shadow: 0 8px 24px rgba(0,0,0,.08);

            border-color: rgba(99,102,241,.3);        }

        }

        .kpi-top {        .b-warn{ background:#fffbeb; color:#92400e; }        .divider{ height:1px; background:var(--border); margin:10px 0; }

        .action-ico {

            width: 40px;            display: flex;

            height: 40px;

            border-radius: 12px;            align-items: center;        .activity{ display:flex; flex-direction:column; gap:12px; }        .theme-toggle{ display:inline-flex; align-items:center; gap:8px; padding:8px 10px; border:1px solid var(--border); border-radius:10px; background: var(--card); color:var(--ink); cursor:pointer; font-weight:700; }

            display: grid;

            place-items: center;            justify-content: space-between;

            background: linear-gradient(135deg, rgba(245,158,11,.1), rgba(99,102,241,.1));

            color: var(--accent);        }        .activity-row{ display:flex; align-items:center; gap:12px; padding:12px; border-radius:12px; transition:background .15s ease; }        .theme-toggle:hover{ background: linear-gradient(90deg, rgba(245,158,11,.06), rgba(99,102,241,.06)); }

        }

        .kpi-ico {

        .action-card strong {

            font-size: 14px;            width: 48px;        .activity-row:hover{ background: rgba(99,102,241,.04); }        @media (max-width: 860px) { .panel-grid{ grid-template-columns:1fr; } }

            font-weight: 800;

        }            height: 48px;



        .badge {            border-radius: 14px;        .activity-avatar{ width:36px; height:36px; border-radius:50%; display:grid; place-items:center; font-weight:800; font-size:13px; color:#fff; background: linear-gradient(135deg, var(--accent), #8b5cf6); flex-shrink:0; }    </style>

            display: inline-block;

            padding: 4px 8px;            display: grid;

            font-size: 11px;

            font-weight: 700;            place-items: center;        .activity-info{ flex:1; min-width:0; }    </head>

            border-radius: 999px;

        }            color: #fff;



        .b-ok {            background: linear-gradient(135deg, var(--accent), #8b5cf6);        .activity-title{ font-weight:700; color:var(--ink); font-size:14px; }<body>

            background: #ecfdf5;

            color: #065f46;            box-shadow: 0 4px 14px rgba(99,102,241,.3);

        }

        }        .activity-meta{ color:var(--muted); font-size:12px; margin-top:2px; }    <div class="dashboard-container">

        .b-warn {

            background: #fffbeb;        .kpi-trend {

            color: #92400e;

    }            display: inline-flex;        .activity-badge{ padding:4px 10px; border-radius:999px; font-size:11px; font-weight:800; text-transform:capitalize; background:#eef2ff; color:#3730a3; white-space:nowrap; }        <?= view('components/sidebar') ?>



        .activity {            align-items: center;

            display: flex;

            flex-direction: column;            gap: 4px;        .btn-primary{ display:inline-flex; align-items:center; gap:10px; padding:12px 18px; background:linear-gradient(90deg, var(--brandA), var(--brandB)); color:#fff; border-radius:12px; text-decoration:none; font-weight:800; border:0; box-shadow:0 4px 14px rgba(245,158,11,.3); transition: all .2s ease; }

            gap: 12px;

        }            padding: 4px 8px;



        .activity-row {            border-radius: 999px;        .btn-primary:hover{ transform: translateY(-1px); box-shadow:0 6px 20px rgba(245,158,11,.4); }        <main class="main-content">

            display: flex;

            align-items: center;            font-size: 11px;

            gap: 12px;

            padding: 12px;            font-weight: 700;        .divider{ height:1px; background:var(--border); margin:14px 0; }            <header class="top-bar">

            border-radius: 12px;

            transition: background .15s ease;            background: #ecfdf5;

        }

            color: #065f46;        .theme-toggle{ display:inline-flex; align-items:center; gap:8px; padding:8px 10px; border:1px solid var(--border); border-radius:10px; background: var(--card); color:var(--ink); cursor:pointer; font-weight:700; transition: all .15s ease; }                <h1>Interviewer Dashboard</h1>

        .activity-row:hover {

            background: rgba(99,102,241,.04);        }

        }

        .kpi-meta h3 {        .theme-toggle:hover{ background: linear-gradient(90deg, rgba(245,158,11,.06), rgba(99,102,241,.06)); }                <div class="user-info" style="gap:8px;">

        .activity-avatar {

            width: 36px;            margin: 0;

            height: 36px;

            border-radius: 50%;            font-size: 36px;        @media (max-width: 860px) { .panel-grid{ grid-template-columns:1fr; } .action-grid{ grid-template-columns:1fr; } }                    <button id="themeBtn" class="theme-toggle" type="button" aria-pressed="false" aria-label="Toggle theme">

            display: grid;

            place-items: center;            font-weight: 900;

            font-weight: 800;

            font-size: 13px;            color: var(--ink);    </style>                        <svg id="sunIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>

            color: #fff;

            background: linear-gradient(135deg, var(--accent), #8b5cf6);            letter-spacing: -.5px;

            flex-shrink: 0;

        }            line-height: 1;</head>                        <svg id="moonIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>



        .activity-info {        }

            flex: 1;

            min-width: 0;        .kpi-meta p {<body>                        Theme

        }

            margin: 6px 0 0;

        .activity-title {

            font-weight: 700;            font-size: 12px;    <div class="dashboard-container">                    </button>

            color: var(--ink);

            font-size: 14px;            color: var(--muted);

        }

            font-weight: 600;        <?= view('components/sidebar') ?>                    <span>Welcome, <?= esc(session()->get('first_name')) ?> <?= esc(session()->get('last_name')) ?></span>

        .activity-meta {

            color: var(--muted);            letter-spacing: .2px;

            font-size: 12px;

            margin-top: 2px;            text-transform: uppercase;                    <div class="user-avatar"><?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?></div>

        }

        }

        .activity-badge {

            padding: 4px 10px;        .panel-grid {        <main class="main-content">                </div>

            border-radius: 999px;

            font-size: 11px;            display: grid;

            font-weight: 800;

            text-transform: capitalize;            grid-template-columns: repeat(12, 1fr);            <header class="top-bar">            </header>

            background: #eef2ff;

            color: #3730a3;            gap: 16px;

            white-space: nowrap;

        }            margin-top: 18px;                <h1>Interviewer Dashboard</h1>



        .btn-primary {        }

            display: inline-flex;

            align-items: center;        .panel {                <div class="user-info" style="gap:8px;">            <div class="dashboard-content">

            gap: 10px;

            padding: 12px 18px;            background: var(--card);

            background: linear-gradient(90deg, var(--brandA), var(--brandB));

            color: #fff;            border: 1px solid var(--border);                    <button id="themeBtn" class="theme-toggle" type="button" aria-pressed="false" aria-label="Toggle theme">                <div class="hero">

            border-radius: 12px;

            text-decoration: none;            border-radius: 16px;

            font-weight: 800;

            border: 0;            padding: 20px;                        <svg id="sunIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>                    <div>

            box-shadow: 0 4px 14px rgba(245,158,11,.3);

            transition: all .2s ease;            box-shadow: var(--shadow);

        }

        }                        <svg id="moonIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>                        <h1>Good day, <?= esc(session()->get('first_name')) ?> 👋</h1>

        .btn-primary:hover {

            transform: translateY(-1px);        .panel h3 {

            box-shadow: 0 6px 20px rgba(245,158,11,.4);

        }            margin: 0 0 14px;                        Theme                        <div class="hint">Track your applications and log IGT interviews faster with the quick tools below.</div>



        .divider {            font-size: 16px;

            height: 1px;

            background: var(--border);            letter-spacing: -.2px;                    </button>                    </div>

            margin: 14px 0;

        }            color: var(--ink);



        .theme-toggle {            font-weight: 800;                    <span>Welcome, <?= esc(session()->get('first_name')) ?> <?= esc(session()->get('last_name')) ?></span>                    <a href="<?= base_url('interviewer/application') ?>" class="btn-primary">

            display: inline-flex;

            align-items: center;        }

            gap: 8px;

            padding: 8px 10px;        .action-grid {                    <div class="user-avatar"><?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?></div>                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>

            border: 1px solid var(--border);

            border-radius: 10px;            display: grid;

            background: var(--card);

            color: var(--ink);            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));                </div>                        New Application

            cursor: pointer;

            font-weight: 700;            gap: 12px;

            transition: all .15s ease;

        }        }            </header>                    </a>



        .theme-toggle:hover {        .action-card {

            background: linear-gradient(90deg, rgba(245,158,11,.06), rgba(99,102,241,.06));

        }            display: flex;                    <div class="blobs" aria-hidden="true">



        @media (max-width: 860px) {            flex-direction: column;

            .panel-grid {

                grid-template-columns: 1fr;            align-items: center;            <div class="dashboard-content">                        <div class="blob b1"></div>

            }

            .action-grid {            gap: 10px;

                grid-template-columns: 1fr;

            }            padding: 18px 14px;                <div class="hero">                        <div class="blob b2"></div>

        }

    </style>            border: 1px solid var(--border);

</head>

<body>            border-radius: 14px;                    <div>                    </div>

    <div class="dashboard-container">

    <?= view('components/sidebar') ?>            text-decoration: none;



        <main class="main-content">            color: var(--ink);                        <h1>Good day, <?= esc(session()->get('first_name')) ?> 👋</h1>                </div>

            <header class="top-bar">

                <h1>Interviewer Dashboard</h1>            background: var(--card);

                <div class="user-info" style="gap:8px;">

                    <button id="themeBtn" class="theme-toggle" type="button" aria-pressed="false" aria-label="Toggle theme">            transition: all .2s cubic-bezier(.4,0,.2,1);                        <div class="hint">Track your applications and log IGT interviews faster with the quick tools below.</div>

                        <svg id="sunIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                            <circle cx="12" cy="12" r="4"/>            text-align: center;

                            <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>

                        </svg>        }                    </div>                <?php if (session()->getFlashdata('success')): ?>

                        <svg id="moonIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;">

                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>        .action-card:hover {

                        </svg>

                        Theme            transform: translateY(-2px);                    <a href="<?= base_url('interviewer/application') ?>" class="btn-primary">                    <div class="alert alert-success" style="margin-top:12px;">

                    </button>

                    <span>Welcome, <?= esc(session()->get('first_name')) ?> <?= esc(session()->get('last_name')) ?></span>            box-shadow: 0 8px 24px rgba(0,0,0,.08);

                    <div class="user-avatar"><?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?></div>

                </div>            border-color: rgba(99,102,241,.3);                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>                        <?= session()->getFlashdata('success') ?>

            </header>

        }

            <div class="dashboard-content">

                <div class="hero">        .action-ico {                        New Application                    </div>

                    <div>

                        <h1>Good day, <?= esc(session()->get('first_name')) ?> 👋</h1>            width: 40px;

                        <div class="hint">Track your applications and log IGT interviews faster with the quick tools below.</div>

                    </div>            height: 40px;                    </a>                <?php endif; ?>

                    <a href="<?= base_url('interviewer/application') ?>" class="btn-primary">

                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">            border-radius: 12px;

                            <path d="M12 5v14M5 12h14"/>

                        </svg>            display: grid;                </div>

                        New Application

                    </a>            place-items: center;

                </div>

            background: linear-gradient(135deg, rgba(245,158,11,.1), rgba(99,102,241,.1));                <section class="kpi-grid">

                <?php if (session()->getFlashdata('success')): ?>

                    <div class="alert alert-success" style="margin-top:14px;">            color: var(--accent);

                        <?= session()->getFlashdata('success') ?>

                    </div>        }                <?php if (session()->getFlashdata('success')): ?>                    <div class="kpi-card">

                <?php endif; ?>

        .action-card strong {

                <section class="kpi-grid">

                    <div class="kpi-card">            font-size: 14px;                    <div class="alert alert-success" style="margin-top:14px;">                        <div class="kpi-ico" style="background: linear-gradient(135deg,#6366f1,#8b5cf6);">

                        <div class="kpi-top">

                            <div class="kpi-ico">            font-weight: 800;

                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>        }                        <?= session()->getFlashdata('success') ?>                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                                    <circle cx="8.5" cy="7" r="4"/>

                                    <polyline points="17 11 19 13 23 9"/>        .activity {

                                </svg>

                            </div>            display: flex;                    </div>                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>

                            <div class="kpi-trend">

                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">            flex-direction: column;

                                    <path d="M18 15l-6-6-6 6"/>

                                </svg>            gap: 12px;                <?php endif; ?>                                <circle cx="8.5" cy="7" r="4"/>

                                12%

                            </div>        }

                        </div>

                        <div class="kpi-meta">        .activity-row {                                <polyline points="17 11 19 13 23 9"/>

                            <h3><?= (int)($total_applications ?? 0) ?></h3>

                            <p>Total Applications</p>            display: flex;

                        </div>

                    </div>            align-items: center;                <section class="kpi-grid">                            </svg>



                    <div class="kpi-card">            gap: 12px;

                        <div class="kpi-top">

                            <div class="kpi-ico" style="background: linear-gradient(135deg,#10b981,#34d399); box-shadow: 0 4px 14px rgba(16,185,129,.3);">            padding: 12px;                    <div class="kpi-card">                        </div>

                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                                    <rect x="3" y="3" width="18" height="18" rx="2"/>            border-radius: 12px;

                                    <line x1="9" y1="3" x2="9" y2="21"/>

                                </svg>            transition: background .15s ease;                        <div class="kpi-top">                        <div class="kpi-meta">

                            </div>

                            <div class="kpi-trend">        }

                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">

                                    <path d="M18 15l-6-6-6 6"/>        .activity-row:hover {                            <div class="kpi-ico">                            <h3><?= (int)($total_applications ?? 0) ?></h3>

                                </svg>

                                8%            background: rgba(99,102,241,.04);

                            </div>

                        </div>        }                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">                            <p>Total Applications</p>

                        <div class="kpi-meta">

                            <h3><?= (int)($rsd_applications ?? 0) ?></h3>        .activity-avatar {

                            <p>RSD Applications</p>

                        </div>            width: 36px;                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>                        </div>

                    </div>

            height: 36px;

                    <div class="kpi-card">

                        <div class="kpi-top">            border-radius: 50%;                                    <circle cx="8.5" cy="7" r="4"/>                    </div>

                            <div class="kpi-ico" style="background: linear-gradient(135deg,#0ea5e9,#22d3ee); box-shadow: 0 4px 14px rgba(14,165,233,.3);">

                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">            display: grid;

                                    <path d="M20 7h-9"/>

                                    <path d="M14 17H5"/>            place-items: center;                                    <polyline points="17 11 19 13 23 9"/>                    <div class="kpi-card">

                                    <circle cx="17" cy="17" r="3"/>

                                    <circle cx="7" cy="7" r="3"/>            font-weight: 800;

                                </svg>

                            </div>            font-size: 13px;                                </svg>                        <div class="kpi-ico" style="background: linear-gradient(135deg,#10b981,#34d399);">

                            <div class="kpi-trend">

                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">            color: #fff;

                                    <path d="M18 15l-6-6-6 6"/>

                                </svg>            background: linear-gradient(135deg, var(--accent), #8b5cf6);                            </div>                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                                5%

                            </div>            flex-shrink: 0;

                        </div>

                        <div class="kpi-meta">        }                            <div class="kpi-trend">                                <rect x="3" y="3" width="18" height="18" rx="2"/>

                            <h3><?= (int)($igt_applications ?? 0) ?></h3>

                            <p>IGT Applications</p>        .activity-info {

                        </div>

                    </div>            flex: 1;                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M18 15l-6-6-6 6"/></svg>                                <line x1="9" y1="3" x2="9" y2="21"/>

                </section>

            min-width: 0;

                <section class="panel-grid">

                    <div class="panel" style="grid-column: span 7; min-width:0;">        }                                12%                            </svg>

                        <h3>Quick Actions</h3>

                        <div class="action-grid">        .activity-title {

                            <a class="action-card" href="<?= base_url('interviewer/application') ?>">

                                <div class="action-ico">            font-weight: 700;                            </div>                        </div>

                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                                        <path d="M12 5v14M5 12h14"/>            color: var(--ink);

                                    </svg>

                                </div>            font-size: 14px;                        </div>                        <div class="kpi-meta">

                                <strong>Create Application</strong>

                            </a>        }



                            <a class="action-card" href="<?= base_url('interviewer/applications') ?>">        .activity-meta {                        <div class="kpi-meta">                            <h3><?= (int)($rsd_applications ?? 0) ?></h3>

                                <div class="action-ico">

                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">            color: var(--muted);

                                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>

                                    </svg>            font-size: 12px;                            <h3><?= (int)($total_applications ?? 0) ?></h3>                            <p>RSD Applications</p>

                                </div>

                                <strong>My Applications</strong>            margin-top: 2px;

                            </a>

        }                            <p>Total Applications</p>                        </div>

                            <a class="action-card" href="<?= base_url('interviewer/applications') ?>?filter=igt">

                                <div class="action-ico">        .activity-badge {

                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                                        <circle cx="11" cy="11" r="8"/>            padding: 4px 10px;                        </div>                    </div>

                                        <path d="M21 21l-4.35-4.35"/>

                                    </svg>            border-radius: 999px;

                                </div>

                                <strong>IGT Candidates</strong>            font-size: 11px;                    </div>                    <div class="kpi-card">

                            </a>

                        </div>            font-weight: 800;



                        <div class="divider"></div>            text-transform: capitalize;                    <div class="kpi-card">                        <div class="kpi-ico" style="background: linear-gradient(135deg,#0ea5e9,#22d3ee);">

                        <div class="hint">💡 Tip: Open an application and use "➕ IGT Interview" for IGT companies to record the additional interview.</div>

                    </div>            background: #eef2ff;



                    <div class="panel" style="grid-column: span 5; min-width:0;">            color: #3730a3;                        <div class="kpi-top">                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                        <h3>Recently Updated</h3>

                        <?php if (!empty($recent_applications) && is_array($recent_applications)): ?>            white-space: nowrap;

                            <div class="activity">

                                <?php foreach ($recent_applications as $ra): ?>        }                            <div class="kpi-ico" style="background: linear-gradient(135deg,#10b981,#34d399); box-shadow: 0 4px 14px rgba(16,185,129,.3);">                                <path d="M20 7h-9"/>

                                    <div class="activity-row">

                                        <div class="activity-avatar"><?= strtoupper(substr($ra['first_name'] ?? 'U', 0, 1)) ?></div>        .btn-primary {

                                        <div class="activity-info">

                                            <div class="activity-title">#<?= str_pad((int)($ra['id'] ?? 0), 4, '0', STR_PAD_LEFT) ?> · <?= esc(($ra['first_name'] ?? '') . ' ' . ($ra['last_name'] ?? '')) ?></div>            display: inline-flex;                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">                                <path d="M14 17H5"/>

                                            <div class="activity-meta"><?= esc($ra['company_name'] ?? '—') ?> • <?= !empty($ra['updated_at']) ? date('M d, H:i', strtotime($ra['updated_at'])) : '—' ?></div>

                                        </div>            align-items: center;

                                        <div class="activity-badge"><?= esc(str_replace('_',' ', $ra['status'] ?? 'pending')) ?></div>

                                    </div>            gap: 10px;                                    <rect x="3" y="3" width="18" height="18" rx="2"/>                                <circle cx="17" cy="17" r="3"/>

                                <?php endforeach; ?>

                            </div>            padding: 12px 18px;

                        <?php else: ?>

                            <div class="hint">No recent updates yet.</div>            background: linear-gradient(90deg, var(--brandA), var(--brandB));                                    <line x1="9" y1="3" x2="9" y2="21"/>                                <circle cx="7" cy="7" r="3"/>

                        <?php endif; ?>

                    </div>            color: #fff;

                </section>

            </div>            border-radius: 12px;                                </svg>                            </svg>

        </main>

    </div>            text-decoration: none;



    <?= view('components/sidebar_script') ?>            font-weight: 800;                            </div>                        </div>

    <script>

        (function(){            border: 0;

            const btn = document.getElementById('themeBtn');

            const sun = document.getElementById('sunIcon');            box-shadow: 0 4px 14px rgba(245,158,11,.3);                            <div class="kpi-trend">                        <div class="kpi-meta">

            const moon = document.getElementById('moonIcon');

            const root = document.documentElement;            transition: all .2s ease;

            const key = 'rsd-theme';

                    }                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M18 15l-6-6-6 6"/></svg>                            <h3><?= (int)($igt_applications ?? 0) ?></h3>

            function apply(theme){

                const dark = theme === 'dark';        .btn-primary:hover {

                document.body.classList.toggle('theme-dark', dark);

                btn.setAttribute('aria-pressed', String(dark));            transform: translateY(-1px);                                8%                            <p>IGT Applications</p>

                sun.style.display = dark ? 'none' : '';

                moon.style.display = dark ? '' : 'none';            box-shadow: 0 6px 20px rgba(245,158,11,.4);

            }

                    }                            </div>                        </div>

            const preferred = localStorage.getItem(key) || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

            apply(preferred);        .divider {

            

            btn.addEventListener('click', ()=>{            height: 1px;                        </div>                    </div>

                const next = document.body.classList.contains('theme-dark') ? 'light' : 'dark';

                localStorage.setItem(key, next);            background: var(--border);

                apply(next);

            });            margin: 14px 0;                        <div class="kpi-meta">                </section>

        })();

    </script>        }

</body>

</html>        .theme-toggle {                            <h3><?= (int)($rsd_applications ?? 0) ?></h3>


            display: inline-flex;

            align-items: center;                            <p>RSD Applications</p>                <section class="panel-grid">

            gap: 8px;

            padding: 8px 10px;                        </div>                    <div class="panel" style="grid-column: span 7; min-width:0;">

            border: 1px solid var(--border);

            border-radius: 10px;                    </div>                        <h3>Quick Actions</h3>

            background: var(--card);

            color: var(--ink);                    <div class="kpi-card">                        <div class="quick-grid">

            cursor: pointer;

            font-weight: 700;                        <div class="kpi-top">                            <a class="qbtn" href="<?= base_url('interviewer/application') ?>">

            transition: all .15s ease;

        }                            <div class="kpi-ico" style="background: linear-gradient(135deg,#0ea5e9,#22d3ee); box-shadow: 0 4px 14px rgba(14,165,233,.3);">                                <span class="badge b-ok">New</span>

        .theme-toggle:hover {

            background: linear-gradient(90deg, rgba(245,158,11,.06), rgba(99,102,241,.06));                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">                                Create Application

        }

        @media (max-width: 860px) {                                    <path d="M20 7h-9"/>                            </a>

            .panel-grid {

                grid-template-columns: 1fr;                                    <path d="M14 17H5"/>                            <a class="qbtn" href="<?= base_url('interviewer/applications') ?>">

            }

            .action-grid {                                    <circle cx="17" cy="17" r="3"/>                                <span class="badge b-warn">View</span>

                grid-template-columns: 1fr;

            }                                    <circle cx="7" cy="7" r="3"/>                                My Applications

        }

    </style>                                </svg>                            </a>

</head>

<body>                            </div>                            <a class="qbtn" href="<?= base_url('interviewer/applications') ?>?filter=igt">

    <div class="dashboard-container">

    <?= view('components/sidebar') ?>                            <div class="kpi-trend">                                <span class="badge b-ok">IGT</span>



        <main class="main-content">                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M18 15l-6-6-6 6"/></svg>                                IGT Candidates

            <header class="top-bar">

                <h1>Interviewer Dashboard</h1>                                5%                            </a>

                <div class="user-info" style="gap:8px;">

                    <button id="themeBtn" class="theme-toggle" type="button" aria-pressed="false" aria-label="Toggle theme">                            </div>                        </div>

                        <svg id="sunIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                            <circle cx="12" cy="12" r="4"/>                        </div>                        <div class="divider"></div>

                            <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>

                        </svg>                        <div class="kpi-meta">                        <div class="hint">Tip: Open an application and use “➕ IGT Interview” for IGT companies to record the additional interview.</div>

                        <svg id="moonIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;">

                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>                            <h3><?= (int)($igt_applications ?? 0) ?></h3>                    </div>

                        </svg>

                        Theme                            <p>IGT Applications</p>

                    </button>

                    <span>Welcome, <?= esc(session()->get('first_name')) ?> <?= esc(session()->get('last_name')) ?></span>                        </div>                    <div class="panel" style="grid-column: span 5; min-width:0;">

                    <div class="user-avatar"><?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?></div>

                </div>                    </div>                        <h3>Recently Updated</h3>

            </header>

                </section>                        <?php if (!empty($recent_applications) && is_array($recent_applications)): ?>

            <div class="dashboard-content">

                <div class="hero">                            <div class="list">

                    <div>

                        <h1>Good day, <?= esc(session()->get('first_name')) ?> 👋</h1>                <section class="panel-grid">                                <?php foreach ($recent_applications as $ra): ?>

                        <div class="hint">Track your applications and log IGT interviews faster with the quick tools below.</div>

                    </div>                    <div class="panel" style="grid-column: span 7; min-width:0;">                                    <div class="row">

                    <a href="<?= base_url('interviewer/application') ?>" class="btn-primary">

                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">                        <h3>Quick Actions</h3>                                        <div class="left">

                            <path d="M12 5v14M5 12h14"/>

                        </svg>                        <div class="action-grid">                                            <div class="title">#<?= str_pad((int)($ra['id'] ?? 0), 4, '0', STR_PAD_LEFT) ?> · <?= esc(($ra['first_name'] ?? '') . ' ' . ($ra['last_name'] ?? '')) ?></div>

                        New Application

                    </a>                            <a class="action-card" href="<?= base_url('interviewer/application') ?>">                                            <div class="meta"><?= esc($ra['company_name'] ?? '—') ?> • <?= !empty($ra['updated_at']) ? date('M d, Y H:i', strtotime($ra['updated_at'])) : '—' ?></div>

                </div>

                                <div class="action-ico">                                        </div>

                <?php if (session()->getFlashdata('success')): ?>

                    <div class="alert alert-success" style="margin-top:14px;">                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>                                        <div>

                        <?= session()->getFlashdata('success') ?>

                    </div>                                </div>                                            <span class="badge" style="background:#eef2ff;color:#3730a3; font-weight:800; text-transform:capitalize;"><?= esc(str_replace('_',' ', $ra['status'] ?? 'pending')) ?></span>

                <?php endif; ?>

                                <strong>Create Application</strong>                                        </div>

                <section class="kpi-grid">

                    <div class="kpi-card">                            </a>                                    </div>

                        <div class="kpi-top">

                            <div class="kpi-ico">                            <a class="action-card" href="<?= base_url('interviewer/applications') ?>">                                <?php endforeach; ?>

                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>                                <div class="action-ico">                            </div>

                                    <circle cx="8.5" cy="7" r="4"/>

                                    <polyline points="17 11 19 13 23 9"/>                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>                        <?php else: ?>

                                </svg>

                            </div>                                </div>                            <div class="hint">No recent updates yet.</div>

                            <div class="kpi-trend">

                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">                                <strong>My Applications</strong>                        <?php endif; ?>

                                    <path d="M18 15l-6-6-6 6"/>

                                </svg>                            </a>                    </div>

                                12%

                            </div>                            <a class="action-card" href="<?= base_url('interviewer/applications') ?>?filter=igt">                </section>

                        </div>

                        <div class="kpi-meta">                                <div class="action-ico">            </div>

                            <h3><?= (int)($total_applications ?? 0) ?></h3>

                            <p>Total Applications</p>                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>        </main>

                        </div>

                    </div>                                </div>    </div>

                    <div class="kpi-card">

                        <div class="kpi-top">                                <strong>IGT Candidates</strong>

                            <div class="kpi-ico" style="background: linear-gradient(135deg,#10b981,#34d399); box-shadow: 0 4px 14px rgba(16,185,129,.3);">

                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">                            </a>    <?= view('components/sidebar_script') ?>

                                    <rect x="3" y="3" width="18" height="18" rx="2"/>

                                    <line x1="9" y1="3" x2="9" y2="21"/>                        </div>    <script>

                                </svg>

                            </div>                        <div class="divider"></div>        (function(){

                            <div class="kpi-trend">

                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">                        <div class="hint">💡 Tip: Open an application and use "➕ IGT Interview" for IGT companies to record the additional interview.</div>            const btn = document.getElementById('themeBtn');

                                    <path d="M18 15l-6-6-6 6"/>

                                </svg>                    </div>            const sun = document.getElementById('sunIcon');

                                8%

                            </div>            const moon = document.getElementById('moonIcon');

                        </div>

                        <div class="kpi-meta">                    <div class="panel" style="grid-column: span 5; min-width:0;">            const root = document.documentElement;

                            <h3><?= (int)($rsd_applications ?? 0) ?></h3>

                            <p>RSD Applications</p>                        <h3>Recently Updated</h3>            const key = 'rsd-theme';

                        </div>

                    </div>                        <?php if (!empty($recent_applications) && is_array($recent_applications)): ?>            function apply(theme){

                    <div class="kpi-card">

                        <div class="kpi-top">                            <div class="activity">                const dark = theme === 'dark';

                            <div class="kpi-ico" style="background: linear-gradient(135deg,#0ea5e9,#22d3ee); box-shadow: 0 4px 14px rgba(14,165,233,.3);">

                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">                                <?php foreach ($recent_applications as $ra): ?>                document.body.classList.toggle('theme-dark', dark);

                                    <path d="M20 7h-9"/>

                                    <path d="M14 17H5"/>                                    <div class="activity-row">                btn.setAttribute('aria-pressed', String(dark));

                                    <circle cx="17" cy="17" r="3"/>

                                    <circle cx="7" cy="7" r="3"/>                                        <div class="activity-avatar"><?= strtoupper(substr($ra['first_name'] ?? 'U', 0, 1)) ?></div>                sun.style.display = dark ? 'none' : '';

                                </svg>

                            </div>                                        <div class="activity-info">                moon.style.display = dark ? '' : 'none';

                            <div class="kpi-trend">

                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">                                            <div class="activity-title">#<?= str_pad((int)($ra['id'] ?? 0), 4, '0', STR_PAD_LEFT) ?> · <?= esc(($ra['first_name'] ?? '') . ' ' . ($ra['last_name'] ?? '')) ?></div>            }

                                    <path d="M18 15l-6-6-6 6"/>

                                </svg>                                            <div class="activity-meta"><?= esc($ra['company_name'] ?? '—') ?> • <?= !empty($ra['updated_at']) ? date('M d, H:i', strtotime($ra['updated_at'])) : '—' ?></div>            const preferred = localStorage.getItem(key) || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

                                5%

                            </div>                                        </div>            apply(preferred);

                        </div>

                        <div class="kpi-meta">                                        <div class="activity-badge"><?= esc(str_replace('_',' ', $ra['status'] ?? 'pending')) ?></div>            btn.addEventListener('click', ()=>{

                            <h3><?= (int)($igt_applications ?? 0) ?></h3>

                            <p>IGT Applications</p>                                    </div>                const next = document.body.classList.contains('theme-dark') ? 'light' : 'dark';

                        </div>

                    </div>                                <?php endforeach; ?>                localStorage.setItem(key, next);

                </section>

                            </div>                apply(next);

                <section class="panel-grid">

                    <div class="panel" style="grid-column: span 7; min-width:0;">                        <?php else: ?>            });

                        <h3>Quick Actions</h3>

                        <div class="action-grid">                            <div class="hint">No recent updates yet.</div>        })();

                            <a class="action-card" href="<?= base_url('interviewer/application') ?>">

                                <div class="action-ico">                        <?php endif; ?>    </script>

                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                                        <path d="M12 5v14M5 12h14"/>                    </div></body>

                                    </svg>

                                </div>                </section></html>

                                <strong>Create Application</strong>

                            </a>            </div>

                            <a class="action-card" href="<?= base_url('interviewer/applications') ?>">        </main>

                                <div class="action-ico">    </div>

                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">

                                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>    <?= view('components/sidebar_script') ?>

                                    </svg>    <script>

                                </div>        (function(){

                                <strong>My Applications</strong>            const btn = document.getElementById('themeBtn');

                            </a>            const sun = document.getElementById('sunIcon');

                            <a class="action-card" href="<?= base_url('interviewer/applications') ?>?filter=igt">            const moon = document.getElementById('moonIcon');

                                <div class="action-ico">            const root = document.documentElement;

                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">            const key = 'rsd-theme';

                                        <circle cx="11" cy="11" r="8"/>            function apply(theme){

                                        <path d="M21 21l-4.35-4.35"/>                const dark = theme === 'dark';

                                    </svg>                document.body.classList.toggle('theme-dark', dark);

                                </div>                btn.setAttribute('aria-pressed', String(dark));

                                <strong>IGT Candidates</strong>                sun.style.display = dark ? 'none' : '';

                            </a>                moon.style.display = dark ? '' : 'none';

                        </div>            }

                        <div class="divider"></div>            const preferred = localStorage.getItem(key) || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

                        <div class="hint">💡 Tip: Open an application and use "➕ IGT Interview" for IGT companies to record the additional interview.</div>            apply(preferred);

                    </div>            btn.addEventListener('click', ()=>{

                const next = document.body.classList.contains('theme-dark') ? 'light' : 'dark';

                    <div class="panel" style="grid-column: span 5; min-width:0;">                localStorage.setItem(key, next);

                        <h3>Recently Updated</h3>                apply(next);

                        <?php if (!empty($recent_applications) && is_array($recent_applications)): ?>            });

                            <div class="activity">        })();

                                <?php foreach ($recent_applications as $ra): ?>    </script>

                                    <div class="activity-row"></body>

                                        <div class="activity-avatar"><?= strtoupper(substr($ra['first_name'] ?? 'U', 0, 1)) ?></div></html>

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
    </div>

    <?= view('components/sidebar_script') ?>
    <script>
        (function(){
            const btn = document.getElementById('themeBtn');
            const sun = document.getElementById('sunIcon');
            const moon = document.getElementById('moonIcon');
            const root = document.documentElement;
            const key = 'rsd-theme';
            function apply(theme){
                const dark = theme === 'dark';
                document.body.classList.toggle('theme-dark', dark);
                btn.setAttribute('aria-pressed', String(dark));
                sun.style.display = dark ? 'none' : '';
                moon.style.display = dark ? '' : 'none';
            }
            const preferred = localStorage.getItem(key) || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            apply(preferred);
            btn.addEventListener('click', ()=>{
                const next = document.body.classList.contains('theme-dark') ? 'light' : 'dark';
                localStorage.setItem(key, next);
                apply(next);
            });
        })();
    </script>
</body>
</html>
