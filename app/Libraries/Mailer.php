<?php

namespace App\Libraries;

use App\Models\SystemLogModel;
use CodeIgniter\I18n\Time;

class Mailer
{
    /**
     * Send an email using configured transport.
     *
     * Options:
     * - fromEmail, fromName
     * - cc (string|array), bcc (string|array)
     * - attachments (array of file paths)
     * - log (bool) whether to log to SystemLogModel
     */
    public static function send($to, string $subject, string $htmlMessage, array $options = []): bool
    {
        $email = service('email');
        $config = config('Email');

        $fromEmail = $options['fromEmail'] ?? ($config->fromEmail ?? 'no-reply@example.com');
        $fromName  = $options['fromName']  ?? ($config->fromName  ?? 'RSD Notifications');

        $email->setFrom($fromEmail, $fromName);
        $email->setTo($to);
        if (!empty($options['cc'])) {
            $email->setCC($options['cc']);
        }
        if (!empty($options['bcc'])) {
            $email->setBCC($options['bcc']);
        }

        $email->setSubject($subject);
        $email->setMessage($htmlMessage);

        if (!empty($options['attachments']) && is_array($options['attachments'])) {
            foreach ($options['attachments'] as $path) {
                if (is_string($path) && is_file($path)) {
                    $email->attach($path);
                }
            }
        }

        $ok = false;
        $errorMsg = null;
        try {
            $ok = $email->send();
            if (!$ok) {
                $errorMsg = (string) $email->printDebugger(['headers']);
            }
        } catch (\Throwable $e) {
            $ok = false;
            $errorMsg = $e->getMessage();
        }

        // Optional logging to system logs
        $shouldLog = array_key_exists('log', $options) ? (bool) $options['log'] : true;
        if ($shouldLog) {
            try {
                $logger = new SystemLogModel();
                if ($ok) {
                    $logger->logActivity(
                        'Email Sent',
                        'email',
                        'To: ' . (is_array($to) ? implode(', ', $to) : $to) . ' | Subject: ' . $subject,
                        session()->get('user_id')
                    );
                } else {
                    $logger->logActivity(
                        'Email Failed',
                        'email',
                        'To: ' . (is_array($to) ? implode(', ', $to) : $to) . ' | Subject: ' . $subject . ' | Error: ' . (string) $errorMsg,
                        session()->get('user_id')
                    );
                }
            } catch (\Throwable $e) {
                // Swallow logging errors to not impact the caller
            }
        }

        if (!$ok && $errorMsg) {
            log_message('error', 'Email send failed: ' . $errorMsg);
        }

        return (bool) $ok;
    }
}
