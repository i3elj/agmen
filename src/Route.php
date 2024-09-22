<?php declare(strict_types=1);

namespace tusk;

use function tusk\http\header\redirect;

class Route
{
    private array $params = [];

    public function __construct(
        public string $path,
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
        $name_type_regex = '/:([A-Za-z]+)\((string|int)\)/';
        $param = [];

        if (preg_match($name_type_regex, $route, $matches)) {
            $param = ['name' => $matches[1], 'type'=> $matches[2]];
            $type_regex = match ($param['type']) {
                'string' => '[a-z]+',
                'int' => '\d+',
            };
            $parsed_route = preg_replace(
                '/:([A-Za-z]+)(\((int|string)\))?/',
                "($type_regex)",
                $route
            );
            $parsed_route = preg_replace('/\//', '\/', $parsed_route);
            preg_match("/$parsed_route/", $this->path, $matches);
            $this->params[$param['name']] = $matches[1];
            return count($matches) > 0;
        }

        return false;
    }
}
