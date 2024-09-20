<?php declare(strict_types=1);

namespace arrays;

function array_escape(array $arr)
{
    foreach ($arr as $k => $v) {
        if (!is_string($v)) {
            throw new \Exception('Only strings allowed. Expected (string). Got ('.gettype($v).')');
        }
        $arr[$k] = htmlspecialchars($v);
    }
    return $arr;
}

function map(array $arr, callable $func): array
{
    for ($i = 0; $i < count($arr); $i++) {
        $arr[$i] = $func($arr[$i]);
    }
    return $arr;
}

function filter(array $arr, callable $func): array
{
    $new_arr = [];
    for ($i = 0; $i < count($arr); $i++) {
        if ($func($arr[$i])) {
            array_push($new_arr, $arr[$i]);
        }
    }
    return $new_arr;
}

function reduce(mixed $first, array $arr, callable $func): mixed
{
    $carry = null;
    for ($i = 0; $i < count($arr); $i++) {
        $carry = $func($carry, $arr[$i]);
    }
    return $carry;
}
