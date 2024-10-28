<?php declare(strict_types=1);
// TODO: escape and clean data sent through requests

namespace tusk\http;

function POST(string $key, bool $opt = false, $default = null): string | int | array | null
{
    if (count($_POST) == 0 && !$opt) status\bad_request();
    if (count($_POST) == 0 && $opt) return $default;

    $result = $_POST[$key];

    if (gettype($result) == "array")
        foreach ($result as $i => $r) $result[$i] = htmlspecialchars($r);
    else
        $result = htmlspecialchars($result);

    return $result;
}

function GET(string $key, bool $opt = false, $default = null): string | int | array | null
{
    if (count($_GET) == 0 && !$opt) status\bad_request();
    if (count($_GET) == 0 && $opt) return $default;

    $result = $_GET[$key];

    if (gettype($result) == "array")
        foreach ($result as $i => $r) $result[$i] = htmlspecialchars($r);
    else
        $result = htmlspecialchars($result);

    return $result;
}

function FILES(string $key, bool $opt = false, $default = null): array | null
{
    if (count($_FILES) == 0 && !$opt) status\bad_request();
    if (count($_FILES) == 0 && $opt) return $default;

    $result = $_FILES[$key];
    $transposed_files = [];

    foreach ($files["name"] as $i => $name)
    {
        array_push($transposed_files, [
            "filename" => $name,
            "tmp_name" => $files["tmp_name"][$i],
            "error"    => $files["error"][$i]
        ]);
    }

    return $transposed_files;
}
