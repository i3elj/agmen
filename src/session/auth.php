<?php declare(strict_types=1);

namespace tusk\session;

function sign_in(string $email): void
{
    $_SESSION["signed"] = true;
    $_SESSION["email"] = $email;

    if (!isset($_SESSION['csrf_token'])) {
        generate_csrf_token();
    }

    unset($_SESSION["guest"]);
}

function sign_out(): void
{
    $_SESSION["signed"] = false;
    unset($_SESSION["email"]);
    unset($_SESSION['csrf_token']);
    sign_guest();
}

function is_signed(): bool
{
    return $_SESSION["signed"] ?? false;
}

function signed_user(): string|NULL
{
    return is_signed() ? $_SESSION["email"] : $_SESSION["guest"];
}

function sign_guest(): void
{
    if (!is_signed()) {
        $_SESSION["guest"] = $_COOKIE[session_name()] ?? '';
    }
}

function user_type(): string
{
    return is_signed() ? "signed" : "guest";
}
