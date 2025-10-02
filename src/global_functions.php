<?php declare(strict_types=1);

namespace Agmen;

use DirectoryIterator;
use Agmen\Http\Status;

/**
 * Requires once the file and extract every associative array inside $ctx. If
 * the $path passed is a directory, requires everything recursively.
 */
function import(string $path, array $ctx = [], int $index = 0): void
{
	$base = $index == 0 ? \BASE_PATH . $path : $path;

	if (is_dir($base)) {
		$iter = new DirectoryIterator($base);
		foreach ($iter as $file) {
			$filename = $file->getRealPath();

			if ($file->isDot()) {
				continue;
			}

			if (is_dir($filename)) {
				import($filename, index: 1);
				continue;
			}

			if (pathinfo($filename, PATHINFO_EXTENSION) == "php") {
				extract($ctx);
				require_once $filename;
			}
		}
		return;
	}

	extract($ctx);
	require_once $base;
	return;
}

/**
 * Dies and dump.
 */
function dd(...$args)
{
	echo "<pre>";
	var_dump(...$args);
	echo "</pre>";
	die();
}

/**
 * Just dump, no killing here
 */
function just_dump(...$args)
{
	echo "<pre>";
	var_dump(...$args);
	echo "</pre>";
}

///////////////////////////
// WEB RELATED FUNCTIONS //
///////////////////////////

/**
 * Triggers a method not allowed status code on response and kill the process
 * if the request method is not present in the parameters.
 */
function allowed_methods(string ...$methods)
{
	if (!in_array($_SERVER["REQUEST_METHOD"], $methods)) {
		Status::method_not_allowed();
	}
}

/**
 * Calls the `main.php` controller of a specific `$route`.
 *
 * @param string $route Route
 * @param string $prefix Route's prefix
 *
 * @return void
 */
function controller(string $route, string $prefix = "")
{
	if (is_file(\BASE_PATH . $prefix . $route)) {
		require_once \BASE_PATH . $prefix . $route;
		return;
	}
	require_once \BASE_PATH . $prefix . $route . "main.php";
}

////////////////////////////////
// TEMPLATE RELATED FUNCTIONS //
////////////////////////////////

/**
 * Calls the `view.php` view of a specific route. This functions is dynamic,
 * it uses the `URL['path']` global variable to determine which `view.php`
 * should be called.
 *
 * @param array $ctx Context variables that should be accessible in the view.
 *
 * @return void
 */
function view(string $name = "view", $ctx = [])
{
	extract($ctx);
	require_once \BASE_PATH . \WEB_DIR . "$name.view.php";
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
function snip($name, $ctx = [], $components_path = \URL["path"])
{
	extract($ctx);
	$name = str_replace(".", "/", $name);
	require \BASE_PATH . \COMPONENTS_DIR . "$name.php";
}

/**
 * Calls a specific html snippet that should be used sporadically, like
 * `<head/>` or `<nav/>`. Uses the `GLOBALS_DIR` global variable to locate
 * the files.
 *
 * @param string $name The name of the file without the .php extension.
 * @param array $ctx Context variables that should be accessible in the view.
 */
function globals($name, $ctx = [])
{
	extract($ctx);
	require \BASE_PATH . \GLOBALS_DIR . "$name.php";
}

function icon(string $name, string $ext = "svg", array $ctx = []): void
{
	extract($ctx);
	require \BASE_PATH . \ICONS_DIR . "$name.$ext";
}

function svg(string $name, array $ctx = []): void
{
	extract($ctx);
	require \BASE_PATH . \SVG_DIR . "$name.svg";
}

/**
 * Return an error page with an exit(1). Uses the `ERROR_PAGES_DIR` to locate
 * the page.
 *
 * @param string $name Name of the error page without the .php extension.
 */
function error_page($name)
{
	require_once \BASE_PATH . \ERROR_PAGES_DIR . "$name.php";
	exit(1);
}

////////////////////////////
// LOGS RELATED FUNCTIONS //
////////////////////////////

function todo(string $msg = ""): void
{
	$e = new \Exception($msg);
	error_log("TODO: {$e->getMessage()} at {$e->getTraceAsString()}", 0);
	return;
}

function error(string $msg, int $code = 4): void
{
	error_log($msg, 4);
	return;
}

///////////////////////////////////////
// FILE OPERATIONS RELATED FUNCTIONS //
///////////////////////////////////////

function get_mime_type(string $filename): bool|string
{
	$finfo = new \finfo(FILEINFO_MIME_TYPE);

	if (!$finfo) {
		finfo_close($finfo);
		return false;
	}

	$mime_type = $finfo->file($filename);
	finfo_close($finfo);

	return $mime_type;
}

function get_upload_dir(): string
{
	$currDir = realpath(dirname(getcwd()));
	$uploadDir = "$currDir/public_html/uploads/";

	if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
		mkdir($uploadDir);
	}

	return $uploadDir;
}
