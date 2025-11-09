<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'RSD') ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">
    <?php
        // Choose bundle based on provided bodyClass
        $bundle = 'interviewer';
        if (isset($bodyClass) && strpos($bodyClass, 'admin') !== false) {
            $bundle = 'admin';
        }
    ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/' . $bundle . '.css') ?>?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
</head>
<body>
<div class="dashboard-container <?= esc($bodyClass ?? '') ?>">
