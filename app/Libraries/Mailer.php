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

        // Safety: When using Gmail SMTP, enforce From to match the authenticated account
        $host = strtolower((string) ($config->SMTPHost ?? ''));
        // For Gmail, enforce From matching SMTPUser unless aliases are explicitly allowed
        if (strpos($host, 'gmail.com') !== false && !empty($config->SMTPUser)) {
            if (empty($config->allowAlias) || $config->allowAlias === false) {
                $fromEmail = $config->SMTPUser; // safer default
            }
        }

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

        // Optional reply-to
        if (!empty($options['replyTo'])) {
            if (is_array($options['replyTo'])) {
                $email->setReplyTo($options['replyTo'][0], $options['replyTo'][1] ?? null);
            } elseif (is_string($options['replyTo'])) {
                $email->setReplyTo($options['replyTo']);
            }
        }

        if (!empty($options['attachments']) && is_array($options['attachments'])) {
            foreach ($options['attachments'] as $path) {
                if (is_string($path) && is_file($path)) {
                    $email->attach($path);
                }
            }
        }

        // Calendar invite (ICS) support via temporary file attachment
        $tmpFiles = [];
        if (!empty($options['ics']) && is_array($options['ics'])) {
            $icsContent = self::buildIcs($options['ics'], $subject);
            if ($icsContent) {
                $tmpDir = WRITEPATH . 'tmp';
                if (!is_dir($tmpDir)) { @mkdir($tmpDir, 0755, true); }
                $tmpPath = $tmpDir . DIRECTORY_SEPARATOR . 'invite_' . uniqid() . '.ics';
                if (@file_put_contents($tmpPath, $icsContent) !== false) {
                    $email->attach($tmpPath, 'attachment', 'invite.ics', 'text/calendar');
                    $tmpFiles[] = $tmpPath;
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

        // Cleanup temp files
        foreach ($tmpFiles as $f) {
            if (is_file($f)) { @unlink($f); }
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

    private static function buildIcs(array $data, string $fallbackSummary = 'Meeting') : ?string
    {
        try {
            $uid = bin2hex(random_bytes(8)) . '@rsd.local';
        } catch (\Throwable $e) {
            $uid = uniqid('rsd_', true) . '@rsd.local';
        }

        // Expect ISO strings; default to now + 1 hour if missing
        $start = !empty($data['start']) ? new \DateTime($data['start']) : new \DateTime('+1 hour');
        $end   = !empty($data['end'])   ? new \DateTime($data['end'])   : (clone $start)->modify('+1 hour');

        $dtStart = $start->format('Ymd\THis');
        $dtEnd   = $end->format('Ymd\THis');
        $now     = (new \DateTime())->format('Ymd\THis');

        $summary = $data['summary'] ?? $fallbackSummary;
        $description = $data['description'] ?? '';
        $location = $data['location'] ?? '';

        $organizer = '';
        if (!empty($data['organizer'])) {
            $name = isset($data['organizer']['name']) ? 'CN=' . self::escapeText($data['organizer']['name']) . ':' : '';
            $email = $data['organizer']['email'] ?? '';
            if ($email) {
                $organizer = "ORGANIZER;{$name}mailto:" . $email;
            }
        }

        $attendees = '';
        if (!empty($data['attendees']) && is_array($data['attendees'])) {
            foreach ($data['attendees'] as $att) {
                $name = isset($att['name']) ? 'CN=' . self::escapeText($att['name']) . ';' : '';
                $email = $att['email'] ?? '';
                if ($email) {
                    $attendees .= "ATTENDEE;{$name}ROLE=REQ-PARTICIPANT:mailto:{$email}\r\n";
                }
            }
        }

        $ics = "BEGIN:VCALENDAR\r\n" .
               "PRODID:-//RSD//EN\r\n" .
               "VERSION:2.0\r\n" .
               "CALSCALE:GREGORIAN\r\n" .
               "METHOD:REQUEST\r\n" .
               "BEGIN:VEVENT\r\n" .
               "DTSTAMP:{$now}Z\r\n" .
               "DTSTART:{$dtStart}Z\r\n" .
               "DTEND:{$dtEnd}Z\r\n" .
               "UID:{$uid}\r\n" .
               ($organizer ? $organizer . "\r\n" : '') .
               $attendees .
               "SUMMARY:" . self::escapeText($summary) . "\r\n" .
               (!empty($location) ? ("LOCATION:" . self::escapeText($location) . "\r\n") : '') .
               (!empty($description) ? ("DESCRIPTION:" . self::escapeText($description) . "\r\n") : '') .
               "END:VEVENT\r\n" .
               "END:VCALENDAR\r\n";
        return $ics;
    }

    private static function escapeText(string $s): string
    {
        // ICS text escaping for commas, semicolons, and newlines
        $s = str_replace(["\\", ",", ";", "\n", "\r"], ["\\\\", "\\,", "\\;", "\\n", ''], $s);
        return $s;
    }
}
