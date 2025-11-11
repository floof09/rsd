<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Companies - RSD Admin</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>?v=<?= time() ?>">
</head>
<body>
<div class="dashboard-container interviewer-dashboard">
    <?= view('components/sidebar') ?>
    <main class="main-content companies-page">
        <header class="top-bar">
            <h1>Companies</h1>
            <div class="user-info user-info--tight">
                <span>Welcome, <?= esc(session()->get('first_name')) ?> <?= esc(session()->get('last_name')) ?></span>
                <div class="user-avatar"><?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?></div>
            </div>
        </header>
        <div class="dashboard-content">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-error"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>
            <div class="subheader">
                <div>
                    <p>Manage companies and configure their custom application fields.</p>
                </div>
                <a class="btn btn-primary" href="<?= base_url('admin/companies/create') ?>">Add Company</a>
            </div>

            <div class="companies-toolbar card">
                <div class="toolbar-left">
                    <div class="search-input">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" id="companySearch" placeholder="Search companies..." autocomplete="off" />
                    </div>
                    <div class="filter-group" id="statusFilter" role="tablist" aria-label="Status filter">
                        <button class="filter-btn active" data-status="all">All</button>
                        <button class="filter-btn" data-status="active">Active</button>
                        <button class="filter-btn" data-status="inactive">Inactive</button>
                    </div>
                </div>
                <div class="toolbar-right">
                    <span class="muted" id="companyCount"></span>
                </div>
            </div>

            <div class="card">
                <table class="table table-modern" id="companiesTable">
                    <thead>
                        <tr>
                            <th class="col-id">#</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Custom Fields</th>
                            <th class="col-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($companies)): ?>
                            <tr><td colspan="5" class="empty">No companies yet.</td></tr>
                        <?php else: foreach ($companies as $c): $schema = json_decode($c['form_schema'] ?? '[]', true); $count = is_array($schema) && !empty($schema['fields']) ? count($schema['fields']) : 0; ?>
                            <tr data-name="<?= esc(strtolower($c['name'])) ?>" data-status="<?= esc(strtolower($c['status'])) ?>">
                                <td><?= (int)$c['id'] ?></td>
                                <td>
                                    <div class="cell-title">
                                        <div class="logo-fallback" aria-hidden="true"><?= strtoupper(substr($c['name'],0,1)) ?></div>
                                        <div>
                                            <div class="title-text"><?= esc($c['name']) ?></div>
                                            <div class="sub-text">ID <?= (int)$c['id'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge <?= $c['status']==='active'?'badge-success':'badge-secondary' ?>"><?= esc(ucfirst($c['status'])) ?></span></td>
                                <td><span class="count-pill"><?= (int)$count ?></span></td>
                                <td>
                                    <div class="row-actions">
                                        <a class="btn btn-small" href="<?= base_url('admin/companies/' . (int)$c['id'] . '/edit') ?>">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                                            Edit
                                        </a>
                                        <a class="btn btn-small btn-secondary" href="<?= base_url('admin/companies/' . (int)$c['id'] . '/form') ?>">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 8h10M7 12h6"/></svg>
                                            Configure Form
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<?= view('components/sidebar_script') ?>
<script>
    // Client-side search and status filter for a snappy UX
    const searchInput = document.getElementById('companySearch');
    const filterWrap = document.getElementById('statusFilter');
    const table = document.getElementById('companiesTable');
    const rows = [...table.querySelectorAll('tbody tr')];
    const countEl = document.getElementById('companyCount');

    function applyFilters(){
        const q = (searchInput?.value || '').trim().toLowerCase();
        const activeBtn = filterWrap?.querySelector('.filter-btn.active');
        const status = activeBtn ? activeBtn.dataset.status : 'all';
        let visible = 0;
        rows.forEach(r => {
            if (r.classList.contains('empty')) return;
            const name = r.getAttribute('data-name') || '';
            const st = r.getAttribute('data-status') || '';
            const matchesText = !q || name.includes(q);
            const matchesStatus = status === 'all' || st === status;
            const show = matchesText && matchesStatus;
            r.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        if (countEl) countEl.textContent = visible + ' of ' + rows.filter(r=>!r.classList.contains('empty')).length + ' shown';
    }

    searchInput?.addEventListener('input', applyFilters);
    filterWrap?.addEventListener('click', (e)=>{
        if (e.target.classList.contains('filter-btn')){
            [...filterWrap.querySelectorAll('.filter-btn')].forEach(b=>b.classList.remove('active'));
            e.target.classList.add('active');
            applyFilters();
        }
    });
    applyFilters();
</script>
</body>
</html>
