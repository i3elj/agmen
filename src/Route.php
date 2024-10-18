<?php declare(strict_types=1);

namespace tusk;

use function tusk\http\header\redirect;

class Route
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
     *
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
     *
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
      *
      * @return Route
      */
    public function api($route, $controller_route)
    {
        if ($this->parse_url_params($route) || $this->path === $route) {
            $this->route = $this->remove_params($route);
            controller($controller_route, prefix: \API_DIR);
            exit(0);
        }

        return $this;
    }

    /**
     * Gets a specific url parameter by name.
     *
     * @param string $name The name of the url parameter.
     *
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
     *
     * @return void
     */
    public function view($ctx = [])
    {
        extract($ctx, EXTR_SKIP);
        require_once base_path(\WEB_DIR . $this->route. "view.php");
    }

    /**
     * @return boolean
     */
    private function parse_url_params($route)
    {
        $var_regex = ':([A-Za-z]+)';
        $type_regex = '\((word|number)\)';
        $var_type_regex = '/' . $var_regex . $type_regex . '/';

        if (preg_match($var_type_regex, $route, $output)) {
            $param = ['name' => $output[1], 'type'=> $output[2]];
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
     * @param string $route
     */
    private function remove_params($route)
    {
        return preg_replace(
            "/:[a-z]+\((word|number)\)(\/)?/",
            "",
            $route
        );
    }
}
