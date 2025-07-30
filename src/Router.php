<?php

declare(strict_types=1);

namespace tusk;

use function tusk\http\header\redirect;

class Router
{
	public readonly string $path;
	private array $params = [];
	private array $routes;
	private string $route;
	private string $base_route;

	public function __construct()
	{
		$this->path = URL['path'];
	}

	/**
	 * Redirects the request to the specified path
	 */
	public function path_redirect(string $from, string $to): Router
	{
		if ($this->path === $from) redirect($to);
		return $this;
	}

	/**
	 * Redirects to the path with the given name.
	 */
	public function redirect(string $path_name): Router
	{
		foreach ($this->routes as $route) {
			if ($route['name'] === $path_name) {
				redirect($route['path']);
			}
		}

		return $this;
	}

	/**
	 * Forwards the request to the specified controller
	 */
	public function path(string $path, string $controller, string $name, ?array $middlewares = []): Router
	{
		$this->routes[] = [ 
			'path'       => $path,
			'controller' => $controller,
			'name'       => $name,
		];

		if ($this->match_url_with_route($path)) {
			$this->route = $this->remove_params($path);
			
			foreach ($middlewares as $m) {
				$m::run();
			}

			controller($controller, prefix: \WEB_DIR);
			exit(0);
		}

		return $this;
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
	 * Resolves a view using the route passed to the `Route::path` method.
	 */
	public function view(array $ctx = []): void
	{
		extract($ctx, EXTR_SKIP);
		require_once base_path(\WEB_DIR . $this->route . "view.php");
	}

	/**
	 * Returns a snip based on the router as the `component_path`
	 */
	public function snip(string $name, array $ctx = []): void
	{
		snip($name, $ctx, $this->base_route);
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
		$param_name_regex = ':([a-zA-Z]+)';
		$param_type_regex = '\((word|number|string)\)';
		$full_param_regex = "/$param_name_regex$param_type_regex/";

		if (preg_match_all($full_param_regex, $path, $matches)) {
			$this->base_route = preg_replace('/\/?:[a-zA-Z]+\((word|number|string)\)/', '', $path);
			$params = array_map(null, ...array_slice($matches, 1));

			for ($i = 0; $i < sizeof($params); $i++) {
				$type_based_regex = $this->get_type_regex($params[$i][1]);
				array_push($params[$i], $type_based_regex);
			}

			$type_replace_regex = "/$param_name_regex($param_type_regex)?/";

			$route_with_types = preg_replace_callback(
				$type_replace_regex,
				fn ($matches) => '(' . $this->get_type_regex(str_replace(['(', ')'], ['',''], $matches[2])) . ')',
				$path
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

	/**
	 * Get the corresponding type from a regex. (e.g. word, number or string)
	 */
	private function get_type_regex(string $type_name): string 
	{
		return match ($type_name) {
			'word' => '[A-Za-z]+',
			'number' => '\d+',
			'string' => '[A-Za-z0-9\-]+',
			default => exit
		};
	}

	/**
	 * Removes the parameters from the route's path.
	 */
	private function remove_params(string $path): string
	{
		return rtrim(preg_replace(
			"/:[A-Za-z]+\((word|number)\)(\/)?/",
			"",
			$path
		), '/');
	}
}
