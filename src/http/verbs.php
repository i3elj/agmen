<?php

declare(strict_types=1);

namespace tusk\http;

/**
 * Returns the body of a POST request.
 * 
 * @param string $key     The key of the POST body.
 * @param mixed  $default The default value to return if the key is not found.
 * @return string|int|array|NULL
 */
function POST($key, $default = NULL): string | int | array | NULL
{
    return _parse_superglobal($_POST, $key, $default);
}

/**
 * Returns the query string of a request.
 * 
 * @param string $key     The key of the query string.
 * @param mixed  $default The default value to return if the key is not found.
 * @return string|int|array|NULL
 */
function GET($key, $default = NULL): string | int | array | NULL
{
    return _parse_superglobal($_GET, $key, $default);
}

/**
 * Returns the body of a request.
 * 
 * @param string $key     The key of the body.
 * @param mixed  $default The default value to return if the key is not found.
 * @return string|int|array|NULL
 */
function BODY($key, $default = NULL): string | int | array | NULL
{
    $temp_arr = array();
    parse_str(urldecode(file_get_contents('php://input')), $temp_arr);
    return _parse_superglobal($temp_arr, $key, $default);
}

/**
 * Parse a superglobal array ($_POST, $_GET, etc...) and return the value of a key. It
 * also checks if the key was sent, if it's empty, and if a default value was set, and
 * makes sure its value is HTML escaped.
 * 
 * @param array $superglobal The superglobal array ($_POST, $_GET, etc...).
 * @param string $key        The key to look for in the superglobal array.
 * @param mixed $default     The default value to return if the key is not found.
 * @return string|int|array|NULL
 */
function _parse_superglobal($superglobal, $key, $default = NULL): array
{
    $key_was_sent = array_key_exists($key, $superglobal);
    $value_is_empty = strlen($superglobal[$key]) == 0;
    $key_was_sent_but_empty = $key_was_sent && $value_is_empty;
    $default_is_set = isset($default);

    if ((!$key_was_sent && !$default_is_set) || ($key_was_sent_but_empty && !$default_is_set)) {
        status\bad_request();
    }

    if ((!$key_was_sent && $default_is_set) || ($key_was_sent_but_empty && $default_is_set)) {
        return $default;
    }

    $result = $superglobal[$key];

    if (gettype($result) == "array") {
        foreach ($result as $i => $r) {
            $result[$i] = htmlspecialchars($r);
        }
    } else {
        $result = htmlspecialchars($result);
    }

    return $result;
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
    $value_is_empty = strlen($_FILES[$key]) == 0;
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
