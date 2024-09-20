<?php declare(strict_types=1);

namespace http\header;

function redirect(string $path, int $exit_status = 0): void
{
    header('Location: ' . $path);
    exit($exit_status);
}
