<?php declare(strict_types=1);

namespace tusk\session;

function init(string $name = "SessionName")
{
    session_name(SESSION_NAME ?? $name);
    session_start();
    sign_guest();
}
