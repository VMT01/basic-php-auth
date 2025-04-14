<?php

namespace app\core;

use app\core\exception\NotFoundException;

class Router
{
    private array $routes = [];

    /** 
     * @param 'get'|'post' $method 
     * @param object{class: class,action: string,middlewares?:array<int,class>} $callback
     */
    public function assign(string $method, string $path, object $callback): void
    {
        $this->routes[$method][$path] = $callback;
    }

    public function resolve(): void
    {
        $path = Application::$REQUEST->getPath();
        $method = Application::$REQUEST->getMethod();
        $body = Application::$REQUEST->getBody();

        /** @var object{class: class,action: string,middlewares?:array<int,class>} $callback*/
        $callback = $this->routes[$method][$path] ?? null;
        if (!isset($callback)) throw new NotFoundException();

        $class = $callback->class;
        $action = $callback->action;
        foreach ($callback->middlewares ?? [] as $middleware) {
            $middleware::getInstance()->execute();
        }
        call_user_func_array([$class::getInstance(), $action], [$body]);
    }
}
