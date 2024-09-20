<?php declare(strict_types=1);

namespace tusk\io\logs;

function todo(string $filename, string $funcname, string $msg = '')
{
    $fileinfo = pathinfo($filename);
    $msg = "\n\t" . $msg;
    error_log(
        "\n\nTODO in $funcname() at {$fileinfo["dirname"]}/{$fileinfo["basename"]}"
      . $msg,
        0
    );
}

function error(string $err)
{
    error_log($err, 0);
}
