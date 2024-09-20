<?php declare(strict_types=1);

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

function view(array $ctx = [], string $prefix = "web"): void
{
    extract($ctx);

    $viewPath = match ($prefix) {
        "api" => PATH . "view.php",
        default => $prefix . PATH . "view.php",
    };

    require_once base_path($viewPath);
    return;
}

function section(string $section, array $ctx = [], string $prefix = "web"): void
{
    extract($ctx);
    require base_path($prefix . PATH . "sections/" . $section . ".php");
    return;
}

function partials(string $name, array $ctx = []): void
{
    extract($ctx);
    require base_path("web/partials/" . $name . ".php");
    return;
}

function fstatic(string $path)
{
    return file_get_contents(base_path($path));
}

function error_page(string $name): void
{
    require_once base_path("web/errors/" . $name . ".php");
    exit(1);
}

function svg(string $name, array $ctx = []): void
{
    extract($ctx);
    require base_path("public_html/assets/svg/" . $name . ".svg");
    return;
}
