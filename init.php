<?php declare(strict_types=1);

if (array_key_exists('REQUEST_URI', $_SERVER)) {
    define("URL", parse_url($_SERVER['REQUEST_URI']));
}

/**
 * Applies the a base path for the rest of the library
 */
function base_path(string $path): string
{
    return \BASE_PATH . $path;
}

/**
 * Requires once the file and extract every associative array inside $ctx. If
 * the $path passed is a directory, requires everything recursively.
 */
function import(string $path, array $ctx = [], int $index = 0): void
{
    $base = $index == 0 ? base_path($path) : $path;

    if (is_dir($base)) {
        foreach (new DirectoryIterator($base) as $file) {
            $filename = $file->getRealPath();

            if ($file->isDot()) {
                continue;
            }

            if (is_dir($filename)) {
                import($filename, index: 1);
                continue;
            } 

            if (pathinfo($filename, PATHINFO_EXTENSION) == "php") {
                extract($ctx);
                require_once $filename;
            }
        }
        return;
    }

    extract($ctx);
    require_once $base;
    return;
}

$vars = require __DIR__ . '/../../config.php';

foreach ($vars as $key => $value)
    define("$key", $value);

import(__DIR__ . '/src', index: 1);
