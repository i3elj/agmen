<?php declare(strict_types=1);

namespace tusk\http;

function POST(string $key, $default = null): string | int | array | null
{
    if (!array_key_exists($key, $_POST) && !isset($default)) status\bad_request();
    if (!array_key_exists($key, $_POST) && isset($default)) return $default;

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
    if (!array_key_exists($key, $_GET) && !isset($default)) {
        status\bad_request();
    }

    if (!array_key_exists($key, $_GET) && isset($default)) {
        return $default;
    }

    $result = $_GET[$key];

    if (gettype($result) == "array"){
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
    if (!array_key_exists($key, $_FILES) && !isset($default)) {
        status\bad_request();
    }
    
    if (!array_key_exists($key, $_FILES) && isset($default)) {
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
