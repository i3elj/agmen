<?php

declare(strict_types=1);

namespace tusk\http;

function POST(string $key, $default = null): string | int | array | null
{
    $key_was_sent = array_key_exists($key, $_POST);
    $value_is_empty = strlen($_POST[$key]) == 0;
    $key_was_sent_but_empty = $key_was_sent && $value_is_empty;
    $default_is_set = isset($default);

    if ((!$key_was_sent && !$default_is_set) || ($key_was_sent_but_empty && !$default_is_set)) {
        status\bad_request();
    }

    if ((!$key_was_sent && $default_is_set) || ($key_was_sent_but_empty && $default_is_set)) {
        return $default;
    }

    $result = $_POST[$key];

    if (gettype($result) == "array") {
        foreach ($result as $i => $r) {
            $result[$i] = htmlspecialchars($r);
        }
    } else {
        $result = htmlspecialchars($result);
    }

    return $result;
}

function GET(string $key, $default = null): string | int | array | null
{
    $key_was_sent = array_key_exists($key, $_GET);
    $value_is_empty = strlen($_GET[$key]) == 0;
    $key_was_sent_but_empty = $key_was_sent && $value_is_empty;
    $default_is_set = isset($default);

    if ((!$key_was_sent && !$default_is_set) || ($key_was_sent_but_empty && !$default_is_set)) {
        status\bad_request();
    }

    if ((!$key_was_sent && $default_is_set) || ($key_was_sent_but_empty && $default_is_set)) {
        return $default;
    }

    $result = $_GET[$key];

    if (gettype($result) == "array") {
        foreach ($result as $i => $r) {
            $result[$i] = htmlspecialchars($r);
        }
    } else {
        $result = htmlspecialchars($result);
    }

    return $result;
}

function FILES(string $key, $default = null): array | null
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
