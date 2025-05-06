<?php declare(strict_types=1);

namespace tusk;

abstract class Middleware {
    abstract public static function run(): void;
}
