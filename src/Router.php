<?php

declare(strict_types=1);

namespace tusk;

use function tusk\http\header\redirect;

class Router
{
	public readonly string $path;
	private array $params = [];
	private string $route;
	private string $base_route;

	public function __construct()
	{
		$this->path = URL['path'];
	}

	/**
	 * Redirects the request to the specified path
	 *
	 * @param string $from
	 * @param string $to
	 * @return Route
	 */
	public function redirect($from, $to)
	{
		if ($this->path === $from) redirect($to);
		return $this;
	}

	/**
	 * Forwards the request to the specified controller
	 *
	 * @param string $route The expected requested path.
	 * @param string $controller_route The route to the controller.
	 * @return Route
	 */
	public function path($route, $controller_route)
	{
		if ($this->parse_url_params($route) || $this->path === $route) {
			$this->route = $this->remove_params($route);
			controller($controller_route, prefix: \WEB_DIR);
			exit(0);
		}

		return $this;
	}

	/**
	 * Forwards the request to the specified controller
	 *
	 * @param string $route The expected requested path.
	 * @param string $controller_route The route to the controller.
	 * @param array $middlewares Every middleware that will be applied to the route.
	 * @return Route
	 */
	public function pathM($route, $controller_route, ...$middlewares)
	{
		if ($this->match_url_with_route($route)) {
			$this->route = $this->remove_params($route);
			$this->middleware($controller_route, \WEB_DIR, $middlewares);
			exit(0);
		}

		return $this;
	}

	public function middleware_group(array $middlewares, array $routes)
	{
		foreach ($routes as $route => $controller_route) {
			if ($this->match_url_with_route($route)) {
				$this->route = $this->remove_params($route);
				$this->middleware($controller_route, \WEB_DIR, $middlewares);
				exit(0);
			}
		}
	}

	/**
	 * Gets a specific url parameter by name.
	 *
	 * @param string $name The name of the url parameter.
	 * @return string|number|null
	 */
	public function param($name)
	{
		if (array_key_exists($name, $this->params)) {
			return $this->params[$name];
		}

		return null;
	}

	/**
	 * Resolves a view using the route passed to the `Route::path` method.
	 *
	 * @param array $ctx Any variable that should be available in the path.
	 * @return void
	 */
	public function view($ctx = [])
	{
		extract($ctx, EXTR_SKIP);
		require_once base_path(\WEB_DIR . $this->route . "view.php");
	}

	public function snip($name, $ctx = [])
	{
		snip($name, $ctx, $this->base_route);
	}

	private function match_url_with_route($route): bool
	{
		return $this->parse_url_params($route) || $this->path == $route;
	}

	/**
	 * Parses the url parameters and stores them in the $params array.
	 *
	 * @param string $route
	 * @return bool
	 */
	private function parse_url_params($route)
	{
		$param_name_regex = ':([a-zA-Z]+)';
		$param_type_regex = '\((word|number|string)\)';
		$full_param_regex = "/$param_name_regex$param_type_regex/";

		if (preg_match_all($full_param_regex, $route, $matches)) {
			$this->base_route = preg_replace('/\/?:[a-zA-Z]+\((word|number|string)\)/', '', $route);
			$params = array_map(null, ...array_slice($matches, 1));

			for ($i = 0; $i < sizeof($params); $i++) {
				$type_based_regex = $this->get_type_regex($params[$i][1]);
				array_push($params[$i], $type_based_regex);
			}

			$type_replace_regex = "/$param_name_regex($param_type_regex)?/";

			$route_with_types = preg_replace_callback(
				$type_replace_regex,
				fn ($matches) => '(' . $this->get_type_regex(str_replace(['(', ')'], ['',''], $matches[2])) . ')',
				$route
			);

			$route_with_backslashes = preg_replace('/\//', '\/', $route_with_types);

			// check if the route ends with a route param or not
			preg_match('/\(.*\)$/', $route_with_backslashes, $end_route_matches);

			$parsed_route_regex = count($matches) > 0 ? "$route_with_backslashes$" : "$route_with_backslashes";

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

	private function get_type_regex($type_name): string {
		return match ($type_name) {
			'word' => '[A-Za-z]+',
			'number' => '\d+',
			'string' => '[A-Za-z0-9\-]+',
			default => exit
		};
	}

	/**
	 * Removes the parameters from the route.
	 *
	 * @param string $route
	 * @return string
	 */
	private function remove_params($route)
	{
		return rtrim(preg_replace(
			"/:[A-Za-z]+\((word|number)\)(\/)?/",
			"",
			$route
		), '/');
	}


	/**
	 * Apply middlewares to a route. If every middleware returns true
	 * the controller is called. Every middleware should take care of solving
	 * the "otherwise" branch. Generally they exit(1).
	 *
	 * @param string $route Route.
	 * @param string $prefix Route's prefix.
	 * @param array<int, string> $mc Middleware classes.
	 * @return void
	 */
	private function middleware($route, $prefix, $mc)
	{
		if (array_reduce($mc, fn($carry, $m) => $carry && $m::run(), true)) {
			controller($route, $prefix);
		}
	}
}
