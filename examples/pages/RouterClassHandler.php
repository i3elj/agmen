<?php declare(strict_types=1);

namespace pages;

use Agmen\Http\Status;
use Agmen\Request;
use function Agmen\allowed_methods;
use function Agmen\view;

class RouterClassHandler
{
	/** Handle GET requests. Create a post() function for POST requests */
	public static function get(): void
	{
		view("home"); // 'home' extends to `\WEB_DIR . 'home.view.php'`
	}

	/** Or give it a name for more precision */
	public static function queries(Request $req): void
	{
		/* and handle methods inside */
		allowed_methods("GET"); // POST, PUT, DELETE => 405 Method Not Allowed
		$queries = $req->getQuery(); // getForm, getJson, getFiles too...
		view("queries", compact("queries")); // compact == ['queries' => $queries]
	}

	public static function parameters(): void
	{
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			Status::method_not_allowed(); // you can manually handle the response too
		}
		view("parameters", ["param" => r->param("foo")]);
	}

	public static function notAllowed(): void
	{
		echo <<<HTML
			<h1>Good luck trying to get here</h1>
		HTML;
	}
}
