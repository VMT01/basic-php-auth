<?php

namespace app\controllers;

use app\core\Application;
use app\core\View;
use app\models\user\UserRegisterModel;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->view = new View('register', 'auth');
    }

    public function register(): void
    {
        $this->render();
    }

    public function handleRegister(array $body): void
    {
        $userModel = new UserRegisterModel();
        $userModel->loadData($body);
        $errors = $userModel->validate();
        if (!empty($errors)) {
            $this->render(['model' => $userModel, 'errors' => ['form_error' => $errors]]);
            return;
        }

        try {
            $userModel->register();
            Application::$SESSION->setFlash('success', 'Đăng ký tài khoản thành công');
            Application::$RESPONSE->redirect('/login');
        } catch (\Throwable $error) {
            Application::$SESSION->setFlash('error', $error->getMessage());
            $this->render(['model' => $userModel]);
        }
    }
}
