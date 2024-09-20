<?php declare(strict_types=1);

/**
 * Applies the BASE_PATH constant as a function
 */
function base_path(string $path): string
{
    return BASE_PATH . $path;
}

/**
 * Requires once the file and extract every associative array inside $ctx. If
 * the $path passed is a directory, requires everything recursively.
 */
function import(string $path, array $ctx = [], int $index = 0): void
{
    if ($index == 0) {
        $base = base_path($path);
    } else {
        $base = $path;
    }

    if (is_dir($base)) {
        foreach (new DirectoryIterator($base) as $file) {
            if ($file->isDot()) {
                continue;
            }
            $filename = $file->getRealPath();

            if (is_dir($filename)) {
                import($filename, index: 1);
            } else {
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
