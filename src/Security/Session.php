<?php declare(strict_types=1);

namespace Tusk\Security;

class Session
{
	public static string $key;

	public static function Init(): void
	{
		session_name();
		session_start();
		static::SignGuest();

		if (Csrf::GetToken() !== NULL) {
			Csrf::GenerateToken();
		}
	}

	public static function SignIn(string $key, string $signValue): void
	{
		$_SESSION["signed"] = true;
		$_SESSION[$key] = $signValue;
		static::$key = $key;

		if (Csrf::GetToken() !== NULL) {
			Csrf::GenerateToken();
		}

		unset($_SESSION["guest"]);
	}

	public static function SignOut(): void
	{
		$_SESSION["signed"] = false;
		unset($_SESSION[static::$key]);
		Csrf::UnsetToken();
		static::SignGuest();
	}

	public static function IsSigned(): bool
	{
		return $_SESSION["signed"] ?? false;
	}

	public static function SignedUser(): string|null
	{
		return static::IsSigned() ? $_SESSION["email"] : $_SESSION["guest"];
	}

	public static function SignGuest(): void
	{
		if (!static::IsSigned()) {
			$_SESSION["guest"] = $_COOKIE[session_name()] ?? '';
		}
	}

	public static function UserType(): string
	{
		return static::IsSigned() ? "signed" : "guest";
	}
}
