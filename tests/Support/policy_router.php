<?php

declare(strict_types=1);

// Router for `php -S 127.0.0.1:<port>` used by HttpClientPolicyTest. Local only, no network access.
// The first path segment selects the behaviour; every request is appended to the log file given in BIZGO_POLICY_LOG.

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = (string) parse_url(is_string($uri) ? $uri : '/', PHP_URL_PATH);
$mode = explode('/', trim($path, '/'))[0] ?? '';
$log = getenv('BIZGO_POLICY_LOG');
$auth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (is_string($log) && $log !== '') {
    $entry = ['path' => $path, 'method' => $_SERVER['REQUEST_METHOD'] ?? '', 'auth' => is_string($auth) ? $auth : ''];
    file_put_contents($log, json_encode($entry) . "\n", FILE_APPEND);
}
$ok = '{"common":{"authCode":"A000","authResult":"Success"},"data":{"code":"A000","result":"Success","data":'
    . '{"destinations":[{"to":"01000000000","msgKey":"K001","code":"A000","result":"Success"}],"report":[]}}}';
header('Content-Type: application/json');
switch ($mode) {
    case 'redirect':
        header('Location: /stolen/elsewhere', true, 307);
        echo '{}';
        break;
    case 'basic':
        if (is_string($auth) && (str_starts_with($auth, 'Basic ') || str_starts_with($auth, 'Digest '))) {
            echo $ok;
            break;
        }
        http_response_code(401);
        header('WWW-Authenticate: Basic realm="x"');
        echo '{"common":{"authCode":"A401","authResult":"Unauthorized"}}';
        break;
    case 'unavailable':
        http_response_code(503);
        echo '{}';
        break;
    case 'gzip':
        header('Content-Encoding: gzip');
        echo gzencode(str_repeat(' ', 17 * 1024 * 1024), 9);
        break;
    default:
        echo $ok;
}
