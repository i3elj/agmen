<?php declare(strict_types=1);

namespace Agmen;

abstract class Middleware {
    abstract public static function run(): void;
}
