<?php
/**
 * Vars: $firstName, $company, $program, $applicationDate, $tagResult
 */
ob_start();
?>
  <h2 style="margin:0 0 12px;">Additional interview update<?= $company ? ' — '.esc($company) : '' ?></h2>
  <p>Hi <?= esc($firstName ?? 'there') ?>,</p>
  <p>We updated your additional interview details.</p>
  <ul>
    <?php if (!empty($program)) : ?><li>Program: <?= esc($program) ?></li><?php endif; ?>
    <?php if (!empty($applicationDate)) : ?><li>Interview Date: <?= esc($applicationDate) ?></li><?php endif; ?>
    <?php if (!empty($tagResult)) : ?><li>Result/Tag: <?= esc($tagResult) ?></li><?php endif; ?>
  </ul>
  <p>If you have questions, just reply to this email.</p>
<?php
$content = ob_get_clean();
$title = 'Interview Update';
include __DIR__.'/layout_simple.php';
