<?php declare(strict_types=1);

namespace tusk;

use function tusk\http\header\redirect;

class Route
{
    private array $params = [];

    public function __construct(
        public readonly string $path,
    ) {}

    public function redirect(string $from, string $to)
    {
        if ($this->path === $from) redirect($to);
        return $this;
    }

    public function path($route, $controller_route)
    {
        if ($this->parse_url_params($route) || $this->path === $route) {
            controller($controller_route);
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
     * @return boolean
     */
    private function parse_url_params($route)
    {
        $var_type_regex = '/:([A-Za-z]+)\((word|number)\)/';

        if (preg_match($var_type_regex, $route, $output)) {
            $param = ['name' => $output[1], 'type'=> $output[2]];
            $type_regex = match ($param['type']) {
                'word' => '[a-z]+',
                'number' => '\d+',
            };

            $type_replace_regex ='/:([A-Za-z]+)(\((word|number)\))?/';
            $parsed_route = preg_replace($type_replace_regex, "($type_regex)", $route);
            $parsed_route = preg_replace('/\//', '\/', $parsed_route);

            // check if the route ends with a route param or not
            $parsed_route_regex =
                count(preg_grep("/^.*:[a-z]+\((word|number)\)$/", [$route])) > 0
                ? "/$parsed_route$/"
                : "/$parsed_route/";

            preg_match($parsed_route_regex, $this->path, $output);

            if (count($output) > 0) {
                $this->params[$param['name']] = $output[1];
                return true;
            }
        }

        return false;
    }
}
