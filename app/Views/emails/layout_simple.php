<?php
// Simple shared layout variables: $title, $content (HTML)
?><!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= esc($title ?? 'RSD Notification') ?></title>
  <style>
    body{font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:#f7fafc;color:#1a202c;margin:0;padding:24px}
    .card{max-width:640px;margin:0 auto;background:#fff;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden}
    .header{background:linear-gradient(90deg,#ff9800,#ff6d00);color:#fff;padding:16px 20px;font-weight:600}
    .content{padding:20px}
    .muted{color:#718096;font-size:12px;margin-top:16px}
    a.btn{display:inline-block;background:#ff8a00;color:#fff;text-decoration:none;padding:10px 14px;border-radius:6px}
    ul{padding-left:20px}
  </style>
</head>
<body>
  <div class="card">
    <div class="header">RSD Notifications</div>
    <div class="content">
      <?= $content ?? '' ?>
      <p class="muted">This is an automated message from the RSD system.</p>
    </div>
  </div>
</body>
</html>