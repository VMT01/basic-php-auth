<?php

namespace app\controllers;

use app\core\Application;
use app\core\View;

class LogoutController extends Controller
{
    public function __construct()
    {
        $this->view = new View("login", "auth");
    }

    public function logout(): void
    {
        Application::$SESSION->logout();
        Application::$RESPONSE->redirect('/login');
    }
}
