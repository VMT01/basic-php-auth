<?php

namespace app\controllers;

use app\core\View;

class ErrorController extends Controller
{
    public function __construct()
    {
        $this->view = new View("_error");
    }

    public function _error(\Exception $e): void
    {
        $this->render(['exception' => $e]);
    }
}
