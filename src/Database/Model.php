<?php

namespace JarirAhmed\AuthMicroservice\Database;

abstract class Model
{
    protected static string $table;
    protected static string $primaryKey = 'id';
    protected array         $attributes  = [];
    protected array         $original    = [];
    protected bool          $exists      = false;

    protected static array $casts = [];

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    public function fill(array $attributes): static
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $this->castValue($key, $value);
        }
        return $this;
    }

    private function castValue(string $key, mixed $value): mixed
    {
        $cast = static::$casts[$key] ?? null;
        if ($value === null) return null;
        return match ($cast) {
            'int', 'integer'  => (int) $value,
            'bool', 'boolean' => (bool) $value,
            'array', 'json'   => is_string($value) ? json_decode($value, true) : $value,
            'datetime'        => is_string($value) ? new \DateTimeImmutable($value) : $value,
            default           => $value,
        };
    }

    private function serializeValue(string $key, mixed $value): mixed
    {
        $cast = static::$casts[$key] ?? null;
        if ($value === null) return null;
        return match ($cast) {
            'array', 'json'   => json_encode($value),
            'datetime'        => $value instanceof \DateTimeInterface ? $value->format('Y-m-d H:i:s') : $value,
            'bool', 'boolean' => $value ? 1 : 0,
            default           => $value,
        };
    }

    public function __get(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    public function __set(string $key, mixed $value): void
    {
        $this->attributes[$key] = $this->castValue($key, $value);
    }

    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    public function getKey(): mixed
    {
        return $this->attributes[static::$primaryKey] ?? null;
    }

    protected static function db(): Connection
    {
        return Connection::getInstance();
    }

    protected static function table(): string
    {
        return static::$table;
    }

    public static function find(mixed $id): ?static
    {
        $row = static::db()->first(
            'SELECT * FROM ' . static::table() . ' WHERE ' . static::$primaryKey . ' = ?',
            [$id]
        );
        return $row ? static::fromRow($row) : null;
    }

    public static function findOrFail(mixed $id): static
    {
        $model = static::find($id);
        if (!$model) throw new \RuntimeException(static::class . " #{$id} not found.");
        return $model;
    }

    public static function where(string $column, mixed $value): \JarirAhmed\AuthMicroservice\Database\QueryBuilder
    {
        return static::db()->table(static::table())->where($column, $value);
    }

    public static function create(array $attributes): static
    {
        $now   = date('Y-m-d H:i:s');
        $model = new static($attributes);
        $data  = $model->serializeAttributes();
        if (!isset($data['created_at'])) $data['created_at'] = $now;
        if (!isset($data['updated_at'])) $data['updated_at'] = $now;

        $id = static::db()->insert(static::table(), $data);
        $model->attributes[static::$primaryKey] = $id;
        $model->attributes['created_at'] = $data['created_at'];
        $model->attributes['updated_at'] = $data['updated_at'];
        $model->exists = true;
        return $model;
    }

    public static function firstOrCreate(array $attributes, array $values = []): static
    {
        $qb = static::db()->table(static::table());
        foreach ($attributes as $k => $v) $qb->where($k, $v);
        $row = $qb->first();
        if ($row) return static::fromRow($row);
        return static::create(array_merge($attributes, $values));
    }

    public function update(array $attributes): bool
    {
        $attributes['updated_at'] = date('Y-m-d H:i:s');
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $this->castValue($key, $value);
        }
        $serialized = [];
        foreach ($attributes as $key => $value) {
            $serialized[$key] = $this->serializeValue($key, $value);
        }
        static::db()->update(static::table(), $serialized, [static::$primaryKey => $this->getKey()]);
        return true;
    }

    public function delete(): bool
    {
        static::db()->delete(static::table(), [static::$primaryKey => $this->getKey()]);
        $this->exists = false;
        return true;
    }

    public function refresh(): static
    {
        $fresh = static::find($this->getKey());
        if ($fresh) $this->attributes = $fresh->attributes;
        return $this;
    }

    public function increment(string $column, int $amount = 1): void
    {
        static::db()->query(
            'UPDATE ' . static::table() . " SET {$column} = {$column} + ?, updated_at = ? WHERE " . static::$primaryKey . ' = ?',
            [$amount, date('Y-m-d H:i:s'), $this->getKey()]
        );
        $this->attributes[$column] = ($this->attributes[$column] ?? 0) + $amount;
    }

    public static function latest(string $column = 'created_at'): QueryBuilder
    {
        return static::db()->table(static::table())->orderBy($column, 'DESC');
    }

    public function toArray(): array
    {
        $result = [];
        foreach ($this->attributes as $key => $value) {
            $result[$key] = $value instanceof \DateTimeInterface ? $value->format('Y-m-d H:i:s') : $value;
        }
        return $result;
    }

    public function only(array $keys): array
    {
        return array_intersect_key($this->toArray(), array_flip($keys));
    }

    private function serializeAttributes(): array
    {
        $result = [];
        foreach ($this->attributes as $key => $value) {
            $result[$key] = $this->serializeValue($key, $value);
        }
        return $result;
    }

    protected static function fromRow(array $row): static
    {
        $model = new static($row);
        $model->exists = true;
        return $model;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
