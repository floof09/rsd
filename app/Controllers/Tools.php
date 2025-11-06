<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Config\Database;

class Tools extends BaseController
{
    private function hasValidKey(): bool
    {
        $keyProvided = $this->request->getGet('key');
        $keyExpected = env('app.migrateKey');
        // If a key is configured, require it strictly
        if ($keyExpected) {
            return ($keyProvided && hash_equals((string) $keyExpected, (string) $keyProvided));
        }
        // No key configured: allow only in non-production environments to avoid blocking diagnostics
        return defined('ENVIRONMENT') && ENVIRONMENT !== 'production';
    }

    /**
     * Emit a few log messages to ensure a log file exists in writable/logs.
     * GET /tools/test-log?key=TOKEN
     */
    public function testLog(): ResponseInterface
    {
        if (!$this->hasValidKey()) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: invalid key');
        }

        // Use CI4's global logger helper
        log_message('debug', 'Test log: debug level active');
        log_message('info', 'Test log: info message');
        log_message('notice', 'Test log: notice message');
        log_message('warning', 'Test log: warning message');
        log_message('error', 'Test log: error message');

        return $this->response->setStatusCode(200)->setBody('Wrote test log messages. Now open /tools/logs to view.');
    }
    /**
     * Quick environment info to verify .env and config are loaded on the server.
     * GET /tools/env?key=TOKEN
     */
    public function env(): ResponseInterface
    {
        if (!$this->hasValidKey()) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: invalid key');
        }

        $appConfig = config('App');
        $data = [
            'ENVIRONMENT' => defined('ENVIRONMENT') ? ENVIRONMENT : '(undefined)',
            'CI_DEBUG'    => defined('CI_DEBUG') ? (CI_DEBUG ? 'true' : 'false') : '(undefined)',
            'baseURL'     => $appConfig->baseURL ?? null,
            'forceHTTPS'  => property_exists($appConfig, 'forceGlobalSecureRequests') ? ($appConfig->forceGlobalSecureRequests ? 'true' : 'false') : '(n/a)',
            'php_version' => PHP_VERSION,
            'server_time' => date('c'),
        ];

        return $this->response->setJSON($data);
    }

    public function migrate(): ResponseInterface
    {
        if (!$this->hasValidKey()) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: invalid key');
        }

        $migrate = Services::migrations();
        try {
            $migrate->latest();
            return $this->response->setStatusCode(200)->setBody('Migrations applied successfully.');
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setBody('Migration failed: ' . $e->getMessage());
        }
    }

    public function seedAdmin(): ResponseInterface
    {
        if (!$this->hasValidKey()) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: invalid key');
        }

        $email = trim((string) $this->request->getGet('email'));
        $password = (string) $this->request->getGet('password');
        $first = trim((string) $this->request->getGet('first')) ?: 'Admin';
        $last = trim((string) $this->request->getGet('last')) ?: 'User';

        if ($email === '' || $password === '') {
            return $this->response->setStatusCode(400)->setBody('Missing email or password');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $db = Database::connect();
        $builder = $db->table('users');

        $existing = $builder->select('id')->where('email', $email)->get()->getFirstRow();
        if ($existing) {
            $builder->where('id', $existing->id)->update([
                'password' => $hash,
                'first_name' => $first,
                'last_name' => $last,
                'user_type' => 'admin',
                'status' => 'active',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $builder->insert([
                'email' => $email,
                'password' => $hash,
                'first_name' => $first,
                'last_name' => $last,
                'user_type' => 'admin',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return $this->response->setStatusCode(200)->setBody('Admin user ensured/updated for ' . $email);
    }

    /**
     * Show the last N lines of the most recent log file in writable/logs.
     * GET /tools/logs?key=TOKEN&lines=200
     */
    public function logs(): ResponseInterface
    {
        if (!$this->hasValidKey()) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: invalid key');
        }

        $lines = (int) ($this->request->getGet('lines') ?? 200);
        if ($lines < 10) { $lines = 10; }
        if ($lines > 2000) { $lines = 2000; }

        $logDir = WRITEPATH . 'logs';
        if (!is_dir($logDir)) {
            return $this->response->setStatusCode(404)->setBody('Logs directory not found: ' . $logDir);
        }

        // Find latest log file matching pattern
        $latest = null;
        $latestMtime = 0;
        foreach (glob($logDir . DIRECTORY_SEPARATOR . 'log-*.php') as $file) {
            $mtime = @filemtime($file) ?: 0;
            if ($mtime > $latestMtime) {
                $latestMtime = $mtime;
                $latest = $file;
            }
        }

        if (!$latest) {
            return $this->response->setStatusCode(404)->setBody('No log files found in ' . $logDir);
        }

        // Tail last N lines efficiently
        $fp = @fopen($latest, 'rb');
        if (!$fp) {
            return $this->response->setStatusCode(500)->setBody('Unable to open log file');
        }

        $buffer = '';
        $pos = -1;
        $lineCount = 0;
        $stat = fstat($fp);
        $size = $stat['size'] ?? 0;
        while ($lineCount <= $lines && -$pos < $size) {
            fseek($fp, $pos, SEEK_END);
            $char = fgetc($fp);
            $buffer = $char . $buffer;
            if ($char === "\n") {
                $lineCount++;
            }
            $pos--;
        }
        fclose($fp);

        // Strip PHP opening guard to make it readable in browser
        $buffer = preg_replace('/^<\?php.*?exit;\s*\?>\s*/s', '', $buffer);

        return $this->response
            ->setHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->setBody("Latest log: " . basename($latest) . "\n\n" . $buffer);
    }

    /**
     * Basic health check: verifies rewrite reached CI, environment values,
     * writability of cache/logs/session, and optional DB connectivity.
     * GET /tools/health?key=TOKEN
     */
    public function health(): ResponseInterface
    {
        if (!$this->hasValidKey()) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: invalid key');
        }

        $checks = [];

        $checks['environment'] = defined('ENVIRONMENT') ? ENVIRONMENT : '(undefined)';
        $checks['ci_debug'] = defined('CI_DEBUG') ? (CI_DEBUG ? 'true' : 'false') : '(undefined)';

        $paths = [
            'cache'   => WRITEPATH . 'cache',
            'logs'    => WRITEPATH . 'logs',
            'session' => WRITEPATH . 'session',
            'uploads' => WRITEPATH . 'uploads',
        ];
        foreach ($paths as $key => $path) {
            $result = [
                'exists'    => is_dir($path),
                'writable'  => is_dir($path) ? is_writable($path) : false,
            ];
            // Try to write and delete a temp file
            if ($result['exists']) {
                $tmp = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '.health_' . uniqid('', true);
                $okWrite = @file_put_contents($tmp, 'ok');
                $result['writeTest'] = $okWrite !== false ? 'ok' : 'fail';
                if ($okWrite !== false) {
                    @unlink($tmp);
                }
            }
            $checks['writable_' . $key] = $result;
        }

        // Optional DB connection check
        try {
            $db = Database::connect();
            $db->initialize();
            $checks['database'] = [
                'connected' => $db->connID ? true : false,
                'driver'    => $db->DBDriver ?? null,
                'host'      => $db->hostname ?? null,
                'name'      => $db->database ?? null,
            ];
        } catch (\Throwable $e) {
            $checks['database'] = [
                'connected' => false,
                'error'     => $e->getMessage(),
            ];
        }

        return $this->response->setJSON($checks);
    }
}
