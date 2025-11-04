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
        $config = config('Email');
        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            // Fall back to logged-in user's email or configured sender
            $to = (string) (session()->get('email') ?: $config->fromEmail ?: $config->SMTPUser ?: '');
            if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
                return redirect()->back()->with('error', 'Provide a valid recipient email via ?to=address@example.com');
            }
        }

    $email = service('email');

        // Quick sanity check for placeholder values
        $placeholders = [
            'yourgmailaddress@gmail.com',
            'your-app-password-here',
            'no-reply@example.com'
        ];
        $hasPlaceholders = in_array((string)$config->SMTPUser, $placeholders, true)
            || in_array((string)$config->SMTPPass, $placeholders, true);
        if ($hasPlaceholders) {
            return redirect()->back()->with('error', 'SMTP is not configured yet. Update .env: email.SMTPUser, email.SMTPPass (Gmail App Password), and email.fromEmail, then restart Apache.');
        }

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
