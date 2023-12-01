<?php

declare(strict_types=1);

namespace App\Adapter\Database;

use Hyperf\DbConnection\Db;

class DatabaseAdapter implements DatabaseInterface
{
    public function __construct(private Db $connection)
    {
    }

    public function selectOne(string $query, array $bindings = [], bool $useReadPdo = true)
    {
        return $this->connection->selectOne($query, $bindings, $useReadPdo);
    }

    public function select(string $query, array $bindings = [], bool $useReadPdo = true): array
    {
        return $this->connection->select($query, $bindings, $useReadPdo);
    }

    public function insert(string $query, array $bindings = []): bool
    {
        return $this->connection->insert($query, $bindings);
    }

    public function update(string $query, array $bindings = []): int
    {
        return $this->connection->update($query, $bindings);
    }

    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    public function commit(): void
    {
        $this->connection->commit();
    }

    public function rollBack(): void
    {
        $this->connection->rollBack();
    }
}
