<?php /** @var string $applicantName */ /** @var string $company */ /** @var string $email */ /** @var string $phone */ /** @var string $linkAdmin */ /** @var string $linkInterviewer */ ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Endorsed Candidate</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin:0;padding:0;background:#f7fafc;font-family:Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#1f2937;">
  <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:#f7fafc;padding:24px 0;">
    <tr>
      <td>
        <table role="presentation" cellpadding="0" cellspacing="0" width="600" align="center" style="margin:0 auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
          <tr>
            <td style="padding:18px 20px;background:linear-gradient(90deg,#f59e0b,#f97316);color:#fff;font-weight:700;font-size:18px;">Endorsed Candidate</td>
          </tr>
          <tr>
            <td style="padding:20px;">
              <p style="margin:0 0 12px;">Hi Recruiter,</p>
              <p style="margin:0 0 12px;">The candidate below has been <strong>approved for endorsement</strong>.</p>
              <div style="margin:16px 0;padding:14px;border:1px solid #e5e7eb;border-radius:10px;background:#fafafa;">
                <div style="font-weight:700;"><?= htmlspecialchars($applicantName) ?></div>
                <div style="color:#6b7280;">Company: <strong><?= htmlspecialchars($company) ?></strong></div>
                <div style="color:#6b7280;">Email: <a href="mailto:<?= htmlspecialchars($email) ?>"><?= htmlspecialchars($email) ?></a></div>
                <div style="color:#6b7280;">Phone: <?= htmlspecialchars($phone) ?></div>
              </div>
              <div style="margin-top:12px;">
                <a href="<?= htmlspecialchars($linkAdmin) ?>" style="display:inline-block;margin-right:8px;padding:10px 14px;border-radius:10px;background:#111827;color:#fff;text-decoration:none;">View in Admin</a>
                <a href="<?= htmlspecialchars($linkInterviewer) ?>" style="display:inline-block;padding:10px 14px;border-radius:10px;border:1px solid #e5e7eb;color:#111827;text-decoration:none;">View Summary</a>
              </div>
              <p style="margin:16px 0 0;color:#6b7280;font-size:12px;">You can reply directly to this email to contact the candidate.</p>
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
