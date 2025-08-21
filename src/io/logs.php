<?php declare(strict_types=1);

namespace tusk\io\logs;

function todo(string $msg = '')
{
    $e = new \Exception($msg);
    error_log("TODO: {$e->getMessage()} at {$e->getTraceAsString()}", 0);
}

function error(string $msg)
{
	error_log($msg, 4);
}
