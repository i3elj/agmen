<?php declare(strict_types=1);

namespace tusk\io\files;

function get_mime_type(string $filename): bool|string
{
    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    if (!$finfo) {
        return false;
    }

    $mime_type = $finfo->file($filename);
    finfo_close($finfo);

    return $mime_type;
}

function get_upload_dir(): string
{
    $currDir = realpath(dirname(getcwd()));
    $uploadDir = $currDir . "/public_html/uploads/";

    if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
        mkdir($uploadDir);
    }

    return $uploadDir;
}
