<?php declare(strict_types=1);

namespace Tusk;

use PDO;
use PDOException;
use Exception;
use PDOStatement;

class Database
{
	private PDO $pdo;
	private static ?Database $instance = null;

	private function __construct() {}

	/**
	 * Connects to a database
	 *
	 * @return Database
	 */
	public static function connect(): Database
	{
		if (self::$instance != null) {
			return self::$instance;
		}

		$ENV = parse_ini_file(\BASE_PATH . ".env");

		try {
			return $ENV["DB"] == "sqlite"
				? self::sqlite_connect($ENV)
				: self::server_connect($ENV, driver_name: $ENV["DB"]);
		} catch (Exception $e) {
			throw new Exception(
				"Warning: .env file doesn't have a DB variable",
			);
		}
	}

	/**
	 * Connects to a database that uses a server, like MySQL, PostgreSQL, MariaDB, etc...
	 *
	 * @param array<mixed> $ENV         Environment variables with the database information.
	 * @param string       $driver_name The name of the driver used.
	 *
	 * @return Database
	 */
	private static function server_connect($ENV, $driver_name): Database
	{
		$HOST = $ENV["DB_HOST"];
		$PORT = $ENV["DB_PORT"];
		$USER = $ENV["DB_USER"];
		$PWD = $ENV["DB_PASSWORD"];
		$NAME = $ENV["DB_NAME"];
		$dsn = "$driver_name:host=$HOST;port=$PORT;dbname=$NAME;user=$USER;password=$PWD";
		self::$instance = new Database();
		(self::$instance->pdo = new PDO($dsn, $USER, $PWD)) or
			throw new PDOException();
		self::$instance->pdo->setAttribute(
			PDO::ATTR_DEFAULT_FETCH_MODE,
			PDO::FETCH_ASSOC,
		);
		self::$instance->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		return self::$instance;
	}

	/**
	 * @param array $ENV
	 * @return Database
	 */
	private static function sqlite_connect($ENV): Database
	{
		$URL = $ENV["DB_URL"];
		self::$instance = new Database();
		self::$instance->pdo = new PDO("sqlite:" . \BASE_PATH . $URL);
		self::$instance->pdo->setAttribute(
			PDO::ATTR_DEFAULT_FETCH_MODE,
			PDO::FETCH_ASSOC,
		);
		self::$instance->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		return self::$instance;
	}

	/**
	 * Runs a query in the database.
	 *
	 * @param  string $query  The query you want to run
	 * @param  array  $values All the values the query needs
	 * @return PDO|int
	 */
	public function sql($query, $values = []): int|PDO
	{
		$stmt = self::$instance->pdo->prepare($query);
		$succeeded = $stmt->execute($values);

		if (!$succeeded) {
			printf("Prepare statement error: " . $stmt);
			$stmt = null;
			exit(1);
		}

		return self::$instance->pdo;
	}

	/**
	 * Runs a query in the database using a file path as argument instead of a
	 * query. The file should be a .sql file.
	 *
	 * @param  string $path   The absolute path to the .sql file containing your query.
	 * @param  array  $values All the values the query needs
	 * @return PDO|int
	 */
	public function fsql($path, $values = []): int|PDO
	{
		$file_content = file_get_contents($path);
		$stmt = self::$instance->pdo->prepare($file_content);
		$succeeded = $stmt->execute($values);

		if (!$succeeded) {
			printf("Prepare statement error: " . $stmt);
			$stmt = null;
			exit(1);
		}

		return self::$instance->pdo;
	}

	/**
	 * Runs a query in the database and return the affected rows.
	 *
	 * @param string $query  The query you want to run
	 * @param array  $values All the values the query needs
	 * @return array $rows, $count
	 */
	public function sqlr($query, $values = []): array
	{
		$stmt = self::$instance->pdo->prepare($query);
		$succeeded = $stmt->execute($values);

		if (!$succeeded) {
			printf("Prepare statement error: " . $stmt);
			$stmt = null;
			exit(1);
		}

		$rows = $stmt->fetchAll();

		return [$rows, count($rows)];
	}

	/**
	 * Runs a query in the database and return the affected rows. This version
	 * uses a .sql file instead of a query
	 *
	 * @param  string $path   The absolute path to the .sql file containing your query.
	 * @param  array  $values All the values the query needs.
	 * @return array $rows, $count
	 */
	public function fsqlr(string $path, array $values = []): array
	{
		$file_content = file_get_contents($path);
		$stmt = self::$instance->pdo->prepare($file_content);
		$succeeded = $stmt->execute($values);

		if (!$succeeded) {
			printf("Prepare statement error: " . $stmt);
			$stmt = null;
			exit(1);
		}

		$rows = $stmt->fetchAll();

		return [$rows, count($rows)];
	}

	/**
	 * @param string $query
	 * @param array $values
	 * @return bool|int
	 */
	public function count($query, $values = []): bool|int
	{
		$stmt = self::$instance->pdo->prepare($query);
		$succeeded = $stmt->execute($values);

		if (!$succeeded) {
			printf("Prepare statement error: " . $stmt);
			$stmt = null;
			exit(1);
		}

		return $stmt->fetchColumn();
	}

	public function beginTransaction(): void
	{
		self::$instance->pdo->beginTransaction();
	}

	public function commit(): void
	{
		self::$instance->pdo->commit();
	}

	public function rollback(): void
	{
		self::$instance->pdo->rollBack();
	}

	/**
	 * @param string $query
	 * @return PDOStatement
	 */
	public function prepare(string $query): PDOStatement
	{
		return self::$instance->pdo->prepare($query);
	}
}
