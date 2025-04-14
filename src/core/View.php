<?php

namespace app\core;

class View
{
    private string $view;
    private ?string $layout;

    public function __construct(string $view, ?string $layout = null)
    {
        $this->view = $view;
        $this->layout = $layout;
    }

    public function render(array $params = []): void
    {
        $view = $this->renderView($params);

        if (isset($this->layout)) {
            $layout = $this->renderLayout();
            echo str_replace("{{content}}", $view, $layout);
        } else {
            echo $view;
        }
    }

    private function renderLayout(): string
    {
        ob_start();
        require_once Application::$ROOT_PATH . "/views/$this->layout/index.php";
        return ob_get_clean();
    }

    /**
     * Renders `view` by provided `$view`.
     *
     * This will throw error if `$view` not found
     */
    private function renderView(array $params): string
    {
        foreach ($params as $key => $value) {
            $$key = $value;
        }

        ob_start();
        require_once Application::$ROOT_PATH . "/views/" . ($this->layout ?? '') . "/$this->view.php";
        return ob_get_clean();
    }
}
