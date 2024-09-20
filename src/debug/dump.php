<?php declare(strict_types=1);

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
