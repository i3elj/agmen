<?php declare(strict_types=1);

abstract class Middleware {
    abstract public static function run(): bool;
}
