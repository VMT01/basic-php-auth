<?php

namespace app\core;

use app\entities\User;

class Session
{
    private const FLASH_KEY = 'flash_messages';

    public ?User $user = null;

    public function __construct()
    {
        session_start();

        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage) {
            $flashMessage['removed'] = true;
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    /** @param 'success'|'error' $key */
    public function setFlash(string $key, string $message): void
    {
        $_SESSION[self::FLASH_KEY][$key] = ['removed' => false, 'value' => $message];
    }

    /** @param 'success'|'error' $key */
    public function getFlash(string $key): mixed
    {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? null;
    }

    /**
     * This function will set or remove session value;
     * 
     * @param string $key
     * @param mixed $value null will remove, else will set
     */
    public function set(string $key, mixed $value): void
    {
        if ($value === null) unset($_SESSION[$key]);
        else $_SESSION[$key] = $value;
    }

    public function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }

    public function login(User $user): void
    {
        $this->user = $user;
        $this->set('user', $user->id);
    }

    public function logout(): void
    {
        $this->user = null;
        $this->set('user', null);
    }

    public function isGuest(): bool
    {
        return !$this->user;
    }

    public function __destruct()
    {
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage) {
            if ($flashMessage['removed']) {
                unset($flashMessages[$key]);
            }
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
}
