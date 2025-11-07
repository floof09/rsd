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
     * Ensure an interviewer user exists (create or update password and profile).
     * GET /tools/seed-interviewer?key=TOKEN&email=...&password=...&first=...&last=...
     */
    public function seedInterviewer(): ResponseInterface
    {
        if (!$this->hasValidKey()) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: invalid key');
        }

        $email = trim((string) $this->request->getGet('email')) ?: 'interviewer@rsd.com';
        $password = (string) $this->request->getGet('password');
        $first = trim((string) $this->request->getGet('first')) ?: 'Interviewer';
        $last = trim((string) $this->request->getGet('last')) ?: 'User';

        if ($password === '') {
            return $this->response->setStatusCode(400)->setBody('Missing password');
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
                'user_type' => 'interviewer',
                'status' => 'active',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $builder->insert([
                'email' => $email,
                'password' => $hash,
                'first_name' => $first,
                'last_name' => $last,
                'user_type' => 'interviewer',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return $this->response->setStatusCode(200)->setBody('Interviewer ensured/updated for ' . $email);
    }

    /**
     * Seed initial companies with example form schemas so the dynamic form feature works immediately.
     * GET /tools/seed-companies?key=TOKEN
     */
    public function seedCompanies(): ResponseInterface
    {
        if (!$this->hasValidKey()) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: invalid key');
        }
        $db = Database::connect();
        // Ensure companies table exists before seeding
        if (!$db->tableExists('companies')) {
            return $this->response->setStatusCode(500)->setBody('companies table does not exist. Run /tools/migrate first.');
        }

        $builder = $db->table('companies');
        $seed = [
            [
                'name' => 'Everise',
                'status' => 'active',
                'form_schema' => json_encode([
                    'fields' => [
                        [ 'key' => 'position_applied', 'label' => 'Position Applied For', 'type' => 'text', 'required' => true, 'maxLength' => 150 ],
                        [ 'key' => 'shift_preference', 'label' => 'Shift Preference', 'type' => 'select', 'options' => ['Day','Night','Graveyard'], 'required' => true ],
                        [ 'key' => 'expected_salary', 'label' => 'Expected Salary (PHP)', 'type' => 'number', 'min' => 10000, 'max' => 150000 ],
                        [ 'key' => 'has_equipment', 'label' => 'Has Own Equipment', 'type' => 'checkbox', 'required' => false ],
                    ]
                ])
            ],
            [
                'name' => 'IGT',
                'status' => 'active',
                'form_schema' => json_encode([
                    'fields' => [
                        [ 'key' => 'program', 'label' => 'Program', 'type' => 'text', 'required' => true, 'maxLength' => 120 ],
                        [ 'key' => 'igt_tag', 'label' => 'IGT Tag Result', 'type' => 'select', 'options' => ['Passed','Failed'], 'required' => true ],
                        [ 'key' => 'availability_date', 'label' => 'Availability Date', 'type' => 'date' ],
                        [ 'key' => 'english_level', 'label' => 'English Communication Level', 'type' => 'select', 'options' => ['Poor','Fair','Good','Excellent'], 'required' => true ],
                    ]
                ])
            ],
        ];

        $created = 0; $updated = 0; $errors = [];
        foreach ($seed as $row) {
            $existing = $builder->select('id')->where('name', $row['name'])->get()->getFirstRow();
            try {
                if ($existing) {
                    $builder->where('id', $existing->id)->update([
                        'status' => $row['status'],
                        'form_schema' => $row['form_schema'],
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $updated++;
                } else {
                    $builder->insert([
                        'name' => $row['name'],
                        'status' => $row['status'],
                        'form_schema' => $row['form_schema'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $created++;
                }
            } catch (\Throwable $e) {
                $errors[] = $row['name'] . ': ' . $e->getMessage();
            }
        }

        $msg = 'Companies seeding complete. Created: ' . $created . ', Updated: ' . $updated;
        if ($errors) { $msg .= '\nErrors: ' . implode('; ', $errors); }
        return $this->response->setStatusCode(200)->setBody($msg);
    }

    /**
     * Backfill existing applications.company_id based on applications.company_name matching companies.name.
     * GET /tools/backfill-company-ids?key=TOKEN
     */
    public function backfillCompanyIds(): ResponseInterface
    {
        if (!$this->hasValidKey()) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: invalid key');
        }
        $db = Database::connect();
        if (!$db->tableExists('applications')) {
            return $this->response->setStatusCode(500)->setBody('applications table missing');
        }
        if (!$db->tableExists('companies')) {
            return $this->response->setStatusCode(500)->setBody('companies table missing');
        }

        $companyRows = $db->table('companies')->select('id,name')->get()->getResultArray();
        $map = [];
        foreach ($companyRows as $c) { $map[strtolower($c['name'])] = (int)$c['id']; }

        $apps = $db->table('applications')->select('id,company_name,company_id')->where('company_id IS NULL')->get()->getResultArray();
        $updated = 0; $skipped = 0; $unknown = [];
        foreach ($apps as $a) {
            $name = trim((string)$a['company_name']);
            if ($name === '') { $skipped++; continue; }
            $key = strtolower($name);
            if (!isset($map[$key])) { $unknown[] = $name; continue; }
            try {
                $db->table('applications')->where('id', $a['id'])->update([ 'company_id' => $map[$key] ]);
                $updated++;
            } catch (\Throwable $e) {
                $unknown[] = $name . ' (error: ' . $e->getMessage() . ')';
            }
        }
        $msg = 'Backfill complete. Updated: ' . $updated . ', Skipped blank: ' . $skipped;
        if ($unknown) { $msg .= '\nUnmatched company names: ' . implode(', ', array_slice(array_unique($unknown), 0, 25)); }
        return $this->response->setStatusCode(200)->setBody($msg);
    }

    /**
     * Repair migrations state when some tables already exist but migrations table isn't in sync.
     * - Ensures migrations table exists
     * - Marks known migrations as applied if their tables exist
     * - Runs remaining migrations
     * GET /tools/repair-migrations?key=TOKEN
     */
    public function repairMigrations(): ResponseInterface
    {
        if (!$this->hasValidKey()) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: invalid key');
        }

        $db = Database::connect();
        $forge = \Config\Services::forge();
        $out = [];

        // Ensure migrations table exists (CI normally auto-creates, but do it defensively)
        if (!$db->tableExists('migrations')) {
            $sql = "CREATE TABLE migrations (\n                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,\n                version VARCHAR(255) NOT NULL,\n                class VARCHAR(255) NOT NULL,\n                `group` VARCHAR(255) NOT NULL,\n                namespace VARCHAR(255) NOT NULL,\n                time INT NOT NULL,\n                batch INT UNSIGNED NOT NULL,\n                PRIMARY KEY (id)\n            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $db->query($sql);
            $out[] = 'Created migrations table';
        }

        // Helper to insert a migration row if missing
        $ensureMigration = function (string $version, string $class) use ($db, &$out) {
            $exists = $db->table('migrations')->where('version', $version)->countAllResults();
            if ($exists == 0) {
                $db->table('migrations')->insert([
                    'version'   => $version,
                    'class'     => $class,
                    'group'     => 'default',
                    'namespace' => 'App',
                    'time'      => time(),
                    'batch'     => 1,
                ]);
                $out[] = "Marked migration as applied: $class ($version)";
            }
        };

        // If core tables already exist, mark their migrations as applied
        if ($db->tableExists('users')) {
            $ensureMigration('2025-10-28-073954', 'App\\Database\\Migrations\\CreateUsersTable');
        }
        if ($db->tableExists('applications')) {
            $ensureMigration('2025-10-28-093216', 'App\\Database\\Migrations\\CreateApplicationsTable');
            // If resume column is present (or even if not), mark the AddResume migration as applied to avoid duplicate attempts if already handled manually
            $ensureMigration('2025-10-28-104042', 'App\\Database\\Migrations\\AddResumeToApplications');
        }

        // Now attempt to run remaining migrations (this should create system_logs, etc.)
        $migrate = Services::migrations();
        try {
            $migrate->latest();
            $out[] = 'Ran remaining migrations successfully.';
        } catch (\Throwable $e) {
            $out[] = 'Migration run failed: ' . $e->getMessage();
            return $this->response->setStatusCode(500)->setBody(implode("\n", $out));
        }

        return $this->response->setStatusCode(200)->setBody(implode("\n", $out));
    }

    /**
     * Verify a user's stored hash against a provided password and show minimal user info.
     * GET /tools/auth-check?email=...&password=...
     */
    public function authCheck(): ResponseInterface
    {
        if (!$this->hasValidKey()) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: invalid key');
        }

        $email = trim((string) $this->request->getGet('email'));
        $password = (string) $this->request->getGet('password');
        if ($email === '' || $password === '') {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Missing email or password']);
        }

        $db = Database::connect();
        $row = $db->table('users')->where('email', $email)->get()->getRowArray();
        if (!$row) {
            return $this->response->setJSON([
                'found' => false,
                'email' => $email,
            ]);
        }

        $hash = (string) ($row['password'] ?? '');
        $verified = $hash !== '' ? password_verify($password, $hash) : false;

        // Return minimal info; do not expose full hash in production
        $info = [
            'found' => true,
            'id' => $row['id'] ?? null,
            'email' => $row['email'] ?? null,
            'user_type' => $row['user_type'] ?? null,
            'status' => $row['status'] ?? null,
            'hash_prefix' => substr($hash, 0, 12),
            'hash_len' => strlen($hash),
            'verified' => $verified,
        ];

        return $this->response->setJSON($info);
    }

    /**
     * Show basic DB status: tables present and simple counts.
     * GET /tools/db-status
     */
    public function dbStatus(): ResponseInterface
    {
        if (!$this->hasValidKey()) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: invalid key');
        }

        $db = Database::connect();
        $tables = $db->listTables();
        $hasUsers = in_array('users', $tables, true);
        $hasApps = in_array('applications', $tables, true);
        $hasLogs = in_array('system_logs', $tables, true);

        $counts = [];
        if ($hasUsers) {
            $counts['users'] = (int) $db->table('users')->countAllResults();
        }
        if ($hasLogs) {
            $counts['system_logs'] = (int) $db->table('system_logs')->countAllResults();
        }
        if ($hasApps) {
            $counts['applications'] = (int) $db->table('applications')->countAllResults();
        }

        return $this->response->setJSON([
            'database' => [
                'name' => $db->database ?? null,
                'driver' => $db->DBDriver ?? null,
                'host' => $db->hostname ?? null,
            ],
            'tables' => $tables,
            'has' => [
                'users' => $hasUsers,
                'applications' => $hasApps,
                'system_logs' => $hasLogs,
            ],
            'counts' => $counts,
        ]);
    }

    /**
     * Inspect current session basics (guarded). Useful to debug login persistence.
     * GET /tools/session-info?key=TOKEN
     */
    public function sessionInfo(): ResponseInterface
    {
        if (!$this->hasValidKey()) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: invalid key');
        }

        $sess = session();
        $data = [
            'session_id'   => method_exists($sess, 'getId') ? $sess->getId() : null,
            'isLoggedIn'   => $sess->get('isLoggedIn'),
            'user_id'      => $sess->get('user_id'),
            'email'        => $sess->get('email'),
            'user_type'    => $sess->get('user_type'),
            'justLoggedIn' => $sess->get('justLoggedIn'),
            'cookies'      => $_COOKIE ?? [],
            'headers'      => [
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                'host'       => $_SERVER['HTTP_HOST'] ?? null,
                'https'      => $_SERVER['HTTPS'] ?? null,
                'x_forwarded_proto' => $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? null,
            ],
        ];

        return $this->response->setJSON($data);
    }

    /**
     * List session files in the writable session directory.
     * GET /tools/session-files?key=TOKEN
     */
    public function sessionFiles(): ResponseInterface
    {
        if (!$this->hasValidKey()) {
            return $this->response->setStatusCode(403)->setBody('Forbidden: invalid key');
        }

        $dir = rtrim(WRITEPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'session';
        if (!is_dir($dir)) {
            return $this->response->setStatusCode(404)->setBody('Session dir not found: ' . $dir);
        }

        $files = [];
        $entries = glob($dir . DIRECTORY_SEPARATOR . '*') ?: [];
        foreach ($entries as $f) {
            if (!is_file($f)) continue;
            $stat = stat($f);
            $files[] = [
                'name' => basename($f),
                'size' => $stat['size'] ?? null,
                'mtime' => date('c', $stat['mtime'] ?? 0),
            ];
        }

        // Sort by mtime desc
        usort($files, function ($a, $b) {
            return strcmp($b['mtime'], $a['mtime']);
        });

        return $this->response->setJSON([
            'session_dir' => $dir,
            'count' => count($files),
            'files' => $files,
        ]);
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

        // Find latest log file (support both .php and .log extensions)
        $latest = null;
        $latestMtime = 0;
        $candidates = array_merge(
            glob($logDir . DIRECTORY_SEPARATOR . 'log-*.php') ?: [],
            glob($logDir . DIRECTORY_SEPARATOR . 'log-*.log') ?: []
        );
        foreach ($candidates as $file) {
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

        // If the log file is PHP-protected, strip the guard for readability
        if (substr($latest, -4) === '.php') {
            $buffer = preg_replace('/^<\?php.*?exit;\s*\?>\s*/s', '', $buffer);
        }

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
