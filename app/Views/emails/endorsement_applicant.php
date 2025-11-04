<?php /** @var string $firstName */ /** @var string $company */ /** @var string|null $recruiterEmail */ ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>You have been endorsed</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin:0;padding:0;background:#f7fafc;font-family:Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#1f2937;">
  <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:#f7fafc;padding:24px 0;">
    <tr>
      <td>
        <table role="presentation" cellpadding="0" cellspacing="0" width="600" align="center" style="margin:0 auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
          <tr>
            <td style="padding:18px 20px;background:linear-gradient(90deg,#6366f1,#8b5cf6);color:#fff;font-weight:700;font-size:18px;">You have been endorsed</td>
          </tr>
          <tr>
            <td style="padding:20px;">
              <p style="margin:0 0 12px;">Hi <?= htmlspecialchars(ucfirst($firstName)) ?>,</p>
              <p style="margin:0 0 12px;">Great news! You've been <strong>endorsed</strong> for <strong><?= htmlspecialchars($company) ?></strong>. A recruiter will reach out to you with next steps.</p>
              <?php if (!empty($recruiterEmail)): ?>
              <p style="margin:0 0 12px;">If you need to follow up, you can reach the recruiter at <a href="mailto:<?= htmlspecialchars($recruiterEmail) ?>"><?= htmlspecialchars($recruiterEmail) ?></a>.</p>
              <?php endif; ?>
              <p style="margin:16px 0 0;color:#6b7280;font-size:12px;">This is an automated message from RSD.</p>
            </td>
          </tr>
          <tr>
            <td style="padding:14px 20px;color:#6b7280;font-size:12px;border-top:1px solid #e5e7eb;">RSD Notifications</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
