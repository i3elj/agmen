<?php declare(strict_types=1);

namespace tusk;

function middleware(string $route, string ...$mc): void
{
    if (array_reduce($mc, fn($carry, $m) => $carry && $m::run(), true)) {
        controller($route);
    }
}

function controller(string $path, array $ctx = []): void
{
    extract($ctx);

    if (is_file(base_path($path))) {
        require_once base_path($path);
        return;
    }

    require_once base_path($path . "main.php");
    return;
}

function view(array $ctx = []): void
{
    extract($ctx);
    require_once base_path(WEB_DIR . URL['path'] . "view.php");
}

function snip(string $name, array $ctx = []): void
{
    extract($ctx);
    require base_path(
        WEB_DIR . URL['path'] . COMPONENTS_DIR . "$name.php"
    );
}

function partials(string $name, array $ctx = []): void
{
    extract($ctx);
    require base_path(PARTIALS_DIR . "$name.php");
}

function error_page(string $name): void
{
    require_once base_path(ERROR_PAGES_DIR . "$name.php");
    exit(1);
}

function icon(string $name, array $ctx = []): void
{
    extract($ctx);
    require base_path(ICONS_DIR . "$name.svg");
}

function svg(string $name, array $ctx = []): void
{
    extract($ctx);
    require base_path(SVG_DIR . "$name.svg");
}
