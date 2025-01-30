<?php declare(strict_types=1);

namespace tusk\session;

function init()
{
    session_name();
    session_start();
    sign_guest();
}
