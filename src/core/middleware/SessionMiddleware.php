<?php

namespace app\core\middleware;

use app\core\Application;

class SessionMiddleware extends BaseMiddleware
{
    public function execute(): void
    {
        if (!Application::$SESSION->isGuest()) Application::$RESPONSE->redirect('/profile');
    }
}
