<?php

$request_uri = $_SERVER["REQUEST_URI"];
$base_path = parse_url($request_uri, PHP_URL_PATH);

require_once __DIR__ . "/constants/routing.php";
require_once __DIR__ . "/constants/session.php";
require_once __DIR__ . "/shared/routing.php";

switch ($base_path) {
    case INDEX:
        session_start();
        if (isset($_SESSION[USER])) {
            redirect(PROFILE);
        } else {
            redirect(LOGIN);
        }
        break;
    case LOGIN:
        require_once "views/login.php";
        break;
    case REGISTER:
        require_once "views/register.php";
        break;
    case PROFILE:
        require_once "views/profile.php";
        break;
    default:
        http_response_code(404);
        break;
}
