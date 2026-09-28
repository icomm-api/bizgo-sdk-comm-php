<?php

declare(strict_types=1);

// Router for `php -S 127.0.0.1:<port>` used by CurlSenderTest. Local only, no network access.

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = (string) parse_url(is_string($uri) ? $uri : '/', PHP_URL_PATH);

switch ($path) {
    case '/echo':
    case '/echo-after-redirect':
        header('Content-Type: application/json');
        header('Retry-After: 7');
        $files = [];
        foreach ($_FILES as $name => $file) {
            if (is_array($file)) {
                $files[$name] = ['name' => $file['name'] ?? null, 'type' => $file['type'] ?? null, 'size' => $file['size'] ?? null];
            }
        }
        echo json_encode([
            'common' => ['authCode' => 'A000', 'authResult' => 'Success'],
            'data' => ['code' => 'A000', 'result' => 'Success', 'data' => [
                'path' => $path,
                'method' => $_SERVER['REQUEST_METHOD'] ?? '',
                'authorization' => $_SERVER['HTTP_AUTHORIZATION'] ?? '',
                'contentType' => $_SERVER['CONTENT_TYPE'] ?? '',
                'query' => $_SERVER['QUERY_STRING'] ?? '',
                'body' => $files === [] ? file_get_contents('php://input') : '',
                'post' => $_POST,
                'files' => $files,
            ]],
        ]);
        break;
    case '/redirect':
        header('Location: /echo-after-redirect', true, 302);
        break;
    case '/big':
        header('Content-Type: application/json');
        $chunk = str_repeat(' ', 1024 * 1024);
        for ($i = 0; $i < 17; $i++) {
            echo $chunk;
            flush();
        }
        break;
    case '/gzip':
        // 17 MiB of spaces, a few KiB on the wire: the cap must apply to the decompressed size
        header('Content-Type: application/json');
        header('Content-Encoding: gzip');
        echo gzencode(str_repeat(' ', 17 * 1024 * 1024), 9);
        break;
    case '/slow':
        sleep(3);
        echo '{}';
        break;
    default:
        http_response_code(404);
        echo 'not found';
}
