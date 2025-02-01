<?php declare(strict_types=1);

if (array_key_exists('REQUEST_URI', $_SERVER)) {
    define("URL", parse_url($_SERVER['REQUEST_URI']));
}

include __DIR__ . '/../../../config.php';
require_once __DIR__ . "/src/global_functions.php";

if (!isset(WEB_DIR)) define("WEB_DIR", "src/www");
if (!isset(COMPONENTS_DIR_NAME)) define("COMPONENTS_DIR_NAME", "partials");
if (!isset(GLOBALS_DIR)) define("GLOBALS_DIR", "src/globals");
if (!isset(ICONS_DIR)) define("ICONS_DIR", "public/svg/icons");
if (!isset(SVG_DIR)) define("SVG_DIR", "public/svg");
if (!isset(ERROR_PAGES_DIR)) define("ERROR_PAGES_DIR", "src/www/errors");

import(__DIR__ . '/src', index: 1);
