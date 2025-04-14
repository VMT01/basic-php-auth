<?php

namespace app\controllers;

use app\core\View;

abstract class Controller
{
    private static self $instance;

    protected ?View $view = null;

    protected function render(array $params = []): void
    {
        $this->view->render($params);
    }

    public static function getInstance(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }
}
