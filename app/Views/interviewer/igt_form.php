<?php
/** @var array $application */
/** @var array $igt */
$errors = session('errors') ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IGT Additional Interview</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/application-form.css') ?>">
    <style>
        .field-error { color:#e53e3e; font-size:12px; margin-top:6px; }
        .section-title { margin: 18px 0 8px; color: #2d3748; font-weight: 700; }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php $errors = session('errors') ?? []; ?>
</head>
<body>
<div class="dashboard-container">
    <?= view('components/sidebar') ?>
    <main class="main-content">
        <header class="top-bar">
            <h1>IGT Additional Interview</h1>
            <div class="user-info">
                <span>Welcome, <?= esc(session()->get('first_name')) . ' ' . esc(session()->get('last_name')) ?></span>
                <div class="user-avatar"><?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?></div>
            </div>
        </header>

        <div class="dashboard-content">
            <div class="application-form-container">
                <div class="form-card">
                    <div class="form-header">
                        <h2>#<?= (int)$application['id'] ?> • <?= esc($application['first_name'] . ' ' . $application['last_name']) ?></h2>
                        <p>Company: <?= esc($application['company_name'] ?? '') ?> • Email: <?= esc($application['email_address'] ?? '') ?></p>
                    </div>

                    <?php if (session('error')): ?>
                        <div class="alert alert-error"><?= esc(session('error')) ?></div>
                    <?php endif; ?>
                    <?php if (session('success')): ?>
                        <div class="alert alert-success"><?= esc(session('success')) ?></div>
                    <?php endif; ?>

                    <form method="post" action="<?= base_url('interviewer/applications/' . (int)$application['id'] . '/igt/save') ?>">
                        <?php if (function_exists('csrf_field')) { echo csrf_field(); } ?>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Candidate</label>
                                <input type="text" value="<?= esc(($application['first_name'] ?? '') . ' ' . ($application['last_name'] ?? '')) ?>" disabled>
                            </div>
                            <div class="form-group">
                                <label for="igt_program">Program <span class="required">*</span></label>
                                <input type="text" id="igt_program" name="igt_program" placeholder="Travel Account - Blended" value="<?= esc(old('igt_program', $igt['program'] ?? '')) ?>" required>
                                <?php if (!empty($errors['igt_program'])): ?><div class="field-error"><?= esc($errors['igt_program']) ?></div><?php endif; ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="igt_application_date">Date <span class="required">*</span></label>
                                <input type="date" id="igt_application_date" name="igt_application_date" value="<?= esc(old('igt_application_date', $igt['application_date'] ?? '')) ?>" required>
                                <?php if (!empty($errors['igt_application_date'])): ?><div class="field-error"><?= esc($errors['igt_application_date']) ?></div><?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label for="igt_tag_result">TAG Result <span class="required">*</span></label>
                                <?php $tag = old('igt_tag_result', $igt['tag_result'] ?? ''); ?>
                                <select id="igt_tag_result" name="igt_tag_result" required>
                                    <option value="">Select Result</option>
                                    <option value="Passed" <?= $tag==='Passed'?'selected':'' ?>>Passed</option>
                                    <option value="Failed" <?= $tag==='Failed'?'selected':'' ?>>Failed</option>
                                </select>
                                <?php if (!empty($errors['igt_tag_result'])): ?><div class="field-error"><?= esc($errors['igt_tag_result']) ?></div><?php endif; ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="igt_interviewer_name">Interviewer's Name</label>
                                <input type="text" id="igt_interviewer_name" name="igt_interviewer_name" placeholder="Interviewer Name" value="<?= esc(old('igt_interviewer_name', $igt['interviewer_name'] ?? (session()->get('first_name') . ' ' . session()->get('last_name')))) ?>">
                                <?php if (!empty($errors['igt_interviewer_name'])): ?><div class="field-error"><?= esc($errors['igt_interviewer_name']) ?></div><?php endif; ?>
                            </div>
                            <div class="form-group">
                                <label for="igt_basic_checkpoints">Basic Checkpoints</label>
                                <input type="text" id="igt_basic_checkpoints" name="igt_basic_checkpoints" value="<?= esc(old('igt_basic_checkpoints', $igt['basic_checkpoints'] ?? '')) ?>">
                                <?php if (!empty($errors['igt_basic_checkpoints'])): ?><div class="field-error"><?= esc($errors['igt_basic_checkpoints']) ?></div><?php endif; ?>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="igt_opportunity">Opportunity</label>
                                <input type="text" id="igt_opportunity" name="igt_opportunity" value="<?= esc(old('igt_opportunity', $igt['opportunity'] ?? '')) ?>">
                            </div>
                            <div class="form-group">
                                <label for="igt_availability">Availability</label>
                                <input type="text" id="igt_availability" name="igt_availability" placeholder="ASAP" value="<?= esc(old('igt_availability', $igt['availability'] ?? '')) ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="igt_validated_source">Validated Source</label>
                                <?php $vs = old('igt_validated_source', $igt['validated_source'] ?? ''); ?>
                                <select id="igt_validated_source" name="igt_validated_source">
                                    <option value="">Select Source</option>
                                    <option value="RSD" <?= $vs==='RSD'?'selected':'' ?>>RSD</option>
                                    <option value="Other" <?= $vs==='Other'?'selected':'' ?>>Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="igt_shift_preference">Shift Preference</label>
                                <input type="text" id="igt_shift_preference" name="igt_shift_preference" placeholder="Good working with AM/Night/Graveyard" value="<?= esc(old('igt_shift_preference', $igt['shift_preference'] ?? '')) ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="igt_work_preference">Work Preference</label>
                                <input type="text" id="igt_work_preference" name="igt_work_preference" placeholder="Good work at home" value="<?= esc(old('igt_work_preference', $igt['work_preference'] ?? '')) ?>">
                            </div>
                            <div class="form-group">
                                <label for="igt_expected_salary">Expected / Non-negotiable Salary</label>
                                <input type="number" id="igt_expected_salary" name="igt_expected_salary" placeholder="19000" value="<?= esc(old('igt_expected_salary', $igt['expected_salary'] ?? '')) ?>" step="1" min="0">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="igt_on_hold_salary">On-hold Salary</label>
                                <input type="number" id="igt_on_hold_salary" name="igt_on_hold_salary" placeholder="20000" value="<?= esc(old('igt_on_hold_salary', $igt['on_hold_salary'] ?? '')) ?>" step="1" min="0">
                            </div>
                            <div class="form-group">
                                <label for="igt_pending_applications">Pending Applications</label>
                                <?php $pa = old('igt_pending_applications', $igt['pending_applications'] ?? 'NONE'); ?>
                                <select id="igt_pending_applications" name="igt_pending_applications">
                                    <option value="NONE" <?= $pa==='NONE'?'selected':'' ?>>NONE</option>
                                    <option value="Pending" <?= $pa==='Pending'?'selected':'' ?>>Pending</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label for="igt_current_location">Current Location</label>
                            <input type="text" id="igt_current_location" name="igt_current_location" placeholder="Dasmarinas Cavite, or Dasmarinas - 30 mins - Balic-Balic" value="<?= esc(old('igt_current_location', $igt['current_location'] ?? '')) ?>">
                        </div>

                        <div class="form-group full-width">
                            <label for="igt_commute">Commute</label>
                            <input type="text" id="igt_commute" name="igt_commute" placeholder="NONE" value="<?= esc(old('igt_commute', $igt['commute'] ?? '')) ?>">
                        </div>

                        <div class="form-group full-width">
                            <label for="igt_govt_numbers">Government Numbers</label>
                            <input type="text" id="igt_govt_numbers" name="igt_govt_numbers" placeholder="Completed" value="<?= esc(old('igt_govt_numbers', $igt['govt_numbers'] ?? '')) ?>">
                        </div>

                        <div class="form-group full-width">
                            <label for="igt_education">Education</label>
                            <textarea id="igt_education" name="igt_education" rows="3" placeholder="College - any course, Batch year 2018 with Diploma Industrial technology - 2005&#10;Telephone operator Years: WAH:1/20 until account closed&#10;Voice International healthcare- seasonal account"><?= esc(old('igt_education', $igt['education'] ?? '')) ?></textarea>
                        </div>

                        <div class="form-group full-width">
                            <label for="igt_work_experience">Work Experience (Company / Position / inclusive dates / Reason for leaving)</label>
                            <textarea id="igt_work_experience" name="igt_work_experience" rows="4" placeholder="Concentrix - Lipa Batangas - 20,000 - Personal account&#10;VXI - Telco - 25,000 - Blended - 2 years"><?= esc(old('igt_work_experience', $igt['work_experience'] ?? '')) ?></textarea>
                        </div>

                        <div class="form-group full-width">
                            <label for="igt_communication">Communication Assessment</label>
                            <?php $comm = old('igt_communication', $igt['communication'] ?? ''); ?>
                            <select id="igt_communication" name="igt_communication">
                                <option value="">Select</option>
                                <option value="Good" <?= $comm==='Good'?'selected':'' ?>>Good</option>
                                <option value="Excellent" <?= $comm==='Excellent'?'selected':'' ?>>Excellent</option>
                                <option value="Fair" <?= $comm==='Fair'?'selected':'' ?>>Fair</option>
                                <option value="Poor" <?= $comm==='Poor'?'selected':'' ?>>Poor</option>
                            </select>
                        </div>

                        <div class="form-actions">
                            <a class="btn btn-outline" href="<?= base_url('interviewer/applications/' . (int)$application['id']) ?>">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save IGT Interview</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <?= view('components/sidebar_script') ?>
</div>
</body>
</html>
