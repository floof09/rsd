<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Application - RSD</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/interviewer-dashboard.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/application-form.css') ?>?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Leaflet for map picker -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        /* Modernize edit page visuals */
        .form-card.card { border:1px solid #e5e7eb; border-radius:12px; box-shadow:0 2px 6px rgba(15,23,42,0.04); }
        .form-header p { color:#64748b; margin-top:4px; }
        .section-title { margin:0 0 12px; font-size:16px; color:#0f172a; font-weight:700; }
        .form-row { display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:16px 20px; }
        .form-row .form-group.full-width { grid-column: 1 / -1; }
        .form-group label { font-size:13px; color:#334155; font-weight:600; margin-bottom:6px; display:block; }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="date"],
        .form-group input[type="datetime-local"],
        .form-group input[type="tel"],
        .form-group select,
        .form-group textarea { width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:10px; background:#fff; color:#0f172a; outline:none; transition:border-color .15s ease, box-shadow .15s ease; }
        .form-group textarea { resize:vertical; }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus { border-color:#c7d2fe; box-shadow:0 0 0 3px rgba(99,102,241,.2); }
        .form-section.card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px; box-shadow:0 1px 3px rgba(15,23,42,0.03); }
        .hint { color:#94a3b8; font-size:12px; margin-top:6px; }
        .btn { cursor:pointer; }

        /* (Reverted) Use default button styles for resume actions to match prior design */
    </style>
</head>
<body>
<?php 
    $rolePrefix = session()->get('user_type') === 'interviewer' ? 'interviewer' : 'admin';
    $a = $application; 
    // Helper to show 10-digit PH mobile without prefix
    $toLocal10 = function($e164) {
        if (!$e164) return '';
        $d = preg_replace('/\D/','', $e164);
        if (strpos($d, '63') === 0) $d = substr($d, 2);
        if (strpos($d, '0') === 0) $d = substr($d, 1);
        return $d;
    };
    $notes = $a['decoded_notes'] ?? [];
    $hasSecond = !empty($notes['next_interview']) || !empty($notes['igt']);
?>
<div class="dashboard-container interviewer-dashboard">
    <?php if ($rolePrefix === 'interviewer'): ?>
        <?= view('components/interviewer_sidebar') ?>
    <?php else: ?>
        <?= view('components/admin_sidebar') ?>
    <?php endif; ?>

    <main class="main-content">
        <header class="top-bar">
            <h1>Edit Application #<?= str_pad($a['id'], 4, '0', STR_PAD_LEFT) ?></h1>
            <div class="user-info" style="gap:8px;">
                <span>Welcome, <?= esc(session()->get('first_name')) ?> <?= esc(session()->get('last_name')) ?></span>
                <div class="user-avatar"><?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?></div>
            </div>
        </header>

        <div class="dashboard-content">
            <div class="application-form-container">
                <div class="form-card card">
                    <div class="form-header" style="margin-bottom:12px;">
                        <h2>Update Applicant Details</h2>
                        <p>Make changes and save. Status updates are handled separately.</p>
                    </div>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-error"><?= esc(session()->getFlashdata('error')) ?></div>
                    <?php endif; ?>
                    <?php $errors = session()->getFlashdata('errors') ?? []; $resumeFieldError = session()->getFlashdata('field_error_resume'); ?>

                    <form action="<?= base_url($rolePrefix . '/applications/' . $a['id'] . '/update') ?>" method="POST" enctype="multipart/form-data">
                        <?php if (function_exists('csrf_field')) { echo csrf_field(); } ?>
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="company_name">Company <span class="required">*</span></label>
                                <select id="company_name" name="company_name" required>
                                    <?php $company = old('company_name', $a['company_name']); ?>
                                    <option value="">-- Select Company --</option>
                                    <option value="Everise" <?= $company === 'Everise' ? 'selected' : '' ?>>Everise</option>
                                    <option value="IGT" <?= $company === 'IGT' ? 'selected' : '' ?>>IGT</option>
                                </select>
                                <?php if (!empty($errors['company_name'])): ?><div class="field-error"><?= esc($errors['company_name']) ?></div><?php endif; ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name <span class="required">*</span></label>
                                <input type="text" id="first_name" name="first_name" value="<?= esc(old('first_name', $a['first_name'])) ?>" required>
                                <?php if (!empty($errors['first_name'])): ?><div class="field-error"><?= esc($errors['first_name']) ?></div><?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name <span class="required">*</span></label>
                                <input type="text" id="last_name" name="last_name" value="<?= esc(old('last_name', $a['last_name'])) ?>" required>
                                <?php if (!empty($errors['last_name'])): ?><div class="field-error"><?= esc($errors['last_name']) ?></div><?php endif; ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="email_address">Email <span class="required">*</span></label>
                                <input type="email" id="email_address" name="email_address" value="<?= esc(old('email_address', $a['email_address'])) ?>" required>
                                <?php if (!empty($errors['email_address'])): ?><div class="field-error"><?= esc($errors['email_address']) ?></div><?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label for="phone_number">Phone</label>
                                <div class="phone-input-wrapper">
                                    <span class="phone-prefix">+63</span>
                                    <input type="tel" id="phone_number" name="phone_number" maxlength="10" inputmode="numeric" value="<?= esc(old('phone_number', $toLocal10($a['phone_number'] ?? ''))) ?>" oninput="sanitizeMobile(this)">
                                </div>
                                <?php if (!empty($errors['phone_number'])): ?><div class="field-error"><?= esc($errors['phone_number']) ?></div><?php endif; ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="viber_number">Viber</label>
                                <div class="phone-input-wrapper">
                                    <span class="phone-prefix">+63</span>
                                    <input type="tel" id="viber_number" name="viber_number" maxlength="10" inputmode="numeric" value="<?= esc(old('viber_number', $toLocal10($a['viber_number'] ?? ''))) ?>" oninput="sanitizeMobile(this)">
                                </div>
                                <?php if (!empty($errors['viber_number'])): ?><div class="field-error"><?= esc($errors['viber_number']) ?></div><?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label for="birthdate">Birthdate</label>
                                <input type="date" id="birthdate" name="birthdate" value="<?= esc(old('birthdate', $a['birthdate'])) ?>">
                                <?php if (!empty($errors['birthdate'])): ?><div class="field-error"><?= esc($errors['birthdate']) ?></div><?php endif; ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="street_address">Street Address</label>
                                <input type="text" id="street_address" name="street_address" value="<?= esc(old('street_address', $a['street_address'])) ?>">
                                <div style="margin-top:8px;">
                                    <button type="button" class="btn-map" onclick="openMapModal()">Pick on Map</button>
                                </div>
                                <?php if (!empty($errors['street_address'])): ?><div class="field-error"><?= esc($errors['street_address']) ?></div><?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label for="barangay">Barangay</label>
                                <input type="text" id="barangay" name="barangay" value="<?= esc(old('barangay', $a['barangay'])) ?>">
                                <?php if (!empty($errors['barangay'])): ?><div class="field-error"><?= esc($errors['barangay']) ?></div><?php endif; ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="municipality">Municipality</label>
                                <input type="text" id="municipality" name="municipality" value="<?= esc(old('municipality', $a['municipality'])) ?>">
                                <?php if (!empty($errors['municipality'])): ?><div class="field-error"><?= esc($errors['municipality']) ?></div><?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label for="province">Province</label>
                                <input type="text" id="province" name="province" value="<?= esc(old('province', $a['province'])) ?>">
                                <?php if (!empty($errors['province'])): ?><div class="field-error"><?= esc($errors['province']) ?></div><?php endif; ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="bpo_experience">BPO Experience</label>
                                <input type="text" id="bpo_experience" name="bpo_experience" value="<?= esc(old('bpo_experience', $a['bpo_experience'])) ?>">
                            </div>
                            <div class="form-group">
                                <label for="educational_attainment">Educational Attainment</label>
                                <input type="text" id="educational_attainment" name="educational_attainment" value="<?= esc(old('educational_attainment', $a['educational_attainment'])) ?>">
                            </div>
                        </div>

                        <div class="form-group full-width recruiter-box">
                            <label for="recruiter_email">Recruiter Email <span class="required">*</span></label>
                            <input type="email" id="recruiter_email" name="recruiter_email" value="<?= esc(old('recruiter_email', $a['recruiter_email'])) ?>" required>
                            <?php if (!empty($errors['recruiter_email'])): ?><div class="field-error"><?= esc($errors['recruiter_email']) ?></div><?php endif; ?>
                        </div>

                        <?php if (!$hasSecond): ?>
                        <div class="form-section card" style="margin-top:16px;">
                            <div class="section-title">Schedule another interview (optional)</div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="next_interviewer_email">Interviewer Email</label>
                                    <input type="email" id="next_interviewer_email" name="next_interviewer_email" value="<?= esc(old('next_interviewer_email')) ?>">
                                    <?php if (!empty($errors['next_interviewer_email'])): ?><div class="field-error"><?= esc($errors['next_interviewer_email']) ?></div><?php endif; ?>
                                </div>
                                <div class="form-group">
                                    <label for="next_interview_datetime">Interview Date & Time</label>
                                    <input type="datetime-local" id="next_interview_datetime" name="next_interview_datetime" value="<?= esc(old('next_interview_datetime')) ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group full-width">
                                    <label for="next_interview_notes">Notes for the interviewer</label>
                                    <textarea id="next_interview_notes" name="next_interview_notes" rows="3"><?= esc(old('next_interview_notes')) ?></textarea>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                            <div class="alert alert-info" style="margin-top:16px;">An additional interview record already exists (scheduled or IGT). You can't add another here.</div>
                        <?php endif; ?>

                        <?php $igt = $notes['igt'] ?? null; if ($igt): ?>
                        <div class="form-section card" style="margin-top:16px;">
                            <div class="section-title">IGT Interview</div>
                            <input type="hidden" name="igt_present" value="1">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="igt_program">Program</label>
                                    <input type="text" id="igt_program" name="igt_program" value="<?= esc(old('igt_program', $igt['program'] ?? '')) ?>">
                                </div>
                                <div class="form-group">
                                    <label for="igt_application_date">Application Date</label>
                                    <input type="date" id="igt_application_date" name="igt_application_date" value="<?= esc(old('igt_application_date', $igt['application_date'] ?? '')) ?>">
                                </div>
                                <div class="form-group">
                                    <label for="igt_tag_result">TAG Result</label>
                                    <?php $res = old('igt_tag_result', $igt['tag_result'] ?? ''); ?>
                                    <select id="igt_tag_result" name="igt_tag_result">
                                        <option value="">-- Select --</option>
                                        <option value="Passed" <?= $res==='Passed'?'selected':'' ?>>Passed</option>
                                        <option value="Failed" <?= $res==='Failed'?'selected':'' ?>>Failed</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="igt_interviewer_name">Interviewer Name</label>
                                    <input type="text" id="igt_interviewer_name" name="igt_interviewer_name" value="<?= esc(old('igt_interviewer_name', $igt['interviewer_name'] ?? '')) ?>">
                                </div>
                                <div class="form-group">
                                    <label for="igt_expected_salary">Expected Salary</label>
                                    <input type="text" id="igt_expected_salary" name="igt_expected_salary" value="<?= esc(old('igt_expected_salary', $igt['expected_salary'] ?? '')) ?>">
                                </div>
                                <div class="form-group">
                                    <label for="igt_on_hold_salary">On-hold Salary</label>
                                    <input type="text" id="igt_on_hold_salary" name="igt_on_hold_salary" value="<?= esc(old('igt_on_hold_salary', $igt['on_hold_salary'] ?? '')) ?>">
                                </div>
                                <div class="form-group">
                                    <label for="igt_pending_applications">Pending Applications</label>
                                    <?php $pa = old('igt_pending_applications', $igt['pending_applications'] ?? ''); ?>
                                    <select id="igt_pending_applications" name="igt_pending_applications">
                                        <option value="">-- Select --</option>
                                        <option value="NONE" <?= $pa==='NONE'?'selected':'' ?>>NONE</option>
                                        <option value="Pending" <?= $pa==='Pending'?'selected':'' ?>>Pending</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="igt_communication">Communication</label>
                                    <?php $comm = old('igt_communication', $igt['communication'] ?? ''); ?>
                                    <select id="igt_communication" name="igt_communication">
                                        <option value="">-- Select --</option>
                                        <option value="Excellent" <?= $comm==='Excellent'?'selected':'' ?>>Excellent</option>
                                        <option value="Good" <?= $comm==='Good'?'selected':'' ?>>Good</option>
                                        <option value="Fair" <?= $comm==='Fair'?'selected':'' ?>>Fair</option>
                                        <option value="Poor" <?= $comm==='Poor'?'selected':'' ?>>Poor</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group full-width">
                                    <label for="igt_basic_checkpoints">Basic Checkpoints</label>
                                    <input type="text" id="igt_basic_checkpoints" name="igt_basic_checkpoints" value="<?= esc(old('igt_basic_checkpoints', $igt['basic_checkpoints'] ?? '')) ?>">
                                </div>
                                <div class="form-group full-width">
                                    <label for="igt_opportunity">Opportunity</label>
                                    <input type="text" id="igt_opportunity" name="igt_opportunity" value="<?= esc(old('igt_opportunity', $igt['opportunity'] ?? '')) ?>">
                                </div>
                                <div class="form-group full-width">
                                    <label for="igt_availability">Availability</label>
                                    <input type="text" id="igt_availability" name="igt_availability" value="<?= esc(old('igt_availability', $igt['availability'] ?? '')) ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="igt_validated_source">Validated Source</label>
                                    <?php $vs = old('igt_validated_source', $igt['validated_source'] ?? ''); ?>
                                    <select id="igt_validated_source" name="igt_validated_source">
                                        <option value="">-- Select --</option>
                                        <option value="RSD" <?= $vs==='RSD'?'selected':'' ?>>RSD</option>
                                        <option value="Other" <?= $vs==='Other'?'selected':'' ?>>Other</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="igt_current_location">Current Location</label>
                                    <input type="text" id="igt_current_location" name="igt_current_location" value="<?= esc(old('igt_current_location', $igt['current_location'] ?? '')) ?>">
                                </div>
                                <div class="form-group">
                                    <label for="igt_commute">Commute</label>
                                    <input type="text" id="igt_commute" name="igt_commute" value="<?= esc(old('igt_commute', $igt['commute'] ?? '')) ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group full-width">
                                    <label for="igt_govt_numbers">Government Numbers</label>
                                    <input type="text" id="igt_govt_numbers" name="igt_govt_numbers" value="<?= esc(old('igt_govt_numbers', $igt['govt_numbers'] ?? '')) ?>">
                                </div>
                                <div class="form-group full-width">
                                    <label for="igt_education">Education</label>
                                    <textarea id="igt_education" name="igt_education" rows="3"><?= esc(old('igt_education', $igt['education'] ?? '')) ?></textarea>
                                </div>
                                <div class="form-group full-width">
                                    <label for="igt_work_experience">Work Experience</label>
                                    <textarea id="igt_work_experience" name="igt_work_experience" rows="3"><?= esc(old('igt_work_experience', $igt['work_experience'] ?? '')) ?></textarea>
                                </div>
                            </div>
                            <div class="hint">You can update the IGT details directly here.</div>
                        </div>
                        <?php endif; ?>

                        <div class="form-group full-width">
                            <label for="resume">Replace Resume (PDF only)</label>
                            <div class="file-upload-wrapper">
                                <input type="file" id="resume" name="resume" accept=".pdf" onchange="updateFileName(this)">
                                <div class="file-upload-display">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                        <polyline points="17 8 12 3 7 8"/>
                                        <line x1="12" y1="3" x2="12" y2="15"/>
                                    </svg>
                                    <span class="file-name">Choose PDF file or drag here</span>
                                    <span class="file-size"></span>
                                </div>
                            </div>
                            <div class="file-preview" id="filePreview" style="display: none;">
                                <div class="preview-header">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                        <polyline points="10 9 9 9 8 9"/>
                                    </svg>
                                    <div class="preview-info">
                                        <span class="preview-filename"></span>
                                        <span class="preview-filesize"></span>
                                    </div>
                                </div>
                                <div class="preview-actions">
                                    <button type="button" class="btn-preview" onclick="previewPDF()">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        Preview
                                    </button>
                                    <button type="button" class="btn-remove" onclick="removeFile()">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        </svg>
                                        Remove
                                    </button>
                                </div>
                            </div>
                            <?php if (!empty($resumeFieldError)): ?>
                                <div class="field-error"><?= esc($resumeFieldError) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($a['resume_path'])): ?>
                                <div style="margin-top:8px;">
                                    <a class="btn btn-outline" href="<?= base_url($rolePrefix . '/applications/' . $a['id'] . '/resume') ?>" target="_blank" rel="noopener">View current resume</a>
                                    <a class="btn btn-secondary" href="<?= base_url($rolePrefix . '/applications/' . $a['id'] . '/resume?download=1') ?>">Download</a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <a class="btn btn-outline" href="<?= base_url($rolePrefix . '/applications/' . $a['id']) ?>">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="<?= base_url('assets/js/interviewer-dashboard.js') ?>?v=<?= time() ?>"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    // Minimal reuse: sanitize mobile
    function sanitizeMobile(el) {
        let digits = (el.value || '').replace(/\D/g, '');
        if (digits.startsWith('0')) digits = digits.slice(1);
        if (digits.length > 10) digits = digits.slice(0, 10);
        el.value = digits;
    }

    // Map Picker (simplified copy)
    let map, marker, mapInitialized = false; let selectedLatLng=null, selectedDisplayName='';
    function openMapModal() {
        let modal = document.getElementById('mapModal');
        if (!modal) {
            modal = document.createElement('div');
            modal.className = 'modal'; modal.id = 'mapModal';
            modal.innerHTML = `
                <div class="modal-content map-modal-content">
                    <div class="modal-header">
                        <h3>Pick Address on Map</h3>
                        <button type="button" class="modal-close" onclick="closeMapModal()" aria-label="Close">✕</button>
                    </div>
                    <div class="modal-body map-modal-body">
                        <div class="map-pane">
                            <div class="map-toolbar">
                                <div class="search-box">
                                    <input type="text" id="mapSearch" placeholder="Search a place, street, or city...">
                                    <div class="address-suggestions" id="addressSuggestions"></div>
                                </div>
                            </div>
                            <div id="mapPicker"></div>
                            <div class="modal-footer">
                                <div class="selected-location-info"><span id="selectedLocationText">Drag the marker or search to choose a location.</span></div>
                                <button type="button" class="btn btn-secondary" onclick="closeMapModal()">Cancel</button>
                                <button type="button" class="btn btn-primary" onclick="applySelectedLocation()">Use this location</button>
                            </div>
                        </div>
                    </div>
                </div>`;
            document.body.appendChild(modal);
        }
        modal.style.display = 'flex'; document.body.style.overflow = 'hidden';
        setTimeout(initMap, 0);
    }
    function closeMapModal(){ const m=document.getElementById('mapModal'); if(m) m.style.display='none'; document.body.style.overflow=''; }
    function initMap(){
        if(mapInitialized){ setTimeout(()=>map.invalidateSize(), 50); return; }
        const defaultCenter=[14.5995,120.9842];
        map=L.map('mapPicker').setView(defaultCenter,12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'&copy; OpenStreetMap contributors'}).addTo(map);
        marker=L.marker(defaultCenter,{draggable:true}).addTo(map);
        marker.on('dragend',()=>{ const {lat,lng}=marker.getLatLng(); selectedLatLng={lat,lng}; selectedDisplayName='Selected location'; updateSelectedLocationText(lat,lng); });
        map.on('click',(e)=>{ marker.setLatLng(e.latlng); const {lat,lng}=e.latlng; selectedLatLng={lat,lng}; selectedDisplayName='Selected location'; updateSelectedLocationText(lat,lng); });
        mapInitialized=true; setTimeout(()=>map.invalidateSize(),100);
    }
    function updateSelectedLocationText(lat,lng,label){ const el=document.getElementById('selectedLocationText'); el.textContent=(label||'Selected location')+` (lat: ${lat.toFixed(5)}, lng: ${lng.toFixed(5)})`; }
    function applySelectedLocation(){ if(!selectedLatLng){ const {lat,lng}=marker.getLatLng(); selectedLatLng={lat,lng}; }
        const street=document.getElementById('street_address'); if(street && !street.value){ street.value = `${selectedDisplayName||'Selected location'} (${selectedLatLng.lat.toFixed(5)}, ${selectedLatLng.lng.toFixed(5)})`; }
        closeMapModal(); }

    // Resume uploader helpers (copied from interviewer application form)
    function updateFileName(input) {
        const filePreview = document.getElementById('filePreview');
        const fileName = document.querySelector('.file-name');
        const fileSize = document.querySelector('.file-size');
        const previewFilename = document.querySelector('.preview-filename');
        const previewFilesize = document.querySelector('.preview-filesize');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const fileSizeKB = (file.size / 1024).toFixed(2);
            fileName.textContent = file.name;
            fileSize.textContent = `${fileSizeKB} KB`;
            previewFilename.textContent = file.name;
            previewFilesize.textContent = `${fileSizeKB} KB`;
            filePreview.style.display = 'block';
        }
    }
    function previewPDF(){ const fileInput=document.getElementById('resume'); if(fileInput.files && fileInput.files[0]){ const fileURL=URL.createObjectURL(fileInput.files[0]); window.open(fileURL,'_blank'); } }
    function removeFile(){ const fi=document.getElementById('resume'); const fp=document.getElementById('filePreview'); const fn=document.querySelector('.file-name'); const fs=document.querySelector('.file-size'); fi.value=''; fn.textContent='Choose PDF file or drag here'; fs.textContent=''; fp.style.display='none'; }
</script>
</body>
</html>
