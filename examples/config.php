<?php

spl_autoload_register(
	fn($className) => require __DIR__ .
		"/" .
		str_replace("\\", "/", $className) .
		".php",
);

define("BASE_PATH", __DIR__ . "/");
define("WEB_DIR", "views/");
define("ERROR_PAGES_DIR", "views/errors/");
define("GLOBALS_DIR", "views/globals/");
