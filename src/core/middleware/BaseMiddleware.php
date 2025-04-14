<?php

namespace app\core\middleware;

abstract class BaseMiddleware
{
    protected static self $instance;

    public static function getInstance(): self
    {
        if (!isset(self::$instance)) {
            static::$instance = new static();
        }
        return self::$instance;
    }

    abstract public function execute(): void;
}
