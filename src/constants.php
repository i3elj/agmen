<?php

define("URL", parse_url($_SERVER["REQUEST_URI"]));

if (!defined("WEB_PATH")) {
	define("WEB_PATH", "src/views/");
}
if (!defined("COMPONENTS_PATH")) {
	define("COMPONENTS_PATH", "src/views/snippets/");
}
if (!defined("GLOBALS_PATH")) {
	define("GLOBALS_PATH", "src/views/globals/");
}
if (!defined("ICONS_PATH")) {
	define("ICONS_PATH", "public/svg/icons/");
}
if (!defined("SVG_PATH")) {
	define("SVG_PATH", "public/svg/");
}
if (!defined("ERROR_PAGES_PATH")) {
	define("ERROR_PAGES_PATH", "src/views/errors/");
}
if (!defined("UPLOAD_PATH")) {
	define("UPLOAD_PATH", "uploads/");
}
