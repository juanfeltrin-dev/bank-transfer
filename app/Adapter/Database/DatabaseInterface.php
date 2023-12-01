<?php

declare(strict_types=1);

namespace App\Adapter\Database;

interface DatabaseInterface
{
    public function selectOne(string $query, array $bindings = [], bool $useReadPdo = true);

    public function select(string $query, array $bindings = [], bool $useReadPdo = true): array;

    public function insert(string $query, array $bindings = []): bool;

    public function update(string $query, array $bindings = []): int;

    public function beginTransaction(): void;

    public function commit(): void;

    public function rollBack(): void;
}
