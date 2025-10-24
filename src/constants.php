<?php

const __agmen_constants = [
	"WEB_DIR" => "src/views/",
	"COMPONENTS_DIR" => "src/views/snippets/",
	"GLOBALS_DIR" => "src/views/globals/",
	"ICONS_DIR" => "public/svg/icons/",
	"SVG_DIR" => "public/svg/",
	"ERROR_PAGES_DIR" => "src/views/errors/",
];

foreach (__agmen_constants as $const => $value) {
	if (!defined($const)) {
		define($const, $value);
	}
}
