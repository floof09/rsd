<?php
/**
 * Variables expected:
 * $firstName, $company, $scheduleHuman (optional), $notes (optional)
 */
ob_start();
?>
  <h2 style="margin:0 0 12px;">We received your application<?= $company ? ' — '.esc($company) : '' ?></h2>
  <p>Hi <?= esc($firstName ?? 'there') ?>,</p>
  <p>Thanks for submitting your application<?= $company ? ' to <strong>'.esc($company).'</strong>' : '' ?>. Your information has been saved.</p>
  <?php if (!empty($scheduleHuman)) : ?>
    <p><strong>Second interview scheduled</strong></p>
    <ul>
      <li>Date/Time: <?= esc($scheduleHuman) ?></li>
      <?php if (!empty($notes)) : ?><li>Notes: <?= esc($notes) ?></li><?php endif; ?>
    </ul>
  <?php endif; ?>
  <p>If you have questions, just reply to this email.</p>
<?php
$content = ob_get_clean();
$title = 'Application Received';
include __DIR__.'/layout_simple.php';
