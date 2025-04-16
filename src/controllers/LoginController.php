<?php

namespace app\controllers;

use app\core\Application;
use app\core\View;
use app\models\user\UserLoginModel;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->view = new View("login", "auth");
    }

    public function login(): void
    {
        $this->render();
    }

    public function handleLogin(array $body): void
    {
        $loginModel = new UserLoginModel();
        $loginModel->loadData($body);
        $errors = $loginModel->validate();
        if (!empty($errors)) {
            $this->render(['model' => $loginModel, 'errors' => ['form_error' => $errors]]);
            return;
        }

        try {
            $userId = $loginModel->login();

            Application::$SESSION->set('user', $userId);
            Application::$RESPONSE->redirect('/profile');
        } catch (\Throwable $error) {
            Application::$SESSION->setFlash('error', $error->getMessage());
            $this->render(['model' => $loginModel]);
        }
    }
}
