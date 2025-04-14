<?php

namespace app\controllers;

use app\core\Application;
use app\models\user\UserUpdateAvatar;
use app\models\user\UserUpdatePassword;
use app\models\user\UserUpdateProfile;

class UpdateProfileController extends Controller
{
    public function updateAvatar(): void
    {
        if (empty($_FILES['avatar']) || $_FILES['avatar']['size'] === 0) return;

        $userModel = new UserUpdateAvatar();
        $userModel->loadData($_FILES);
        $errors = $userModel->validate();

        try {
            if (!empty($errors)) throw new \Error($errors[0]);
            $userModel->updateAvatar();
            Application::$SESSION->setFlash('success', 'Update avatar successfully');
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
        $errors = $userModel->validate();

        if (isset($_FILES['avatar']) && $_FILES['avatar']['size'] !== 0) {
            $avatarModel = new UserUpdateAvatar();
            $avatarModel->loadData($_FILES);
            $userModel->avatarPriv = $avatarModel->avatar;
            $avatarErrors = $avatarModel->validate();
            $errors = array_merge($errors, $avatarErrors);
        }

        if (!empty($errors)) {
            Application::$SESSION->set('updateProfile', ['model' => $userModel, 'error' => $errors]);
            Application::$RESPONSE->redirect('/profile');
            return;
        }

        try {
            $userModel->updateProfile();
            Application::$SESSION->setFlash('success', 'Update profile successfully');
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
            Application::$SESSION->setFlash('success', 'Update password successfully');
        } catch (\Throwable $error) {
            Application::$SESSION->setFlash('error', $error->getMessage());
        } finally {
            Application::$RESPONSE->redirect('/profile');
        }
    }
}
