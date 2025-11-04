<?php
/**
 * Vars: $scheduledBy, $applicantName, $company, $scheduleHuman, $notes, $email, $phone, $linkAdmin, $linkInterviewer
 */
ob_start();
?>
  <h2 style="margin:0 0 12px;">Interview Scheduled</h2>
  <p><?= esc($scheduledBy) ?> scheduled an interview for <strong><?= esc($applicantName) ?></strong><?= $company ? ' (' . esc($company) . ')' : '' ?>.</p>
  <ul>
    <li>Date/Time: <?= esc($scheduleHuman ?: 'TBA') ?></li>
    <?php if (!empty($notes)) : ?><li>Notes: <?= esc($notes) ?></li><?php endif; ?>
    <li>Applicant Email: <?= esc($email ?: 'N/A') ?></li>
    <li>Applicant Phone: <?= esc($phone ?: 'N/A') ?></li>
  </ul>
  <p>View application:</p>
  <p>
    <?php if (!empty($linkInterviewer)) : ?><a class="btn" href="<?= esc($linkInterviewer) ?>">Interviewer view</a> <?php endif; ?>
    <?php if (!empty($linkAdmin)) : ?><a class="btn" href="<?= esc($linkAdmin) ?>" style="margin-left:8px;">Admin view</a><?php endif; ?>
  </p>
<?php
$content = ob_get_clean();
$title = 'Interview Scheduled';
include __DIR__.'/layout_simple.php';
