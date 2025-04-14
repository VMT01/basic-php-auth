<?php

namespace app\core;

class Request
{
    public function __construct() {}

    public function getPath(): string
    {
        $request_uri = $_SERVER["REQUEST_URI"];
        $base_path = parse_url($request_uri, PHP_URL_PATH);

        return $base_path;
    }

    public function getMethod(): string
    {
        $method = strtolower($_SERVER["REQUEST_METHOD"]);

        return $method;
    }

    public function getBody(): array
    {
        $body = [];

        switch ($this->getMethod()) {
            case "get":
                foreach ($_GET as $key => $value) {
                    $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                }
                break;
            case "post":
                foreach ($_POST as $key => $value) {
                    $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                }
                break;
        }

        return $body;
    }
}
