<?php
/**
 * Vars: $applicantName, $company, $email, $phone, $location, $linkAdmin, $linkInterviewer
 */
ob_start();
?>
  <h2 style="margin:0 0 12px;">New Application</h2>
  <p>A new application has been submitted by <strong><?= esc($applicantName) ?></strong><?= $company ? ' for <strong>'.esc($company).'</strong>' : '' ?>.</p>
  <ul>
    <li>Email: <?= esc($email ?: 'N/A') ?></li>
    <li>Phone: <?= esc($phone ?: 'N/A') ?></li>
    <?php if (!empty($location)) : ?><li>Location: <?= esc($location) ?></li><?php endif; ?>
  </ul>
  <p>View application:</p>
  <p>
    <?php if (!empty($linkInterviewer)) : ?><a class="btn" href="<?= esc($linkInterviewer) ?>">Interviewer view</a> <?php endif; ?>
    <?php if (!empty($linkAdmin)) : ?><a class="btn" href="<?= esc($linkAdmin) ?>" style="margin-left:8px;">Admin view</a><?php endif; ?>
  </p>
<?php
$content = ob_get_clean();
$title = 'New Application';
include __DIR__.'/layout_simple.php';
