<?php

declare(strict_types=1);

namespace Tusk;

use Tusk\Http\Header;
use Tusk\Http\Status;
use function Tusk\snip;

class Router
{
	public readonly string $path;
	private array $params = [];
	private array $routes;
	private array $route;
	private string $prefix = "";
	private array $middlewares = [];
	private string $base_route;

	public function __construct()
	{
		$this->path = parse_url($_SERVER["REQUEST_URI"])["path"];
	}

	/**
	 * Get the path value with the correspondig path name.
	 */
	public function getPath(
		string $path_name,
		?array $route_params = null,
		?array $query_params = null,
	): string {
		$uri = $this->routes[$path_name]["uri"];

		if (isset($route_params)) {
			foreach ($route_params as $var => $val) {
				$uri = preg_replace("/(:$var\([A-Za-z]+\))/", $val, $uri);
			}
		}

		if (isset($query_params)) {
			$uri .= '?' . http_build_query($query_params);
		}

		return $uri;
	}

	/**
	 * Forwards the request to the specified controller
	 * @param array<int,mixed> $middlewares
	 */
	public function path(
		string $uri,
		string $name,
		array $middlewares,
		string $handler,
		?string $method = "",
	): Router {
		$fullUri = rtrim($this->prefix, "/") . "/" . ltrim($uri, "/");
		$fullMiddleware = array_merge($this->middlewares, $middlewares);

		$this->routes[$name] = [
			"uri" => $fullUri,
			"handler" => $handler,
			"middlewares" => $fullMiddleware,
			"method" => $method,
		];

		return $this;
	}
	/**
	 * @param array<int,mixed> $middlewares
	 * @param callable(): mixed $callback
	 */
	public function group(
		string $prefix,
		array $middlewares,
		callable $callback,
	): void {
		$currentPrefix = $this->prefix;
		$currentMiddleware = $this->middlewares;

		$this->prefix = rtrim($currentPrefix, "/") . "/" . ltrim($prefix, "/");
		$this->middlewares = array_merge($currentMiddleware, $middlewares);

		$callback($this);

		$this->prefix = $currentPrefix;
		$this->middlewares = $currentMiddleware;
	}

	/**
	 * Gets a specific url parameter by name.
	 */
	public function param(string $name): string|int|null
	{
		if (array_key_exists($name, $this->params)) {
			return $this->params[$name];
		}

		return null;
	}

	/**
	 * Handle the upcoming request.
	 */
	public function handle(): void
	{
		foreach ($this->routes as $route_name => $route) {
			if ($this->match_url_with_route($route["uri"])) {
				$this->route = [
					"name" => $route_name,
					"uri" => $route["uri"],
					"handler" => $route["handler"],
					"middlewares" => $route["middlewares"],
					"method" => $route["method"],
				];

				foreach ($route["middlewares"] as $m) {
					$m::run();
				}

				$reqMethod = strtolower($_SERVER["REQUEST_METHOD"]);

				if (
					strlen($route["method"]) > 0 &&
					method_exists($route["handler"], $route["method"])
				) {
					call_user_func(
						[$route["handler"], $route["method"]],
						new Request(),
					);
					exit();
				}

				if (method_exists($route["handler"], $reqMethod)) {
					call_user_func(
						[$route["handler"], $reqMethod],
						new Request(),
					);
					exit();
				}

				Status::method_not_allowed();
			}
		}
	}

	/**
	 * Redirects the request to the specified path
	 */
	public function pathRedirect(string $from, string $to): Router
	{
		if ($this->path === $from) {
			Header::redirect($to);
		}
		return $this;
	}

	/**
	 * Redirects to the path with the given name.
	 */
	public function redirect(string $path_name): void
	{
		Header::redirect($this->routes[$path_name]["uri"]);
	}

	/**
	 * Resolves a view using the route passed to the `Route::path` method.
	 * @param array<int,mixed> $ctx
	 */
	public function view(array $ctx = []): void
	{
		extract($ctx, EXTR_SKIP);
		require_once \BASE_PATH . \WEB_DIR . $this->route . "view.php";
	}

	/**
	 * Returns a snip based on the router as the `component_path`
	 * @param array<int,mixed> $ctx
	 */
	public function snip(string $name, array $ctx = []): void
	{
		snip($name, $ctx, $this->base_route);
	}

	/**
	 * Get the corresponding type from a regex. (e.g. word, number or string)
	 */
	private function get_type_regex(string $type_name): string
	{
		return match ($type_name) {
			"word" => "[A-Za-z]+",
			"number" => "\d+",
			"string" => "[A-Za-z0-9\-]+",
			default => exit(),
		};
	}

	/**
	 * Matches the url with the given route path's.
	 */
	private function match_url_with_route(string $path): bool
	{
		return $this->parse_url_params($path) || $this->path == $path;
	}

	/**
	 * Parses the url parameters and stores them in the $params array.
	 */
	private function parse_url_params(string $path): bool
	{
		$param_name_regex = ":([a-zA-Z]+)";
		$param_type_regex = "\((word|number|string)\)";
		$full_param_regex = "/$param_name_regex$param_type_regex/";

		if (preg_match_all($full_param_regex, $path, $matches)) {
			$this->base_route = preg_replace(
				"/\/?:[a-zA-Z]+\((word|number|string)\)/",
				"",
				$path,
			);
			$params = array_map(null, ...array_slice($matches, 1));

			for ($i = 0; $i < sizeof($params); $i++) {
				$type_based_regex = $this->get_type_regex($params[$i][1]);
				array_push($params[$i], $type_based_regex);
			}

			$type_replace_regex = "/$param_name_regex($param_type_regex)?/";

			$route_with_types = preg_replace_callback(
				$type_replace_regex,
				fn($matches) => "(" .
					$this->get_type_regex(
						str_replace(["(", ")"], ["", ""], $matches[2]),
					) .
					")",
				$path,
			);

			$route_with_backslashes = preg_replace(
				"/\//",
				"\/",
				$route_with_types,
			);

			// check if the route ends with a route param or not
			preg_match(
				'/\(.*\)$/',
				$route_with_backslashes,
				$end_route_matches,
			);

			$parsed_route_regex =
				count($matches) > 0
					? "$route_with_backslashes$"
					: "$route_with_backslashes";

			preg_match("/$parsed_route_regex/", $this->path, $matches);

			if (count($matches) > 0) {
				$param_names = array_map(fn($v) => $v[0], $params);
				$param_values = array_slice($matches, 1);
				$params = array_map(null, $param_names, $param_values);

				foreach ($params as $param) {
					$this->params[$param[0]] = $param[1];
				}

				return true;
			}
		}

		return false;
	}

	/**
	 * Removes the parameters from the route's path.
	 */
	private function remove_params(string $path): string
	{
		return rtrim(
			preg_replace(
				"/:[A-Za-z]+\((word|number|string)\)(\/)?/",
				"",
				$path,
			),
			"/",
		);
	}
}
