<?php declare(strict_types=1);

namespace Tusk\Http;

class Status
{
	/**
	 * The HTTP 302 Found redirection response status code indicates that the
	 * requested resource has been temporarily moved to the URL in the Location
	 * header. Read more at [mdn web docs](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/302).
	 */
	public static function found(bool $kill = true): void
	{
		http_response_code(302);
		if ($kill) exit(1);
	}

	/**
	 * The HTTP 308 Permanent Redirect redirection response status code indicates
	 * that the requested resource has been permanently moved to the URL given by
	 * the Location header. Read more at [mdn web docs](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/308).
	 */
	public static function permanent_redirect(bool $kill = true): void
	{
		http_response_code(308);
		if ($kill) exit(1);
	}

	/**
	 * The HTTP 400 Bad Request client error response status code indicates that the
	 * server would not process the request due to something the server considered
	 * to be a client error. The reason for a 400 response is typically due to
	 * malformed request syntax, invalid request message framing, or deceptive
	 * request routing. Read more at [mdn web docs](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/400).
	 */
	public static function bad_request(bool $kill = true): void
	{
		http_response_code(400);
		if ($kill) exit(1);
	}

	/**
	 * The HTTP 403 Forbidden client error response status code indicates that the
	 * server understood the request but refused to process it. This status is
	 * similar to 401, except that for 403 Forbidden responses, authenticating or
	 * re-authenticating makes no difference. The request failure is tied to
	 * application logic, such as insufficient permissions to a resource or action.
	 * Read more at [mdn web docs](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/403).
	 */
	public static function forbidden(bool $kill = true): void
	{
		http_response_code(403);
		if ($kill) exit(1);
	}

	/**
	 * The HTTP 404 Not Found client error response status code indicates that the
	 * server cannot find the requested resource. Links that lead to a 404 page are
	 * often called broken or dead links and can be subject to link rot. Read more
	 * at [mdn web docs](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/404).
	 */
	public static function not_found(bool $kill = true): void
	{
		http_response_code(404);
		if ($kill) exit(1);
	}

	/**
	 * The HTTP 405 Method Not Allowed client error response status code indicates
	 * that the server knows the request method, but the target resource doesn't
	 * support this method. The server must generate an Allow header in a 405
	 * response with a list of methods that the target resource currently supports.
	 * Read more at [mdn web docs](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/405).
	 */
	public static function method_not_allowed(bool $kill = true): void
	{
		http_response_code(405);
		if ($kill) exit(1);
	}

	/**
	 * The HTTP 409 Conflict client error response status code indicates a request
	 * conflict with the current state of the target resource. Read more at
	 * [mdn web docs](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/409).
	 */
	public static function conflict(bool $kill = true): void
	{
		http_response_code(409);
		if ($kill) exit(1);
	}

	/**
	 * The HTTP 422 Unprocessable Content client error response status code indicates
	 * that the server understood the content type of the request content, and the
	 * syntax of the request content was correct, but it was unable to process the
	 * contained instructions. Read more at
	 * [mdn web docs](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/409).
	 */
	public static function unprocessable_content(bool $kill = true): void
	{
		http_response_code(422);
		if ($kill) exit(1);
	}

	/**
	 * The HTTP 500 Internal Server Error server error response status code indicates
	 * that the server encountered an unexpected condition that prevented it from fulfilling
	 * the request. This error is a generic "catch-all" response to server issues,
	 * indicating that the server cannot find a more appropriate 5XX error to respond with.
	 * Read more at [mdn web docs](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/409).
	 */
	public static function internal_server_error(bool $kill = true): void
	{
		http_response_code(500);
		if ($kill) exit(1);
	}

	/**
	 * The HTTP 418 I'm a teapot status response code indicates that the server
	 * refuses to brew coffee because it is, permanently, a teapot. A combined
	 * coffee/tea pot that is temporarily out of coffee should instead return 503.
	 * This error is a reference to Hyper Text Coffee Pot Control Protocol defined
	 * in April Fools' jokes in 1998 and 2014. Read more at
	 * [mdn web docs](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/418).
	 */
	public static function im_a_teapot(bool $kill = true): void
	{
		http_response_code(418);
		if ($kill) exit(0);
	}
}
