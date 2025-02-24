<?php

declare(strict_types=1);

namespace tusk;

use function tusk\http\header\redirect;

class Router
{
	public readonly string $path;
	private array $params = [];
	private string $route;

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
		$var_regex = ':([A-Za-z]+)';
		$type_regex = '\((word|number)\)';
		$var_type_regex = '/' . $var_regex . $type_regex . '/';

		if (preg_match($var_type_regex, $route, $output)) {
			$param = ['name' => $output[1], 'type' => $output[2]];
			$replace_type_regex = match ($param['type']) {
				'word' => '[a-z]+(_[a-z]+)*',
				'number' => '\d+',
			};

			$type_replace_regex = "/$var_regex($type_regex)?/";
			$parsed_route = preg_replace($type_replace_regex, "($replace_type_regex)", $route);
			$parsed_route = preg_replace('/\//', '\/', $parsed_route);

			// check if the route ends with a route param or not
			$res = preg_grep("/^.*:[a-z]+\((word|number)\)$/", [$route]);
			$parsed_route_regex = count($res) > 0 ?
				"/$parsed_route$/" :
				"/$parsed_route/";

			preg_match($parsed_route_regex, $this->path, $output);

			if (count($output) > 0) {
				$this->params[$param['name']] = $output[1];
				return true;
			}
		}

		return false;
	}

	/**
	 * Removes the parameters from the route.
	 *
	 * @param string $route
	 * @return string
	 */
	private function remove_params($route)
	{
		return preg_replace(
			"/:[a-z]+\((word|number)\)(\/)?/",
			"",
			$route
		);
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
