<?php declare(strict_types=1);

namespace Agmen;

class Request
{
	public string|array $rawData;
	public readonly string $method;
	public array $parsedData = [];

	public function __construct()
	{
		$this->method = $_SERVER["REQUEST_METHOD"] ?? "GET";
		$contentType = $_SERVER["CONTENT_TYPE"] ?? "";

		// get raw body
		$this->rawData = match ($this->method) {
			"GET" => $_GET,
			"POST" => $_POST,
			"PUT", "PATCH", "DELETE" => file_get_contents("php://input"),
			default => [],
		};

		// parse body
		if ($this->method === "GET") {
			$this->parsedData = $_GET;
		} elseif ($this->method === "POST") {
			$this->parsedData = $_POST;
		} elseif (str_starts_with($contentType, "application/json")) {
			$this->parsedData = json_decode($this->rawData, true) ?? [];
		} elseif (
			str_starts_with($contentType, "application/x-www-form-urlencoded")
		) {
			parse_str($this->rawData, $this->parsedData);
		} else {
			$this->parsedData = [];
		}
	}

	public function getQuery(): array
	{
		return $this->deepCopy($_GET);
	}

	public function getForm(): array
	{
		// Only relevant for POST/PUT/PATCH/DELETE with form data
		if ($this->method === "GET") {
			return [];
		}

		if ($this->method === "POST") {
			return $this->deepCopy($_POST);
		}

		if (
			isset($_SERVER["CONTENT_TYPE"]) &&
			str_starts_with(
				$_SERVER["CONTENT_TYPE"],
				"application/x-www-form-urlencoded",
			)
		) {
			$parsed = [];
			parse_str($this->rawData, $parsed);
			return $this->deepCopy($parsed);
		}

		return [];
	}

	public function getJson(): array
	{
		if (
			isset($_SERVER["CONTENT_TYPE"]) &&
			str_starts_with($_SERVER["CONTENT_TYPE"], "application/json")
		) {
			return $this->deepCopy(json_decode($this->rawData, true) ?? []);
		}
		return [];
	}

	public function getFiles(): array
	{
		$transposed = [];
		foreach ($_FILES as $field => $fileGroup) {
			foreach ($fileGroup as $attr => $values) {
				foreach ((array) $values as $i => $v) {
					$transposed[$field][$i][$attr] = $v;
				}
			}
		}
		return $transposed;
	}

	private function sanitize(mixed $val): mixed
	{
		if (is_array($val)) {
			return array_map([$this, "sanitize"], $val);
		}
		return htmlspecialchars((string) $val, ENT_QUOTES, "UTF-8");
	}

	private function deepCopy(array $data): array
	{
		return unserialize(serialize($this->sanitize($data)));
	}
}
