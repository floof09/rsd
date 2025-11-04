<?php

namespace App\Controllers;

class EmailTest extends BaseController
{
    // Admin-only simple test endpoint to verify SMTP configuration
    public function send()
    {
        if (!session()->get('isLoggedIn') || session()->get('user_type') !== 'admin') {
            return redirect()->to('/auth/login');
        }

        $to = trim((string) $this->request->getGet('to'));
        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', 'Provide a valid recipient email via ?to=address@example.com');
        }

        $email = service('email');
        $config = config('Email');

        $email->setFrom($config->fromEmail ?: $config->SMTPUser, $config->fromName ?: 'RSD Notifications');
        $email->setTo($to);
        $email->setSubject('RSD Test Email');
        $email->setMessage('<p>If you received this, SMTP is working.</p><p>Protocol: ' . esc($config->protocol) . ', Host: ' . esc($config->SMTPHost) . ', Port: ' . esc((string) $config->SMTPPort) . '</p>');

        $ok = false; $debug = '';
        try {
            $ok = $email->send();
            if (!$ok) {
                $debug = (string) $email->printDebugger(['headers']);
            }
        } catch (\Throwable $e) {
            $ok = false;
            $debug = $e->getMessage();
        }

        if ($ok) {
            return redirect()->back()->with('success', 'Test email sent to ' . $to);
        }
        // Truncate long debug output
        if (strlen($debug) > 2000) { $debug = substr($debug, 0, 2000) . '...'; }
        return redirect()->back()->with('error', 'Test email failed. Details: ' . $debug);
    }
}
