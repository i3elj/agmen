<?php declare(strict_types=1);

namespace Agmen\Http;

class HTTP
{
	/**
	 * @param string $key
	 * @param mixed $type
	 * @param mixed $default
	 */
	public static function Request(string $key, mixed $type = "string", mixed $default = null): mixed
	{
		return match ($_SERVER["REQUEST_METHOD"]) {
			"GET" => self::ParseSuperglobal($_GET, $key, $type, $default),
			"POST" => self::ParseSuperglobal($_POST, $key, $type, $default),
			"PATCH", "PUT", "DELETE" => self::Body($key, $type, $default),
			default => Status::method_not_allowed(),
		};
	}

	/**
	 * Returns the body of a POST request.
	 *
	 * @param string $key     The key of the POST body.
	 * @param string $type	  The function will try to convert the value to this type. Throwing a 422 status code (e.g. unprocessable entity).
	 * @param mixed  $default The default value to return if the key is not found.
	 */
	public static function Post($key, $type = "string", $default = null): mixed
	{
		return self::ParseSuperglobal($_POST, $key, $type, $default);
	}

	/**
	 * Returns the query string of a request.
	 *
	 * @param string $key      The key of the query string.
	 * @param string $type     The function will try to convert the value to this type. Throwing a 422 status code (e.g. unprocessable entity).
	 * @param mixed  $default  The default value to return if the key is not found.
	 */
	public static function Get($key, $type = "string", $default = null): mixed
	{
		return self::ParseSuperglobal($_GET, $key, $type, $default);
	}

	/**
	 * Returns the body of a request.
	 *
	 * @param string $key     The key of the body.
	 * @param string $type    The function will try to convert the value to this type. Throwing a 422 status code (e.g. unprocessable entity).
	 * @param mixed  $default The default value to return if the key is not found.
	 */
	public static function Body($key, $type = "string", $default = null): mixed
	{
		$temp_arr = [];
		parse_str(urldecode(file_get_contents("php://input")), $temp_arr);
		return self::ParseSuperglobal($temp_arr, $key, $type, $default);
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
	private static function ParseSuperglobal(array $superglobal, string $key, string $type, mixed $default = null): mixed
	{
		if (!array_key_exists($key, $superglobal)) {
			if (!isset($default)) {
				Status::bad_request(false);
				return null;
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

		Status::unprocessable_content();
		return null;
	}

	/**
	 * Returns the files of a request.
	 *
	 * @param string $key     The key of the files.
	 * @param mixed  $default The default value to return if the key is not found.
	 * @return array|NULL
	 */
	public static function Files(string $key, mixed $default = null): array|NULL
	{
		$key_was_sent = array_key_exists($key, $_FILES);
		$value_is_empty = sizeof($_FILES[$key]) == 0;
		$key_was_sent_but_empty = $key_was_sent && $value_is_empty;
		$default_is_set = isset($default);

		if ((!$key_was_sent && !$default_is_set) || ($key_was_sent_but_empty && !$default_is_set)) {
			Status::bad_request();
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
				"error" => $files["error"][$i],
			]);
		}

		return $transposed_files;
	}
}
