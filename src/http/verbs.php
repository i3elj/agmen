<?php

declare(strict_types=1);

namespace tusk\http\methods;

use function tusk\http\status\bad_request;
use function tusk\http\status\method_not_allowed;
use function tusk\http\status\unprocessable_entity;

function request($key, $type = 'string', $default = NULL): mixed
{
	return match($_SERVER['REQUEST_METHOD']) {
		'GET' => _parse_superglobal($_GET, $key, $type, $default),
		'POST' => _parse_superglobal($_POST, $key, $type, $default),
		'PATCH', 'PUT', 'DELETE' => BODY($key, $type, $default),
		default => method_not_allowed()
	};
}

/**
 * Returns the body of a POST request.
 *
 * @param string $key     The key of the POST body.
 * @param string $type	  The function will try to convert the value to this type. Throwing a 422 status code (e.g. unprocessable entity).
 * @param mixed  $default The default value to return if the key is not found.
 */
function POST($key, $type = 'string', $default = NULL): mixed
{
	return _parse_superglobal($_POST, $key, $type, $default);
}

/**
 * Returns the query string of a request.
 *
 * @param string $key      The key of the query string.
 * @param string $type     The function will try to convert the value to this type. Throwing a 422 status code (e.g. unprocessable entity).
 * @param mixed  $default  The default value to return if the key is not found.
 */
function GET($key, $type = 'string', $default = NULL): mixed
{
	return _parse_superglobal($_GET, $key, $type, $default);
}

/**
 * Returns the body of a request.
 *
 * @param string $key     The key of the body.
 * @param string $type    The function will try to convert the value to this type. Throwing a 422 status code (e.g. unprocessable entity).
 * @param mixed  $default The default value to return if the key is not found.
 */
function BODY($key, $type = 'string', $default = NULL): mixed
{
	$temp_arr = array();
	parse_str(urldecode(file_get_contents('php://input')), $temp_arr);
	return _parse_superglobal($temp_arr, $key, $type, $default);
}

/**
 * Parse a superglobal array ($_POST, $_GET, etc...) and return the value of a key. It
 * also checks if the key was sent, if it's empty, and if a default value was set, and
 * makes sure its value is HTML escaped.
 *
 * @param array $superglobal The superglobal array ($_POST, $_GET, etc...).
 * @param string $key        The key to look for in the superglobal array.
 * @param string $type       The function will try to convert the value to this type. Throwing a 422 status code (e.g. unprocessable entity).
 * @param mixed $default	 The default value to return if the key is not found.
 */
function _parse_superglobal($superglobal, $key, $type, $default = NULL): mixed
{
	if (!array_key_exists($key, $superglobal)) {
		if (!isset($default)) {
			bad_request(false);
			return NULL;
		}
		return $default;
	}

	$value = $superglobal[$key];

	if (gettype($value) == "array") {
		foreach ($value as $i => $r) {
			$value[$i] = htmlspecialchars($r);
		}

		return $value;
	}

	$value = htmlspecialchars($value);
	$res = settype($value, $type);

	if ($res != 0) {
		return $value;
	}

	unprocessable_entity();
}

/**
 * Returns the files of a request.
 *
 * @param string $key     The key of the files.
 * @param mixed  $default The default value to return if the key is not found.
 * @return array|NULL
 */
function FILES(string $key, $default = NULL): array | NULL
{
	$key_was_sent = array_key_exists($key, $_FILES);
	$value_is_empty = sizeof($_FILES[$key]) == 0;
	$key_was_sent_but_empty = $key_was_sent && $value_is_empty;
	$default_is_set = isset($default);

	if ((!$key_was_sent && !$default_is_set) || ($key_was_sent_but_empty && !$default_is_set)) {
		status\bad_request();
	}

	if ((!$key_was_sent && $default_is_set) || ($key_was_sent_but_empty && $default_is_set)) {
		return $default;
	}

	$files = $_FILES[$key];
	$transposed_files = [];

	foreach ($files["name"] as $i => $name) {
		array_push($transposed_files, [
			"filename" => $name,
			"tmp_name" => $files["tmp_name"][$i],
			"error"    => $files["error"][$i]
		]);
	}

	return $transposed_files;
}
