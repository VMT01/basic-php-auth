<?php

namespace app\core\database;

use app\core\Application;
use Throwable;

class Database
{
    protected readonly \PDO $pdo;

    public function __construct(array $config)
    {
        $dsn = $config['dsn'] ?? '';
        $user = $config['username'] ?? '';
        $password = $config['password'] ?? '';

        try {
            $this->pdo = new \PDO($dsn, $user, $password);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (Throwable $error) {
            die($error);
        }
    }

    public function applyMigrations(): void
    {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();
        $toApplyMigrations = array_diff(
            array_filter(
                scandir(Application::$ROOT_PATH . '/migrations'),
                fn(string $file) => $file !== '.' && $file !== '..'
            ),
            $appliedMigrations
        );
        $newMigrations = [];

        foreach ($toApplyMigrations as $migration) {
            $SQL = file_get_contents($this->getSqlScript($migration, 'up'));
            $this->pdo->exec($SQL);
            echo "Applied migration $migration" . PHP_EOL;
            $newMigrations[] = $migration;
        }

        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        } else {
            echo "All migrations are applied" . PHP_EOL;
        }
    }

    public function revertMigration(): void
    {
        $statement = $this->pdo->query('SELECT id, migration FROM migrations ORDER BY id DESC LIMIT 1;');
        $latest = $statement->fetchAll();

        if (!$latest) {
            echo "No migration left" . PHP_EOL;
            return;
        } else {
            $latest = $latest[0];
        }

        $SQL_file = file_get_contents($this->getSqlScript($latest['migration'], 'down'));
        $SQL_migration = 'DELETE FROM migrations WHERE id = ' . $latest['id'] . ';';
        $SQL = $SQL_file . $SQL_migration;

        $this->pdo->exec($SQL);

        echo 'Reverted migration ' . $latest['migration'] . PHP_EOL;
    }

    public function exec(string $SQL, array|null $params = null)
    {
        $statement = $this->pdo->prepare($SQL);
        $statement->execute($params);

        if (str_starts_with($SQL, 'SELECT')) {
            return $statement->fetchAll();
        }
    }

    public function prepare(string $SQL)
    {
        return $this->pdo->prepare($SQL);
    }

    /** @param 'up'|'down' $direction*/
    private function getSqlScript(string $file, string $direction): string
    {
        return Application::$ROOT_PATH . '/migrations/' . $file . '/' . $direction . '.sql';
    }

    private function createMigrationsTable(): void
    {
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(225),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=INNODB;');
    }

    private function getAppliedMigrations(): array
    {
        $statement = $this->pdo->prepare('SELECT migration FROM migrations');
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    private function saveMigrations(array $migrations): void
    {
        $args = implode(
            ',',
            array_map(fn(string $migration) => "('$migration')", $migrations)
        );
        $SQL = 'INSERT INTO migrations (migration) VALUES ' . $args . ';';
        $this->pdo->exec($SQL);
    }
}
