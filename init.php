<?php declare(strict_types=1);

if (!defined("BASE_PATH")) {
	throw new Exception(
		"Tusk needs the BASE_PATH variable to be define, it tells where the project base path is",
	);
}

$configFile = BASE_PATH . "config.php";
if (file_exists($configFile)) {
	include $configFile;
}

if (!defined("WEB_DIR")) {
	define("WEB_DIR", "src/views/");
}
if (!defined("COMPONENTS_DIR_NAME")) {
	define("COMPONENTS_DIR_NAME", "snippets/");
}
if (!defined("GLOBALS_DIR")) {
	define("GLOBALS_DIR", "src/globals/");
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
