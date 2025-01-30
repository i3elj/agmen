<?php declare(strict_types=1);

if (array_key_exists('REQUEST_URI', $_SERVER)) {
    define("URL", parse_url($_SERVER['REQUEST_URI']));
}

require __DIR__ . '/../../config.php';
require_once __DIR__ . "/src/global_functions.php";

import(__DIR__ . '/src', index: 1);
