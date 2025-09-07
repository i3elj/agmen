<?php declare(strict_types=1);

namespace tusk;

class Session
{
	public static string $key;

	public static function Init()
	{
		session_name();
		session_start();
		static::SignGuest();

		if (!isset($_SESSION['csrf_token'])) {
			static::GenerateCsrfToken();
		}
	}

	public static function SignIn(string $key, string $signValue): void
	{
		$_SESSION["signed"] = true;
		$_SESSION[$key] = $signValue;
		static::$key = $key;

		if (!isset($_SESSION['csrf_token'])) {
			static::GenerateCsrfToken();
		}

		unset($_SESSION["guest"]);
	}

	public static function SignOut(): void
	{
		$_SESSION["signed"] = false;
		unset($_SESSION[static::$key]);
		unset($_SESSION['csrf_token']);
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

	public static function GetCsrfToken()
	{
		return $_SESSION['csrf_token'] ?? NULL;
	}

	public static function GenerateCsrfToken()
	{
		$random_key = bin2hex(random_bytes(32) . time());
		$_SESSION['csrf_token'] = $random_key;
	}

	public static function CompareCsrfTokens(?string $token)
	{
		return hash_equals($_SESSION['csrf_token'] ?? '', $token ?? '');
	}
}