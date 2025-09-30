<?php declare(strict_types=1);

namespace middlewares;

use Agmen\Middleware;

class SomeMiddleware extends Middleware
{
	public static function run(): void
	{
		echo <<<HTML
			<span>From Middleware</span>
		HTML;
	}
}
