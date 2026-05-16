<?php
define('APP_ENV', 'dev');
define('BASE_PATH', dirname(__DIR__) . '/');
define('ENV', [
	'DB'      => 'mysql',
	'DB_HOST' => 'localhost',
	'DB_PORT' => '8080',
	'DB_USER' => 'john.doe',
	'DB_PWD'  => '123',
	'DB_NAME' => 'database',
]);

require 'bootstrap.php';
require '../init.php';
require '../src/global_functions.php';

use Agmen\Database;

$db = Database::connect();
[$rows, $count] = $db->sqlr("SELECT * FROM test");
Agmen\dd($rows);
