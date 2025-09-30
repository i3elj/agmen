<?php declare(strict_types=1);

use middlewares\NoOneCanEnter;
use middlewares\SomeMiddleware;
use pages\RouterClassHandler;
use Agmen\Router;

require "config.php";
require "../vendor/autoload.php";

const r = new Router();
r->group('/', [SomeMiddleware::class], function (Router $r) {
	$r->path("", "home", [], RouterClassHandler::class);
	$r->path('/home/with/queries', 'queries', [], RouterClassHandler::class, 'queries');
	$r->path("/home/with/:foo(word)", 'parameters', [], RouterClassHandler::class, 'parameters');
});
r->path('/secure', 'secure', [NoOneCanEnter::class], RouterClassHandler::class, 'notAllowed');
r->handle();
