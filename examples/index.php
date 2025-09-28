<?php declare(strict_types=1);

use pages\RouterClassHandler;
use Tusk\Router;

require "config.php";
require "../vendor/autoload.php";

const r = new Router();
r->path("/", "home", [], RouterClassHandler::class);
r->handle();
