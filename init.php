<?php

declare(strict_types=1);

$errors = false;

if (!defined("BASE_PATH")) {
	error_log(
		"FATAL LIB ERROR: Agmen needs the BASE_PATH variable to be define, it tells where the project base path is.",
		4
	);
	$errors = true;
}

if (!defined("DEV_MODE")) {
	error_log(
		"FATAL LIB ERROR: Agmen needs to know if it is on production or development, define a DEV_MODE boolean variable.",
		4
	);
	$errors = true;
}

if (!defined('ENV')) {
	error_log(<<<EOD
		FATAL LIB ERROR: A global array variable ENV must be defined containing the following keys (I suggest parsing a .env file using `parse_ini_file` instead of hard coding the credentials to the project):

			  DB      - the driver name
		DB_HOST - host (e.g. localhost or some IP)
		DB_PORT - port server is listening to
		DB_USER - user that has access to the database
		DB_PWD  - password
		DB_NAME - database name
		EOD, 4);
	$errors = true;
}

if ($errors) exit(1);
