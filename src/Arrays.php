<?php declare(strict_types=1);

namespace Agmen;

class Arrays
{
	/**
	 * @param array<int,mixed> $arr
	 * @return array
	 */
	public static function escape(array $arr): array
	{
		foreach ($arr as $k => $v) {
			if (!is_string($v)) {
				throw new \Exception('Only strings allowed. Expected (string). Got ('.gettype($v).')');
			}

			$arr[$k] = htmlspecialchars($v);
		}

		return $arr;
	}

	/**
	 * @param array<int,mixed> $arr
	 * @param callable(): mixed $func
	 * @return array
	 */
	public static function map(array $arr, callable $func): array
	{
		for ($i = 0; $i < count($arr); $i++) {
			$arr[$i] = $func($arr[$i]);
		}

		return $arr;
	}

	/**
	 * @param array<int,mixed> $arr
	 * @param callable(): mixed $func
	 * @return array
	 */
	public static function filter(array $arr, callable $func): array
	{
		$new_arr = [];

		for ($i = 0; $i < count($arr); $i++) {
			if ($func($arr[$i])) {
				array_push($new_arr, $arr[$i]);
			}
		}

		return $new_arr;
	}

	/**
	 * @param mixed $first
	 * @param array<int,mixed> $arr
	 * @param callable(): mixed $func
	 * @return mixed
	 */
	public static function reduce(mixed $first, array $arr, callable $func): mixed
	{
		$carry = null;

		for ($i = 0; $i < count($arr); $i++) {
			$carry = $func($carry, $arr[$i]);
		}

		return $carry;
	}
}
