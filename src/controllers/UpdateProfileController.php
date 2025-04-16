<?php

namespace app\controllers;

use app\core\Application;
use app\models\user\UserUpdateAvatar;
use app\models\user\UserUpdatePassword;
use app\models\user\UserUpdateProfile;

class UpdateProfileController extends Controller
{
    public function updateAvatar()
    {
        if (empty($_FILES['avatar']) || $_FILES['avatar']['error'] !== 0) return;

        $userModel = new UserUpdateAvatar();
        $userModel->loadData($_FILES);
        $errors = $userModel->validate();

        if (!empty($errors)) {
            Application::$SESSION->setFlash('error', $errors['avatar']);
            Application::$RESPONSE->redirect('/profile');
            return;
        }

        try {
            $userModel->updateAvatar();
            Application::$SESSION->setFlash('success', 'Cập nhật ảnh đại diện thành công');
        } catch (\Throwable $error) {
            Application::$SESSION->setFlash('error', $error->getMessage());
        } finally {
            Application::$RESPONSE->redirect('/profile');
        }
    }

    public function updateProfile(array $body): void
    {
        $userModel = new UserUpdateProfile();
        $userModel->loadData($body);
        $userModel->avatar = $_FILES['avatar']['size'] !== 0 ? $_FILES['avatar'] : null;
        $errors = $userModel->validate();

        if (!empty($errors)) {
            Application::$SESSION->set('updateProfile', ['model' => $userModel, 'error' => $errors]);
            Application::$RESPONSE->redirect('/profile');
            return;
        }

        try {
            $userModel->updateProfile();
            Application::$SESSION->setFlash('success', 'Cập nhật thông tin cá nhân thành công');
        } catch (\Throwable $error) {
            Application::$SESSION->setFlash('error', $error->getMessage());
        } finally {
            Application::$RESPONSE->redirect('/profile');
        }
    }

    public function updatePassword(array $body): void
    {
        $user = Application::$SESSION->user;

        $userModel = new UserUpdatePassword();
        $userModel->loadData($body);
        $errors = $userModel->validate();

        if (!empty($errors)) {
            Application::$SESSION->set('updatePassword', ['model' => $userModel, 'error' => $errors]);
            Application::$RESPONSE->redirect('/profile');
            return;
        }

        try {
            if (!password_verify($userModel->current_password, $user->password)) throw new \Error('Password is incorrect');
            if (password_verify($userModel->new_password, $user->password)) throw new \Error('Password cannot be the same as old one');

            $userModel->updatePassword();
            Application::$SESSION->setFlash('success', 'Cập nhật mật khẩu thành công');
        } catch (\Throwable $error) {
            Application::$SESSION->setFlash('error', $error->getMessage());
        } finally {
            Application::$RESPONSE->redirect('/profile');
        }
    }
}
