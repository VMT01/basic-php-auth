<?php

namespace app\core\middleware;

use app\core\Application;
use app\core\exception\ForbiddenException;

class AuthMiddleware extends BaseMiddleware
{
    public function execute(): void
    {
        if (Application::$SESSION->isGuest()) throw new ForbiddenException();
    }
}
