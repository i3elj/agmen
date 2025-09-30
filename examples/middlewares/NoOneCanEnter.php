<?php

namespace middlewares;

use Agmen\Http\Status;
use Agmen\Middleware;
use function Agmen\error;
use function Agmen\error_page;

class NoOneCanEnter extends Middleware
{
	public static function run(): void
	{
		error("Not allowed");
		Status::unauthorized(kill: false); // if it kills here, the page doesn't show up
		error_page("cant-enter");
		exit(1);
	}
}
