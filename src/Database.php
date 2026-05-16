<?php

declare(strict_types=1);

namespace Agmen;

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

		$instance = null;

		try {
			$instance = match (ENV["DB"]) {
				"sqlite" => self::sqlite_connect(),
				default => self::server_connect()
			};
		} catch (Exception $e) {
			throw new Exception("Couldn't connect to database: $e");
		}

		return $instance;
	}

	/**
	 * Connects to a database that uses a server, like MySQL, PostgreSQL, MariaDB, etc...
	 *
	 * @return Database
	 */
	private static function server_connect(): Database
	{
		$DRIVER = ENV['DB'];
		$HOST = ENV["DB_HOST"];
		$PORT = ENV["DB_PORT"];
		$USER = ENV["DB_USER"];
		$PWD = ENV["DB_PWD"];
		$NAME = ENV["DB_NAME"];
		$dsn = "$DRIVER:host=$HOST;port=$PORT;dbname=$NAME;charset=utf8mb4";
		self::$instance = new Database();

		try {
			self::$instance->pdo = new PDO($dsn, $USER, $PWD);
		} catch (PDOException $e) {
			match (DEV_MODE) {
				true => error_log("PDO couldn't access the database: $e", 4),
				false => error_log("PDO couldn't connect to the database, check your credentials", 4)
			};
			exit(1);
		}

		self::setAttributes();

		return self::$instance;
	}

	/**
	 * @return Database
	 */
	private static function sqlite_connect(): Database
	{
		self::$instance = new Database();
		self::$instance->pdo = new PDO("sqlite:" . \BASE_PATH . ENV["DB_URL"]);

		self::setAttributes();

		return self::$instance;
	}

	private static function setAttributes(): void
	{
		self::$instance->pdo->setAttribute(PDO::ATTR_PERSISTENT, true);
		self::$instance->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC,);
		self::$instance->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		return;
	}

	/**
	 * Runs a query in the database.
	 *
	 * @param  string $query  Use ? or :name for placeholders
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
	 * @param  string $query  Use ? or :name for placeholders.
	 * @param array  $values All the values the query needs.
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
