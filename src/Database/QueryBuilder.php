<?php

namespace JarirAhmed\AuthMicroservice\Database;

class QueryBuilder
{
    private array  $wheres   = [];
    private array  $bindings = [];
    private ?int   $limitVal = null;
    private string $orderCol = 'id';
    private string $orderDir = 'ASC';
    private array  $columns  = ['*'];

    public function __construct(
        private Connection $connection,
        private string     $table
    ) {}

    public function select(array $columns): static
    {
        $this->columns = $columns;
        return $this;
    }

    public function where(string $column, mixed $value, string $operator = '='): static
    {
        $this->wheres[]   = "{$column} {$operator} ?";
        $this->bindings[] = $value;
        return $this;
    }

    public function whereNull(string $column): static
    {
        $this->wheres[] = "{$column} IS NULL";
        return $this;
    }

    public function whereNotNull(string $column): static
    {
        $this->wheres[] = "{$column} IS NOT NULL";
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): static
    {
        $this->orderCol = $column;
        $this->orderDir = strtoupper($direction);
        return $this;
    }

    public function latest(string $column = 'created_at'): static
    {
        return $this->orderBy($column, 'DESC');
    }

    public function limit(int $limit): static
    {
        $this->limitVal = $limit;
        return $this;
    }

    public function get(): array
    {
        $cols  = implode(', ', $this->columns);
        $sql   = "SELECT {$cols} FROM {$this->table}";
        if ($this->wheres) $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        $sql  .= " ORDER BY {$this->orderCol} {$this->orderDir}";
        if ($this->limitVal !== null) $sql .= " LIMIT {$this->limitVal}";
        return $this->connection->select($sql, $this->bindings);
    }

    public function first(): ?array
    {
        $this->limitVal = 1;
        $results = $this->get();
        return $results[0] ?? null;
    }

    public function insert(array $data): int
    {
        return $this->connection->insert($this->table, $data);
    }

    public function update(array $data): int
    {
        $set      = implode(', ', array_map(fn($k) => "{$k} = ?", array_keys($data)));
        $sql      = "UPDATE {$this->table} SET {$set}";
        $bindings = array_values($data);
        if ($this->wheres) {
            $sql      .= ' WHERE ' . implode(' AND ', $this->wheres);
            $bindings  = [...$bindings, ...$this->bindings];
        }
        return $this->connection->query($sql, $bindings)->rowCount();
    }

    public function delete(): int
    {
        $sql = "DELETE FROM {$this->table}";
        if ($this->wheres) $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        return $this->connection->query($sql, $this->bindings)->rowCount();
    }

    public function paginate(int $perPage = 20, int $page = 1): array
    {
        $offset = ($page - 1) * $perPage;
        $cols   = implode(', ', $this->columns);
        $sql    = "SELECT {$cols} FROM {$this->table}";
        if ($this->wheres) $sql .= ' WHERE ' . implode(' AND ', $this->wheres);
        $sql   .= " ORDER BY {$this->orderCol} {$this->orderDir} LIMIT {$perPage} OFFSET {$offset}";

        $countSql = "SELECT COUNT(*) as total FROM {$this->table}";
        if ($this->wheres) $countSql .= ' WHERE ' . implode(' AND ', $this->wheres);

        $items = $this->connection->select($sql, $this->bindings);
        $total = (int) ($this->connection->first($countSql, $this->bindings)['total'] ?? 0);

        return [
            'data'         => $items,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
        ];
    }
}
