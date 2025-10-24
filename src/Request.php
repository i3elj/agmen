<?php declare(strict_types=1);

namespace Agmen;

#[\AllowDynamicProperties]
class Request
{
	public string|array $rawData;
	public readonly string $method;
	public readonly string $contentType;
	public array $data = [];

	public function __construct()
	{
		$this->method = $_SERVER["REQUEST_METHOD"] ?? 'GET';
		$this->contentType = $_SERVER["CONTENT_TYPE"] ?? "";
		$stream = @file_get_contents('php://input');

		if (str_starts_with($this->contentType, 'application/json')) {
			$this->rawData = json_decode($stream, true) ?? [];
			$this->data = $this->deepCopy($this->rawData);
		}

		else if (in_array($this->method, ['GET', 'POST'])) {
			$this->rawData = match ($this->method) {
				'GET' => $_GET,
				'POST' => $_POST,
			};

			$this->data = $this->deepCopy($this->rawData);
		}

		else {
			$this->rawData = $stream;
			parse_str($this->rawData, $this->data);
			$this->data = $this->deepCopy($this->data);
		}

		foreach ($this->data as $key => $val) {
			$this->{$key} = $val;
		}
	}

	public function json(): array|bool
	{
		if (str_starts_with($this->contentType, 'application/json')) {
			return $this->rawData;
		}

		return false;
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
		return htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8');
	}

	private function deepCopy(array $data): array
	{
		return unserialize(serialize($this->sanitize($data)));
	}
}
