<?php

/**
 * Return server absolute URL path by provided route
 */
function absoluteUrl(string $route): string
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domain = $protocol . $_SERVER['HTTP_HOST'];

    return $domain . '/' . ltrim($route, '/');
}

function redirect(string $route): void
{
    $url = absoluteUrl($route);
    header("Location: " . $url);
    exit;
}
