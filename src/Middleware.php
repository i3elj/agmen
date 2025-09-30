<?php declare(strict_types=1);

namespace Tusk;

abstract class Middleware {
    abstract public static function run(): void;
}
