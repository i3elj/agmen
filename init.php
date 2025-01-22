<?php declare(strict_types=1);

if (array_key_exists('REQUEST_URI', $_SERVER)) {
    define("URL", parse_url($_SERVER['REQUEST_URI']));
}

$vars = require __DIR__ . '/../../config.php';

foreach ($vars as $key => $value)
    define("$key", $value);

require_once "./src/global_functions.php";

import(__DIR__ . '/src', index: 1);
