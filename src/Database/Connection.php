<?php

namespace JarirAhmed\AuthMicroservice\Database;

class Connection
{
    private static ?Connection $instance = null;
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public static function getInstance(): static
    {
        if (!static::$instance) {
            throw new \RuntimeException('Database connection not initialised. Call Connection::setInstance() first.');
        }
        return static::$instance;
    }

    public static function setInstance(Connection $connection): void
    {
        static::$instance = $connection;
    }

    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    public function query(string $sql, array $bindings = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt;
    }

    public function select(string $sql, array $bindings = []): array
    {
        return $this->query($sql, $bindings)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function first(string $sql, array $bindings = []): ?array
    {
        $result = $this->select($sql, $bindings);
        return $result[0] ?? null;
    }

    public function insert(string $table, array $data): int
    {
        $cols        = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $this->query("INSERT INTO {$table} ({$cols}) VALUES ({$placeholders})", array_values($data));
        return (int) $this->pdo->lastInsertId();
    }

    public function update(string $table, array $data, array $where): int
    {
        $set   = implode(', ', array_map(fn($k) => "{$k} = ?", array_keys($data)));
        $cond  = implode(' AND ', array_map(fn($k) => "{$k} = ?", array_keys($where)));
        $stmt  = $this->query("UPDATE {$table} SET {$set} WHERE {$cond}", [...array_values($data), ...array_values($where)]);
        return $stmt->rowCount();
    }

    public function delete(string $table, array $where): int
    {
        $cond = implode(' AND ', array_map(fn($k) => "{$k} = ?", array_keys($where)));
        $stmt = $this->query("DELETE FROM {$table} WHERE {$cond}", array_values($where));
        return $stmt->rowCount();
    }

    public function table(string $table): QueryBuilder
    {
        return new QueryBuilder($this, $table);
    }
}
