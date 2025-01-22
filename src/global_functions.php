<?php declare(strict_types=1);

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

/**
 * Dies and dump.
 */
function dd(...$args)
{
    echo "<pre>";
    var_dump(...$args);
    echo "</pre>";
    die();
}

/**
 * Just dump, no killing here
 */
function just_dump(...$args)
{
    echo "<pre>";
    var_dump(...$args);
    echo "</pre>";
}
