<?php

namespace tusk;

#[\AllowDynamicProperties]
class Request
{
	public string|array $rawData;
	public readonly string $method;
	public array $parsedData = [];

	public function __construct()
	{
        $this->method = $_SERVER["REQUEST_METHOD"] ?? 'GET';
        $contentType = $_SERVER["CONTENT_TYPE"] ?? "";

        // get raw data
        $this->rawData = match ($this->method) {
            "GET" => $_GET,
            "POST", "PUT", "PATCH", "DELETE" => file_get_contents("php://input"),
            default => [],
        };

        // parse input
        if ($this->method === 'GET') {
            $this->parsedData = $_GET;
        } elseif (str_starts_with($contentType, 'application/json')) {
            $this->parsedData = json_decode($this->rawData, true) ?? [];
        } elseif (str_starts_with($contentType, 'application/x-www-form-urlencoded')) {
            parse_str($this->rawData, $this->parsedData);
        } else {
            $this->parsedData = [];
        }

        // attach parsed data as object properties
        foreach ($this->parsedData as $key => $val) {
            $this->{$key} = $this->sanitize($val);
        }
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
		return htmlspecialchars((string) $val);
	}
}
