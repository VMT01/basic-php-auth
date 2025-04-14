<?php

namespace app\controllers;

use app\core\Application;
use app\core\View;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->view = new View("profile");
    }

    public function profile(): void
    {
        $this->render();
    }

    public function logout(): void
    {
        Application::$SESSION->logout();
        Application::$RESPONSE->redirect('/login');
    }
}
