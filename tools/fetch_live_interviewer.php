<?php
// Tiny CLI script to login to live site and fetch the interviewer application page HTML
// Usage: php tools/fetch_live_interviewer.php <email> <password>

if (php_sapi_name() !== 'cli') {
    fwrite(STDERR, "Run from CLI.\n");
    exit(1);
}

[$script, $email, $password] = array_pad($argv, 3, null);
if (!$email || !$password) {
    fwrite(STDERR, "Usage: php tools/fetch_live_interviewer.php <email> <password>\n");
    exit(2);
}

$base = 'https://rsdlearninghub.rsdhrmc.com';
$loginUrl = $base . '/auth/login';
$postUrl  = $base . '/auth/doLogin';
$targetUrl = $base . '/interviewer/application';

$cookieFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rsd_cookies_' . getmypid() . '.txt';

function curl_get($url, $cookieFile)
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_COOKIEJAR => $cookieFile,
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_USERAGENT => 'RSD-Fetch/1.0 (+https://github.com)'
    ]);
    $body = curl_exec($ch);
    if ($body === false) {
        $err = curl_error($ch);
        curl_close($ch);
        throw new RuntimeException('GET failed: ' . $err);
    }
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [$status, $body];
}

function curl_post($url, $cookieFile, array $fields)
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_COOKIEJAR => $cookieFile,
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($fields),
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_USERAGENT => 'RSD-Fetch/1.0 (+https://github.com)'
    ]);
    $body = curl_exec($ch);
    if ($body === false) {
        $err = curl_error($ch);
        curl_close($ch);
        throw new RuntimeException('POST failed: ' . $err);
    }
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [$status, $body];
}

try {
    // Step 1: GET login page (capture CSRF token if present)
    [, $loginHtml] = curl_get($loginUrl, $cookieFile);
    $csrfName = 'csrf_test_name'; // from Config\Security
    $csrfValue = null;
    if (preg_match('/name=\"' . preg_quote($csrfName, '/') . '\"[^>]*value=\"([^\"]+)\"/i', $loginHtml, $m)) {
        $csrfValue = $m[1];
    }

    // Step 2: POST login
    $fields = [
        'email' => $email,
        'password' => $password,
        'remember' => 'on',
    ];
    if ($csrfValue) { $fields[$csrfName] = $csrfValue; }

    [$st, $postResp] = curl_post($postUrl, $cookieFile, $fields);

    // Basic check: after login, interviewer dashboard link should be visible or session set
    // Proceed to target regardless; server will redirect if not logged in
    [$st2, $appHtml] = curl_get($targetUrl, $cookieFile);

    // Save HTML to project build/logs
    $outDir = __DIR__ . '/../build/logs';
    if (!is_dir($outDir)) { @mkdir($outDir, 0775, true); }
    $outFile = $outDir . '/live_interviewer_application.html';
    file_put_contents($outFile, $appHtml);

    // Extract title for quick sanity
    $title = null;
    if (preg_match('/<title>(.*?)<\/title>/is', $appHtml, $m)) { $title = trim($m[1]); }

    echo "Saved: " . realpath($outFile) . PHP_EOL;
    if ($title) echo "Title: $title" . PHP_EOL;
    echo "HTTP: $st2" . PHP_EOL;
} catch (Throwable $e) {
    fwrite(STDERR, 'Error: ' . $e->getMessage() . PHP_EOL);
    exit(3);
} finally {
    @unlink($cookieFile);
}
