<?php declare(strict_types=1);

namespace Agmen\Security;

class Csrf
{
	public static function GetToken(): ?string
	{
		return $_SESSION['csrfToken'] ?? null;
	}

	public static function GenerateToken(): void
	{
		$random_key = bin2hex(random_bytes(32) . time());
		$_SESSION['csrfToken'] = $random_key;
	}

	public static function UnsetToken(): void
	{
		unset($_SESSION['csrfToken']);
	}

	public static function CompareTokens(?string $token): bool
	{
		return hash_equals($_SESSION['csrfToken'] ?? '', $token ?? '');
	}

	public static function Input(): string
	{
		$csrfToken = static::GetToken();
		return "<input type='hidden' name='csrfToken' value='$csrfToken' />";
	}
}
