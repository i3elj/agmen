<?php

namespace middlewares;

use Tusk\Http\Status;
use Tusk\Middleware;
use function Tusk\error;
use function Tusk\error_page;

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
