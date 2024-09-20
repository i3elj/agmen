<?php declare(strict_types=1);

class Database
{
    private PDO $pdo;
    private static ?Database $instance = null;

    private function __construct()
    {
    }

    /**
     * Connects to a database
     *
     * @return Database
     */
    public static function connect()
    {
        if (self::$instance != null) {
            return self::$instance;
        }

        $ENV = parse_ini_file(base_path(".env"));

        return match ($ENV["DB"]) {
            "postgres" => self::pgsql_connect($ENV),
            "sqlite" => self::sqlite_connect($ENV),
            default => throw new Exception(
                "Warning: .env file doesn't have a DB variable"
            ),
        };
    }

    private static function pgsql_connect($ENV): Database
    {
        self::$instance = new Database();

        $HOST = $ENV["DB_HOST"];
        $PORT = $ENV["DB_PORT"];
        $USER = $ENV["DB_USER"];
        $PWD = $ENV["DB_PASSWORD"];
        $NAME = $ENV["DB_NAME"];

        $dsn = "pgsql:host=$HOST;port=$PORT;dbname=$NAME;user=$USER;password=$PWD";

        (self::$instance->pdo = new PDO($dsn, $USER, $PWD)) or throw new PDOException();
        self::$instance->pdo->setAttribute(
            PDO::ATTR_DEFAULT_FETCH_MODE,
            PDO::FETCH_ASSOC
        );
        self::$instance->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        return self::$instance;
    }

    private static function sqlite_connect($ENV)
    {
        self::$instance = new Database();

        $URL = $ENV["DB_URL"];
        self::$instance->pdo = new PDO("sqlite:" . base_path($URL));
        self::$instance->pdo->setAttribute(
            PDO::ATTR_DEFAULT_FETCH_MODE,
            PDO::FETCH_ASSOC
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
    public function sql($query, ...$values)
    {
        $stmt = self::$instance->pdo->prepare($query);

        $succeeded = $stmt->execute([...$values]);

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
     * @return array
     */
    public function sqlR($query, ...$values)
    {
        $stmt = self::$instance->pdo->prepare($query);

        $succeeded = $stmt->execute([...$values]);

        if (!$succeeded) {
            printf("Prepare statement error: " . $stmt);
            $stmt = null;
            exit(1);
        }

        $rows = $stmt->fetchAll();

        return [$rows, count($rows)];
    }

    public function count($query_string, $values = [])
    {
        $stmt = self::$instance->pdo->prepare($query_string);

        $succeeded = $stmt->execute($values);

        if (!$succeeded) {
            printf("Prepare statement error: " . $stmt);
            $stmt = null;
            exit(1);
        }

        return $stmt->fetchColumn();
    }
}
