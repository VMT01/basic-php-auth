<?php

$request_uri = $_SERVER["REQUEST_URI"];
if (trim($request_uri, "/") === "") {
    require_once __DIR__ . "/pages/index.php";
    exit;
}

$path = __DIR__ . "/pages" . parse_url($request_uri, PHP_URL_PATH) . ".php";
if (file_exists($path)) {
    require_once $path;
    exit;
}

http_response_code(404);
echo "404 Not Found";
