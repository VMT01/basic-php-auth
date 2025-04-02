<?php

class App
{
    private string $base_path;
    private string $pages_dir;

    /**
     * Initialize the router
     *
     * @param string $basePath The base file system path of the application
     * @param string $pagesDir The directory containing page files
     */
    public function __construct(string $base_path = "", string $pages_dir = "/pages")
    {
        $this->base_path = $base_path;
        $this->pages_dir = $pages_dir;
    }

    /**
     * Handle the current requestby mapping it to the corresponding file
     */
    public function start(): void
    {
        $request_uri = $_SERVER["REQUEST_URI"];
        $path = trim(parse_url($request_uri, PHP_URL_PATH), "/");

        echo $path;
    }
}
