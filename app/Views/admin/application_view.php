<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Details - RSD Admin</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/application-form.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-start: #f6ad55; /* orange-400 */
            --brand-end: #f59e0b;   /* amber-500 */
            --ink: #1a202c;         /* gray-900 */
            --muted: #718096;       /* gray-500 */
            --border: #e2e8f0;      /* gray-200 */
            --bg-soft: #f8fafc;     /* gray-50 */
            --white: #ffffff;
            --shadow: 0 10px 30px rgba(0,0,0,0.25);
        }
        .detail-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px; }
        .detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px 24px; }
        .detail-item { display: flex; flex-direction: column; }
        .detail-label { font-size: 12px; color: #718096; margin-bottom: 4px; }
        .detail-value { font-size: 14px; color: #2d3748; font-weight: 500; word-break: break-word; }
        .section-title { margin: 18px 0 8px; color: #2d3748; font-weight: 700; }
        .toolbar { display:flex; gap: 8px; }
        .btn-link { display:inline-flex; align-items:center; gap:6px; padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px; background:#fff; color:#2d3748; text-decoration:none; font-weight:500; }
        .btn-link:hover { background:#f7fafc; }
        .status-badge { padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600; text-transform: capitalize; display:inline-block; }
        .status-pending { background:#fef3c7; color:#92400e; }
        .header-flex { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; }

        /* Modal viewer (themed) */
        .modal-overlay { position: fixed; inset: 0; background: rgba(17,24,39,0.6); backdrop-filter: blur(2px); display: none; align-items: center; justify-content: center; z-index: 1000; animation: fadeIn .15s ease-out; }
        .modal { background: var(--white); width: 92vw; max-width: 1100px; height: 90vh; border-radius: 14px; box-shadow: var(--shadow); display:flex; flex-direction: column; overflow: hidden; border:1px solid var(--border); }
        .modal-header { display:flex; align-items:center; justify-content: space-between; padding: 10px 14px; border-bottom: 1px solid var(--border); background: linear-gradient(90deg, var(--bg-soft), #fff); }
        .modal-title { font-weight: 700; color: var(--ink); letter-spacing: .2px; }
        .modal-actions { display:flex; gap: 8px; align-items:center; }
        .modal-btn { padding: 8px 12px; border: 1px solid var(--border); border-radius: 10px; background: var(--white); cursor:pointer; font-weight:600; color: var(--ink); transition: all .15s ease; }
        .modal-btn:hover { background: var(--bg-soft); }
        .modal-btn.primary { background: linear-gradient(90deg, var(--brand-start), var(--brand-end)); color:#fff; border-color: transparent; }
        .modal-btn.primary:hover { filter: brightness(.97); }
        .modal-btn.ghost { background: transparent; border-color: var(--border); }
        .modal-content { position: relative; flex: 1; background:#111827; }
        .modal-iframe { width: 100%; height: 100%; border: 0; background:#fff; }
        .loader { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; background: rgba(255,255,255,0.85); }
        .spinner { width:40px; height:40px; border:3px solid #e2e8f0; border-top-color: var(--brand-end); border-radius:50%; animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes fadeIn { from { opacity:0 } to { opacity:1 } }
    </style>
</head>
<body>
    <div class="dashboard-container">
    <?php $rolePrefix = session()->get('user_type') === 'interviewer' ? 'interviewer' : 'admin'; ?>
    <?php if ($rolePrefix === 'interviewer'): ?>
        <?= view('components/interviewer_sidebar') ?>
    <?php else: ?>
        <?= view('components/admin_sidebar') ?>
    <?php endif; ?>

    <main class="main-content">
        <header class="top-bar">
            <h1>Application Details</h1>
            <div class="user-info">
                <span>Welcome, <?= esc(session()->get('first_name')) ?> <?= esc(session()->get('last_name')) ?></span>
                <div class="user-avatar"><?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?></div>
            </div>
        </header>

        <div class="dashboard-content">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success" style="margin-bottom:12px;">
                    <?= esc(session()->getFlashdata('success')) ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-error" style="margin-bottom:12px;">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>
            <div class="form-card">
                <div class="header-flex">
                    <div>
                        <h2>#<?= str_pad($application['id'], 4, '0', STR_PAD_LEFT) ?> • <?= esc($application['first_name'].' '.$application['last_name']) ?></h2>
                        <div class="status-badge status-<?= esc($application['status']) ?>"><?= ucfirst(str_replace('_',' ',$application['status'])) ?></div>
                    </div>
                    <div class="toolbar">
                        <a class="btn-link" href="<?= base_url($rolePrefix . '/applications') ?>">← Back to list</a>
                        <?php if (!empty($application['resume_path'])): ?>
                            <button type="button" class="btn-link" onclick="openResumeModal()">📄 View Resume</button>
                            <a class="btn-link" href="<?= base_url($rolePrefix . '/applications/' . $application['id'] . '/resume?download=1') ?>">⬇️ Download</a>
                        <?php endif; ?>
                        <?php 
                            $isIGT = isset($application['company_name']) && strtoupper($application['company_name']) === 'IGT';
                            $notes = $application['decoded_notes'] ?? [];
                            $hasIGT = !empty($notes['igt']);
                            $hasSecond = !empty($notes['next_interview']);
                        ?>
                        <?php if ($rolePrefix === 'interviewer' && $isIGT && !$hasIGT && !$hasSecond): ?>
                            <a class="btn-link" href="<?= base_url('interviewer/applications/' . $application['id'] . '/igt') ?>">➕ IGT Interview</a>
                        <?php endif; ?>
                        <?php if ($rolePrefix === 'interviewer' && ($application['status'] ?? '') !== 'approved_for_endorsement'): ?>
                            <form action="<?= base_url('interviewer/applications/' . $application['id'] . '/approve') ?>" method="post" style="display:inline;">
                                <?php if (function_exists('csrf_field')) { echo csrf_field(); } ?>
                                <button type="submit" class="btn-link" title="Mark as Approved for Endorsement">✅ Approve for Endorsement</button>
                            </form>
                        <?php endif; ?>
                        <?php /* Deprecated quick-set removed: pending_for_next_interview */ ?>
                    </div>
                </div>

                <div class="detail-card">
                    <h3 class="section-title">Applicant</h3>
                    <div class="detail-grid">
                        <div class="detail-item"><span class="detail-label">Company</span><span class="detail-value"><?= esc($application['company_name']) ?></span></div>
                        <div class="detail-item"><span class="detail-label">Email</span><span class="detail-value"><?= esc($application['email_address']) ?></span></div>
                        <div class="detail-item"><span class="detail-label">Phone</span><span class="detail-value"><?= esc($application['phone_number'] ?? '—') ?></span></div>
                        <div class="detail-item"><span class="detail-label">Viber</span><span class="detail-value"><?= esc($application['viber_number'] ?? '—') ?></span></div>
                        <div class="detail-item"><span class="detail-label">Birthdate</span><span class="detail-value"><?= $application['birthdate'] ? date('M d, Y', strtotime($application['birthdate'])) : '—' ?></span></div>
                        <div class="detail-item"><span class="detail-label">BPO Experience</span><span class="detail-value"><?= esc($application['bpo_experience'] ?? '—') ?></span></div>
                        <div class="detail-item"><span class="detail-label">Education</span><span class="detail-value"><?= esc($application['educational_attainment'] ?? '—') ?></span></div>
                        <div class="detail-item"><span class="detail-label">Recruiter Email</span><span class="detail-value"><?= esc($application['recruiter_email']) ?></span></div>
                    </div>

                    <h3 class="section-title">Address</h3>
                    <div class="detail-grid">
                        <div class="detail-item"><span class="detail-label">Street</span><span class="detail-value"><?= esc($application['street_address'] ?? '—') ?></span></div>
                        <div class="detail-item"><span class="detail-label">Barangay</span><span class="detail-value"><?= esc($application['barangay'] ?? '—') ?></span></div>
                        <div class="detail-item"><span class="detail-label">Municipality</span><span class="detail-value"><?= esc($application['municipality'] ?? '—') ?></span></div>
                        <div class="detail-item"><span class="detail-label">Province</span><span class="detail-value"><?= esc($application['province'] ?? '—') ?></span></div>
                    </div>

                    <h3 class="section-title">Meta</h3>
                    <div class="detail-grid">
                        <div class="detail-item"><span class="detail-label">Status</span><span class="detail-value"><?= ucfirst(str_replace('_',' ',$application['status'])) ?></span></div>
                        <div class="detail-item"><span class="detail-label">Date Applied</span><span class="detail-value"><?= date('M d, Y H:i', strtotime($application['created_at'])) ?></span></div>
                        <div class="detail-item"><span class="detail-label">Last Updated</span><span class="detail-value"><?= date('M d, Y H:i', strtotime($application['updated_at'])) ?></span></div>
                    </div>

                    <?php /* Next interview scheduling removed as requested */ ?>

                    <?php if (!empty($application['decoded_notes']['igt'])): $igt = $application['decoded_notes']['igt']; ?>
                        <h3 class="section-title">IGT Interview</h3>
                        <div class="detail-grid">
                            <div class="detail-item"><span class="detail-label">Program</span><span class="detail-value"><?= !empty($igt['program']) ? esc($igt['program']) : '—' ?></span></div>
                            <div class="detail-item"><span class="detail-label">Date</span><span class="detail-value"><?= !empty($igt['application_date']) ? date('M d, Y', strtotime($igt['application_date'])) : '—' ?></span></div>
                            <div class="detail-item"><span class="detail-label">TAG Result</span><span class="detail-value"><?= !empty($igt['tag_result']) ? esc($igt['tag_result']) : '—' ?></span></div>
                            <div class="detail-item"><span class="detail-label">Interviewer</span><span class="detail-value"><?= !empty($igt['interviewer_name']) ? esc($igt['interviewer_name']) : '—' ?></span></div>
                            <div class="detail-item"><span class="detail-label">Expected Salary</span><span class="detail-value"><?= isset($igt['expected_salary']) && $igt['expected_salary'] !== null ? number_format((float)$igt['expected_salary']) : '—' ?></span></div>
                            <div class="detail-item"><span class="detail-label">On-hold Salary</span><span class="detail-value"><?= isset($igt['on_hold_salary']) && $igt['on_hold_salary'] !== null ? number_format((float)$igt['on_hold_salary']) : '—' ?></span></div>
                            <div class="detail-item"><span class="detail-label">Pending Applications</span><span class="detail-value"><?= !empty($igt['pending_applications']) ? esc($igt['pending_applications']) : '—' ?></span></div>
                            <div class="detail-item"><span class="detail-label">Communication</span><span class="detail-value"><?= !empty($igt['communication']) ? esc($igt['communication']) : '—' ?></span></div>
                            <div class="detail-item" style="grid-column: 1 / -1;"><span class="detail-label">Basic Checkpoints</span><span class="detail-value"><?= !empty($igt['basic_checkpoints']) ? esc($igt['basic_checkpoints']) : '—' ?></span></div>
                            <div class="detail-item" style="grid-column: 1 / -1;"><span class="detail-label">Opportunity</span><span class="detail-value"><?= !empty($igt['opportunity']) ? esc($igt['opportunity']) : '—' ?></span></div>
                            <div class="detail-item" style="grid-column: 1 / -1;"><span class="detail-label">Availability</span><span class="detail-value"><?= !empty($igt['availability']) ? esc($igt['availability']) : '—' ?></span></div>
                            <div class="detail-item" style="grid-column: 1 / -1;"><span class="detail-label">Validated Source</span><span class="detail-value"><?= !empty($igt['validated_source']) ? esc($igt['validated_source']) : '—' ?></span></div>
                            <div class="detail-item"><span class="detail-label">Shift Preference</span><span class="detail-value"><?= !empty($igt['shift_preference']) ? esc($igt['shift_preference']) : '—' ?></span></div>
                            <div class="detail-item"><span class="detail-label">Work Preference</span><span class="detail-value"><?= !empty($igt['work_preference']) ? esc($igt['work_preference']) : '—' ?></span></div>
                            <div class="detail-item" style="grid-column: 1 / -1;"><span class="detail-label">Current Location</span><span class="detail-value"><?= !empty($igt['current_location']) ? esc($igt['current_location']) : '—' ?></span></div>
                            <div class="detail-item" style="grid-column: 1 / -1;"><span class="detail-label">Commute</span><span class="detail-value"><?= !empty($igt['commute']) ? esc($igt['commute']) : '—' ?></span></div>
                            <div class="detail-item" style="grid-column: 1 / -1;"><span class="detail-label">Government Numbers</span><span class="detail-value"><?= !empty($igt['govt_numbers']) ? esc($igt['govt_numbers']) : '—' ?></span></div>
                            <div class="detail-item" style="grid-column: 1 / -1;"><span class="detail-label">Education</span><span class="detail-value"><?= !empty($igt['education']) ? nl2br(esc($igt['education'])) : '—' ?></span></div>
                            <div class="detail-item" style="grid-column: 1 / -1;"><span class="detail-label">Work Experience</span><span class="detail-value"><?= !empty($igt['work_experience']) ? nl2br(esc($igt['work_experience'])) : '—' ?></span></div>
                            <div class="detail-item"><span class="detail-label">Updated At</span><span class="detail-value"><?= !empty($igt['updated_at']) ? date('M d, Y H:i', strtotime($igt['updated_at'])) : '—' ?></span></div>
                        </div>
                    <?php endif; ?>

                    <?php /* Scheduling form removed as requested */ ?>
                </div>
            </div>
        </div>
    </main>
</div>

<?= view('components/sidebar_script') ?>
<div id="resumeOverlay" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="resumeTitle">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title" id="resumeTitle">Resume Preview</div>
            <div class="modal-actions">
                <a id="openNewTabBtn" class="modal-btn ghost" href="#" target="_blank" rel="noopener">Open in new tab</a>
                <a id="downloadResumeBtn" class="modal-btn" href="#">Download</a>
                <button id="closeResumeBtn" class="modal-btn primary" onclick="closeResumeModal()">Close</button>
            </div>
        </div>
        <div class="modal-content">
            <div id="resumeLoader" class="loader" aria-hidden="true"><div class="spinner"></div></div>
            <iframe id="resumeFrame" class="modal-iframe"></iframe>
        </div>
    </div>
    
</div>
<script>
    (function() {
        const overlay = document.getElementById('resumeOverlay');
        const frame = document.getElementById('resumeFrame');
        const loader = document.getElementById('resumeLoader');
        const downloadBtn = document.getElementById('downloadResumeBtn');
        const openTabBtn = document.getElementById('openNewTabBtn');
        const closeBtn = document.getElementById('closeResumeBtn');
    const base = '<?= base_url($rolePrefix . '/applications/' . $application['id']) ?>';

        window.openResumeModal = function() {
            loader.style.display = 'flex';
            frame.src = base + '/resume';
            downloadBtn.href = base + '/resume?download=1';
            openTabBtn.href = base + '/resume';
            overlay.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            // focus the close button for accessibility
            setTimeout(() => closeBtn.focus(), 0);
            document.addEventListener('keydown', escHandler);
        }

        window.closeResumeModal = function() {
            overlay.style.display = 'none';
            frame.src = '';
            document.body.style.overflow = '';
            document.removeEventListener('keydown', escHandler);
        }

        function escHandler(e) { if (e.key === 'Escape') closeResumeModal(); }

        // Close when clicking outside the modal
        overlay.addEventListener('click', (e) => { if (e.target === overlay) closeResumeModal(); });

        frame.addEventListener('load', () => { loader.style.display = 'none'; });
    })();
</script>
</body>
</html>
