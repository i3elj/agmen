<?php

spl_autoload_register(
	fn($className) => require __DIR__ .
		"/" .
		str_replace("\\", "/", $className) .
		".php",
);

define("BASE_PATH", __DIR__ . "/");
define("WEB_DIR", "views/");
