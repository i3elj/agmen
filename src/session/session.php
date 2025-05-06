<?php declare(strict_types=1);

namespace tusk\session;

function init()
{
    session_name();
    session_start();
    sign_guest();

    if (!isset($_SESSION['csrf_token'])) {
        generate_csrf_token();
    }
}

function get_csrf_token()
{
    return $_SESSION['csrf_token'] ?? NULL;
}

function generate_csrf_token()
{
    $random_key = bin2hex(random_bytes(32) . time());
    $_SESSION['csrf_token'] = $random_key;
}

function compare_csrf_tokens(?string $token)
{
    return hash_equals($_SESSION['csrf_token'] ?? '', $token ?? '');
}
