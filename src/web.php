<?php declare(strict_types=1);

namespace tusk;

/**
 * Apply middlewares to a route. If every middleware returns true
 * the controller is called. Every middleware should take care of solving
 * the "otherwise" branch. Generally they exit(1).
 *
 * @param string $route Route
 * @param string ...$mc Middleware classes
 *
 * @return void
 */
function middleware($route, ...$mc)
{
    if (array_reduce($mc, fn($carry, $m) => $carry && $m::run(), true)) {
        controller($route);
    }
}

/**
 * Calls the `main.php` controller of a specific `$route`.
 *
 * @param string $route Route
 *
 * @return void
 */
function controller($route)
{
    if (is_file(base_path($route))) {
        require_once base_path($route);
        return;
    }

    require_once base_path($route . "main.php");
}

/**
 * Calls the `view.php` view of a specific route. This functions is dynamic,
 * it uses the `URL['path']` global variable to determine which `view.php`
 * should be called.
 *
 * @param array $ctx Context variables that should be accessible in the view.
 *
 * @return void
 */
function view($ctx = [])
{
    extract($ctx);
    require_once base_path(\WEB_DIR . URL['path'] . "/view.php");
}

/**
 * Calls an html snippet based on the `URL['path']` global variable. It uses
 * the `COMPONENT_DIR_NAME` global variable to locate the correct folder under
 * the specified route.
 *
 * @param string $name The name of the file without the .php extension
 * @param array $ctx Context variables that should be accessible in the view.
 *
 * @return void
 */
function snip($name, $ctx = [])
{
    extract($ctx);
    require base_path(
        \WEB_DIR . URL['path'] . '/' . \COMPONENTS_DIR_NAME . "$name.php"
    );
}

/**
 * Calls a specific html snippet that should be used sporadically, like
 * `<head/>` or `<nav/>`. Uses the `PARTIALS_DIR` global variable to locate
 * the files.
 *
 * @param string $name The name of the file without the .php extension.
 * @param array $ctx Context variables that should be accessible in the view.
 */
function partials($name, $ctx = [])
{
    extract($ctx);
    require base_path(\PARTIALS_DIR . "$name.php");
}

/**
 * Return an error page with an exit(1). Uses the `ERROR_PAGES_DIR` to locate
 * the page.
 *
 * @param string $name Name of the error page without the .php extension.
 */
function error_page($name)
{
    require_once base_path(\ERROR_PAGES_DIR . "$name.php");
    exit(1);
}

function icon(string $name, array $ctx = []): void
{
    extract($ctx);
    require base_path(\ICONS_DIR . "$name.svg");
}

function svg(string $name, array $ctx = []): void
{
    extract($ctx);
    require base_path(\SVG_DIR . "$name.svg");
}
