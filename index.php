<?php

require_once "./constants/routing.php";
require_once "./shared/routing.php";

if (!isset($_SESSION['user'])) {
    redirect(LOGIN);
} else {
    redirect(PROFILE);
}
