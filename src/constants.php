<?php

define('URL', parse_url($_SERVER["REQUEST_URI"]));

if (!defined("WEB_DIR")) {
	define("WEB_DIR", "src/views/");
}
if (!defined("COMPONENTS_DIR_NAME")) {
	define("COMPONENTS_DIR_NAME", "src/views/snippets/");
}
if (!defined("GLOBALS_DIR")) {
	define("GLOBALS_DIR", "src/views/globals/");
}
if (!defined("ICONS_DIR")) {
	define("ICONS_DIR", "public/svg/icons/");
}
if (!defined("SVG_DIR")) {
	define("SVG_DIR", "public/svg/");
}
if (!defined("ERROR_PAGES_DIR")) {
	define("ERROR_PAGES_DIR", "src/views/errors/");
}
